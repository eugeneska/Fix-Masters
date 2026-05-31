<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyticsConversionService
{
    /**
     * @return array{ga4: bool, metrika: bool, errors: list<string>}
     */
    public function send(Lead $lead, string $target): array
    {
        $result = [
            'ga4' => false,
            'metrika' => false,
            'errors' => [],
        ];

        $sendGa4 = in_array($target, ['both', 'ga4'], true);
        $sendMetrika = in_array($target, ['both', 'metrika'], true);

        if ($sendGa4) {
            if ($lead->ga4_conversion_sent) {
                $result['errors'][] = 'Конверсия в GA4 уже была отправлена.';
            } else {
                $ga4 = $this->sendGa4($lead);
                if ($ga4['ok']) {
                    $result['ga4'] = true;
                } elseif ($ga4['error']) {
                    $result['errors'][] = $ga4['error'];
                }
            }
        }

        if ($sendMetrika) {
            if ($lead->metrika_conversion_sent) {
                $result['errors'][] = 'Конверсия в Яндекс Метрику уже была отправлена.';
            } else {
                $metrika = $this->sendMetrika($lead);
                if ($metrika['ok']) {
                    $result['metrika'] = true;
                } elseif ($metrika['error']) {
                    $result['errors'][] = $metrika['error'];
                }
            }
        }

        return $result;
    }

    /**
     * @return array{ok: bool, error: string|null}
     */
    private function sendGa4(Lead $lead): array
    {
        $measurementId = config('analytics.ga4_measurement_id');
        $apiSecret = config('analytics.ga4_api_secret');

        if (! $measurementId || ! $apiSecret) {
            return ['ok' => false, 'error' => 'GA4 не настроен (GA4_MEASUREMENT_ID, GA4_API_SECRET).'];
        }

        $clientId = $lead->ga_client_id ?: 'lead_'.$lead->id;
        $eventName = config('analytics.ga4_conversion_event', 'quality_lead');

        $url = 'https://www.google-analytics.com/mp/collect?'.http_build_query([
            'measurement_id' => $measurementId,
            'api_secret' => $apiSecret,
        ]);

        $response = Http::timeout(15)
            ->asJson()
            ->post($url, [
                'client_id' => $clientId,
                'events' => [
                    [
                        'name' => $eventName,
                        'params' => [
                            'transaction_id' => $lead->conversion_id,
                            'lead_id' => $lead->id,
                        ],
                    ],
                ],
            ]);

        if (! $response->successful() && $response->status() !== 204) {
            Log::error('GA4 conversion failed', [
                'lead_id' => $lead->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['ok' => false, 'error' => 'Ошибка отправки в GA4 (HTTP '.$response->status().').'];
        }

        return ['ok' => true, 'error' => null];
    }

    /**
     * Офлайн-конверсия через Management API (OAuth).
     *
     * @see https://yandex.com/dev/metrika/en/management/offline-conv
     *
     * @return array{ok: bool, error: string|null}
     */
    private function sendMetrika(Lead $lead): array
    {
        $counterId = config('analytics.yandex_metrika_id');
        $token = config('analytics.yandex_metrika_oauth_token');
        $goal = config('analytics.yandex_metrika_goal', 'quality_lead');

        if (! $counterId || ! $token) {
            return ['ok' => false, 'error' => 'Яндекс Метрика не настроена (YANDEX_METRIKA_ID, YANDEX_METRIKA_OAUTH_TOKEN).'];
        }

        $csv = $this->buildMetrikaOfflineCsv($lead, $goal);

        if ($csv === null) {
            return [
                'ok' => false,
                'error' => 'Нет идентификатора для Метрики (ClientId, Yclid или UserId). Сохраните ym_client_id при заявке.',
            ];
        }

        $url = "https://api-metrika.yandex.net/management/v1/counter/{$counterId}/offline_conversions/upload";

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'OAuth '.$token,
            ])
            ->attach('file', $csv, 'offline-conversions.csv')
            ->post($url);

        if (! $response->successful()) {
            Log::error('Metrika offline conversion failed', [
                'lead_id' => $lead->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $message = 'Ошибка отправки в Яндекс Метрику (HTTP '.$response->status().').';
            $body = $response->json();
            if (is_array($body) && isset($body['message'])) {
                $message .= ' '.$body['message'];
            }

            return ['ok' => false, 'error' => $message];
        }

        return ['ok' => true, 'error' => null];
    }

    /**
     * CSV: Target, DateTime и хотя бы один ID (ClientId / Yclid / UserId).
     */
    private function buildMetrikaOfflineCsv(Lead $lead, string $goal): ?string
    {
        $dateTime = (int) $lead->created_at->timestamp;

        if ($dateTime > time()) {
            $dateTime = time() - 60;
        }

        $header = ['Target', 'DateTime'];
        $row = [$goal, (string) $dateTime];

        if ($lead->ym_client_id) {
            $header[] = 'ClientId';
            $row[] = $lead->ym_client_id;
        } elseif ($lead->yclid) {
            $header[] = 'Yclid';
            $row[] = $lead->yclid;
        } else {
            $header[] = 'UserId';
            $row[] = 'lead_'.$lead->id;
        }

        $lines = [
            implode(',', $header),
            implode(',', array_map([$this, 'escapeCsvField'], $row)),
        ];

        return implode("\n", $lines)."\n";
    }

    private function escapeCsvField(string $value): string
    {
        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
