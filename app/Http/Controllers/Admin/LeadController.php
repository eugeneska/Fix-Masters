<?php

namespace App\Http\Controllers\Admin;

use App\Enums\LeadSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendConversionRequest;
use App\Http\Requests\Admin\UpdateLeadRequest;
use App\Models\Lead;
use App\Services\AnalyticsConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only([
            'period',
            'date_from',
            'date_to',
            'source',
            'qualification',
            'quality',
            'sort',
            'direction',
        ]);

        $perPage = (int) $request->input('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $leads = Lead::query()
            ->filtered($filters)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'filters' => $filters,
            'perPage' => $perPage,
            'sources' => LeadSource::cases(),
        ]);
    }

    public function show(Lead $lead): View
    {
        return view('admin.leads.show', [
            'lead' => $lead,
        ]);
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()
            ->back(fallback: route('admin.leads.index'))
            ->with('status', 'Заявка удалена');
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead,
        AnalyticsConversionService $conversionService,
    ): JsonResponse {
        $data = $request->validated();
        $previousQualityStatus = $lead->quality_status;

        foreach (['qualification_status', 'quality_status'] as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                $lead->{$field} = $value === '' ? null : $value;
            }
        }

        if (array_key_exists('admin_note', $data)) {
            $lead->admin_note = $data['admin_note'];
        }

        $lead->save();

        $conversionResult = null;

        if (
            $previousQualityStatus !== Lead::STATUS_YES
            && $lead->quality_status === Lead::STATUS_YES
        ) {
            $conversionResult = $this->trySendQualityConversion($lead, $conversionService);
            $lead->refresh();
        }

        $response = [
            'ok' => true,
            'lead' => $this->leadPayload($lead),
        ];

        if ($conversionResult !== null) {
            $response['conversion'] = [
                'ga4' => $conversionResult['ga4'],
                'metrika' => $conversionResult['metrika'],
                'errors' => $conversionResult['errors'],
                'target' => $conversionResult['target'],
            ];
        }

        return response()->json($response);
    }

    public function sendConversion(
        SendConversionRequest $request,
        Lead $lead,
        AnalyticsConversionService $conversionService,
    ): JsonResponse {
        if ($lead->quality_status !== Lead::STATUS_YES) {
            return response()->json([
                'ok' => false,
                'message' => 'Конверсию можно отправить только для качественного лида (Да).',
            ], 422);
        }

        $target = $this->resolveConversionTarget($lead, $request->validated('target'));

        if ($target === 'ga4' && ! $lead->canSendGa4Conversion()) {
            return response()->json([
                'ok' => false,
                'message' => 'Конверсия в GA4 уже была отправлена.',
            ], 422);
        }

        if ($target === 'metrika' && ! $lead->canSendMetrikaConversion()) {
            return response()->json([
                'ok' => false,
                'message' => 'Конверсия в Яндекс Метрику уже была отправлена.',
            ], 422);
        }

        if ($target === 'both' && ! $lead->canSendGa4Conversion() && ! $lead->canSendMetrikaConversion()) {
            return response()->json([
                'ok' => false,
                'message' => 'Конверсии уже отправлены во все выбранные системы.',
            ], 422);
        }

        $result = $this->processConversionSend($lead, $conversionService, $target);

        return $this->conversionResponse($lead->fresh(), $result, $target);
    }

    /**
     * @return array{ga4: bool, metrika: bool, errors: list<string>, target: string}|null
     */
    private function trySendQualityConversion(Lead $lead, AnalyticsConversionService $conversionService): ?array
    {
        if (! $lead->canAutoSendConversion()) {
            return null;
        }

        $target = $this->effectiveConversionTarget($lead);

        if ($target === null) {
            return null;
        }

        $result = $this->processConversionSend($lead, $conversionService, $target);

        return array_merge($result, ['target' => $target]);
    }

    /**
     * @return array{ga4: bool, metrika: bool, errors: list<string>}
     */
    private function processConversionSend(
        Lead $lead,
        AnalyticsConversionService $conversionService,
        string $target,
    ): array {
        $result = $conversionService->send($lead, $target);

        $updates = [];

        if ($result['ga4']) {
            $updates['ga4_conversion_sent'] = true;
        }

        if ($result['metrika']) {
            $updates['metrika_conversion_sent'] = true;
        }

        if ($updates !== []) {
            $lead->fill($updates);
            $lead->conversion_sent = $lead->ga4_conversion_sent || $lead->metrika_conversion_sent;
            $lead->conversion_sent_at = now();
            $lead->save();
        }

        return $result;
    }

    private function resolveConversionTarget(Lead $lead, string $requested): string
    {
        if ($requested === 'auto') {
            return $this->effectiveConversionTarget($lead) ?? $lead->conversionTarget();
        }

        $channelTarget = $lead->conversionTarget();

        if ($channelTarget === 'metrika') {
            return 'metrika';
        }

        return $requested;
    }

    private function effectiveConversionTarget(Lead $lead): ?string
    {
        $channelTarget = $lead->conversionTarget();

        if ($channelTarget === 'metrika') {
            return $lead->canSendMetrikaConversion() ? 'metrika' : null;
        }

        $canGa4 = $lead->shouldSendGa4ForChannel() && $lead->canSendGa4Conversion();
        $canMetrika = $lead->shouldSendMetrikaForChannel() && $lead->canSendMetrikaConversion();

        if ($canGa4 && $canMetrika) {
            return 'both';
        }

        if ($canGa4) {
            return 'ga4';
        }

        if ($canMetrika) {
            return 'metrika';
        }

        return null;
    }

    private function conversionResponse(Lead $lead, array $result, string $target): JsonResponse
    {
        if (($result['ga4'] || $result['metrika']) && $result['errors'] === []) {
            return response()->json([
                'ok' => true,
                'lead' => $this->leadPayload($lead),
                'conversion' => [
                    'ga4' => $result['ga4'],
                    'metrika' => $result['metrika'],
                    'errors' => [],
                    'target' => $target,
                ],
            ]);
        }

        if ($result['ga4'] || $result['metrika']) {
            return response()->json([
                'ok' => true,
                'partial' => true,
                'errors' => $result['errors'],
                'lead' => $this->leadPayload($lead),
                'conversion' => [
                    'ga4' => $result['ga4'],
                    'metrika' => $result['metrika'],
                    'errors' => $result['errors'],
                    'target' => $target,
                ],
            ]);
        }

        return response()->json([
            'ok' => false,
            'message' => implode(' ', $result['errors']) ?: 'Не удалось отправить конверсию.',
        ], 422);
    }

    /**
     * @return array<string, mixed>
     */
    private function leadPayload(Lead $lead): array
    {
        return [
            'id' => $lead->id,
            'qualification_status' => $lead->qualification_status,
            'quality_status' => $lead->quality_status,
            'admin_note' => $lead->admin_note,
            'ga4_conversion_sent' => $lead->ga4_conversion_sent,
            'metrika_conversion_sent' => $lead->metrika_conversion_sent,
            'conversion_sent' => $lead->conversion_sent,
            'traffic_channel' => $lead->trafficChannel()->value,
            'traffic_channel_label' => $lead->trafficChannelLabel(),
            'conversion_target' => $lead->conversionTarget(),
        ];
    }
}
