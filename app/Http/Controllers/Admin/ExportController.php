<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function csv(Request $request): StreamedResponse
    {
        $filters = $request->only([
            'period',
            'date_from',
            'date_to',
            'source',
            'qualification',
            'quality',
        ]);

        $filename = 'leads-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($filters) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Дата',
                'Имя',
                'Телефон',
                'Источник',
                'Устройство',
                'Проблема',
                'Бренд',
                'Квалификация',
                'Качество',
                'IP',
                'UTM',
                'gclid',
                'yclid',
                'Комментарий',
                'GA4 отправлено',
                'Метрика отправлено',
            ], ';');

            Lead::query()
                ->filtered($filters)
                ->orderByDesc('id')
                ->chunk(200, function ($leads) use ($handle) {
                    foreach ($leads as $lead) {
                        fputcsv($handle, [
                            $lead->id,
                            $lead->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                            $lead->name,
                            $lead->phone,
                            $lead->sourceLabel(),
                            $lead->deviceLabel() ?? '',
                            $lead->problemsText() ?? '',
                            $lead->brandLabel() ?? '',
                            $lead->statusLabel($lead->qualification_status),
                            $lead->statusLabel($lead->quality_status),
                            $lead->ip ?? '',
                            $lead->utmSummary() ?? '',
                            $lead->gclid ?? '',
                            $lead->yclid ?? '',
                            $lead->comment ?? '',
                            $lead->ga4_conversion_sent ? 'Да' : 'Нет',
                            $lead->metrika_conversion_sent ? 'Да' : 'Нет',
                        ], ';');
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
