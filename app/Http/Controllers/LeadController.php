<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Services\TelegramLeadNotifier;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function store(StoreLeadRequest $request, TelegramLeadNotifier $notifier): JsonResponse
    {
        $validated = $request->validated();

        $lead = Lead::query()->create([
            'source' => $validated['source'],
            'quiz_answers' => $validated['quiz_answers'] ?? null,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'comment' => $validated['comment'] ?? null,
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'utm_content' => $validated['utm_content'] ?? null,
            'utm_term' => $validated['utm_term'] ?? null,
            'gclid' => $validated['gclid'] ?? null,
            'yclid' => $validated['yclid'] ?? null,
            'ip' => $request->ip(),
        ]);

        $notifier->send($lead);

        return response()->json([
            'ok' => true,
            'id' => $lead->id,
            'redirect' => route('thanks'),
        ]);
    }
}
