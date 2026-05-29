<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramLeadNotifier
{
    public function send(Lead $lead): bool
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram not configured: lead #{id} saved without notification.', ['id' => $lead->id]);

            return false;
        }

        $response = Http::timeout(10)->post(
            "https://api.telegram.org/bot{$token}/sendMessage",
            [
                'chat_id' => $chatId,
                'text' => $this->formatMessage($lead),
                'parse_mode' => 'HTML',
            ],
        );

        if (! $response->successful()) {
            Log::error('Telegram send failed for lead #{id}: {body}', [
                'id' => $lead->id,
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    private function formatMessage(Lead $lead): string
    {
        $lines = [
            '<b>Новая заявка #'.$lead->id.'</b>',
            '📅 '.$lead->created_at->timezone(config('app.timezone'))->format('d.m.Y H:i'),
            '👤 '.e($lead->name),
            '📞 '.e($lead->phone),
        ];

        if ($lead->ip) {
            $lines[] = '🌐 '.e($lead->ip);
        }

        $quiz = $this->formatQuizAnswers($lead->quiz_answers);
        if ($quiz !== '') {
            $lines[] = '';
            $lines[] = '<b>Ответы квиза:</b>';
            $lines[] = $quiz;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<string, mixed>|null  $answers
     */
    private function formatQuizAnswers(?array $answers): string
    {
        if (! $answers) {
            return '';
        }

        $parts = [];

        if (! empty($answers['device_label'])) {
            $parts[] = '• Устройство: '.e((string) $answers['device_label']);
        }

        if (! empty($answers['problems']) && is_array($answers['problems'])) {
            foreach ($answers['problems'] as $problem) {
                $parts[] = '• Проблема: '.e((string) $problem);
            }
        }

        if (! empty($answers['problem_custom'])) {
            $parts[] = '• Свой вариант: '.e((string) $answers['problem_custom']);
        }

        if (! empty($answers['brand'])) {
            $parts[] = '• Марка: '.e((string) $answers['brand']);
        }

        return implode("\n", $parts);
    }
}
