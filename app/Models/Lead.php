<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadTrafficChannel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory;
    public const STATUS_YES = 'yes';

    public const STATUS_NO = 'no';

    protected $fillable = [
        'conversion_id',
        'source',
        'quiz_answers',
        'name',
        'phone',
        'comment',
        'admin_note',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'gclid',
        'yclid',
        'ip',
        'form_url',
        'first_contact_url',
        'last_click',
        'referrer',
        'ym_client_id',
        'ga_client_id',
        'messenger',
        'telegram_sent',
        'qualification_status',
        'quality_status',
        'conversion_sent',
        'ga4_conversion_sent',
        'metrika_conversion_sent',
        'conversion_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'quiz_answers' => 'array',
            'conversion_sent' => 'boolean',
            'ga4_conversion_sent' => 'boolean',
            'metrika_conversion_sent' => 'boolean',
            'telegram_sent' => 'boolean',
            'conversion_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (! $lead->conversion_id) {
                $lead->conversion_id = 'FM-'.Str::upper(Str::random(8)).'-'.now()->format('Ymd');
            }
        });
    }

    public function sourceLabel(): string
    {
        $source = LeadSource::tryFrom($this->source);

        return $source?->label() ?? $this->source;
    }

    public function deviceLabel(): ?string
    {
        $answers = $this->quiz_answers;

        if (! is_array($answers)) {
            return null;
        }

        return $answers['device_label'] ?? $answers['device'] ?? null;
    }

    public function problemsText(): ?string
    {
        $answers = $this->quiz_answers;

        if (! is_array($answers)) {
            return null;
        }

        $parts = [];

        if (! empty($answers['problems']) && is_array($answers['problems'])) {
            $parts = array_merge($parts, $answers['problems']);
        }

        if (! empty($answers['problem_custom'])) {
            $parts[] = $answers['problem_custom'];
        }

        return $parts !== [] ? implode(', ', $parts) : null;
    }

    public function brandLabel(): ?string
    {
        $answers = $this->quiz_answers;

        if (! is_array($answers)) {
            return null;
        }

        return $answers['brand'] ?? null;
    }

    public function utmSummary(): ?string
    {
        $parts = array_filter([
            $this->utm_source ? 'source='.$this->utm_source : null,
            $this->utm_medium ? 'medium='.$this->utm_medium : null,
            $this->utm_campaign ? 'campaign='.$this->utm_campaign : null,
            $this->utm_content ? 'content='.$this->utm_content : null,
            $this->utm_term ? 'term='.$this->utm_term : null,
        ]);

        return $parts !== [] ? implode(', ', $parts) : null;
    }

    public function quizPathText(): string
    {
        $answers = $this->quiz_answers;

        if (! is_array($answers)) {
            return '—';
        }

        $lines = [];

        if (! empty($answers['device_label']) || ! empty($answers['device'])) {
            $lines[] = 'Устройство: '.($answers['device_label'] ?? $answers['device']);
        }

        if (! empty($answers['problems']) && is_array($answers['problems'])) {
            foreach ($answers['problems'] as $problem) {
                $lines[] = 'Проблема: '.$problem;
            }
        }

        if (! empty($answers['problem_custom'])) {
            $lines[] = 'Свой вариант: '.$answers['problem_custom'];
        }

        if (! empty($answers['brand'])) {
            $lines[] = 'Марка: '.$answers['brand'];
        }

        return $lines !== [] ? implode("\n", $lines) : '—';
    }

    public function statusLabel(?string $status): string
    {
        return match ($status) {
            self::STATUS_YES => 'Да',
            self::STATUS_NO => 'Нет',
            default => '—',
        };
    }

    public function statusCssClass(?string $status): string
    {
        return match ($status) {
            self::STATUS_YES => 'status-yes',
            self::STATUS_NO => 'status-no',
            default => 'status-empty',
        };
    }

    public function trafficChannel(): LeadTrafficChannel
    {
        if ($this->matchesGoogleAds()) {
            return LeadTrafficChannel::GoogleAds;
        }

        if ($this->matchesYandexAds()) {
            return LeadTrafficChannel::YandexAds;
        }

        if ($this->matchesSocial()) {
            return LeadTrafficChannel::Social;
        }

        return LeadTrafficChannel::Other;
    }

    public function conversionTarget(): string
    {
        return $this->trafficChannel()->conversionTarget();
    }

    public function trafficChannelLabel(): string
    {
        return $this->trafficChannel()->label();
    }

    public function canSendGa4Conversion(): bool
    {
        return ! $this->ga4_conversion_sent;
    }

    public function canSendMetrikaConversion(): bool
    {
        return ! $this->metrika_conversion_sent;
    }

    public function shouldSendGa4ForChannel(): bool
    {
        return $this->conversionTarget() !== 'metrika';
    }

    public function shouldSendMetrikaForChannel(): bool
    {
        return true;
    }

    public function canSendConversion(): bool
    {
        if ($this->quality_status !== self::STATUS_YES) {
            return false;
        }

        $needsGa4 = $this->shouldSendGa4ForChannel() && $this->canSendGa4Conversion();
        $needsMetrika = $this->shouldSendMetrikaForChannel() && $this->canSendMetrikaConversion();

        return $needsGa4 || $needsMetrika;
    }

    private function matchesGoogleAds(): bool
    {
        if ($this->gclid) {
            return true;
        }

        $source = strtolower(trim((string) $this->utm_source));
        $medium = strtolower(trim((string) $this->utm_medium));
        $paidMediums = ['cpc', 'ppc', 'paid', 'cpm', 'display'];

        if (in_array($source, ['googleads', 'adwords'], true)) {
            return $medium === '' || in_array($medium, $paidMediums, true);
        }

        return (in_array($source, ['google', 'goog'], true) || str_contains($source, 'google'))
            && in_array($medium, $paidMediums, true);
    }

    private function matchesYandexAds(): bool
    {
        if ($this->yclid) {
            return true;
        }

        $source = strtolower(trim((string) $this->utm_source));
        $medium = strtolower(trim((string) $this->utm_medium));
        $paidMediums = ['cpc', 'ppc', 'paid', 'cpm', 'dm'];

        if ($source === 'yandex_direct') {
            return true;
        }

        return (in_array($source, ['yandex', 'ya', 'yandex.ru'], true) || str_contains($source, 'yandex'))
            && in_array($medium, $paidMediums, true);
    }

    private function matchesSocial(): bool
    {
        $source = strtolower(trim((string) $this->utm_source));
        $medium = strtolower(trim((string) $this->utm_medium));

        if (in_array($medium, ['social', 'social-network', 'sns', 'sm'], true)) {
            return true;
        }

        if ($source === '') {
            return false;
        }

        $socialSources = [
            'vk', 'vkontakte', 'facebook', 'fb', 'instagram', 'ig', 'telegram', 'tg',
            'ok', 'odnoklassniki', 'tiktok', 'twitter', 'x', 'linkedin', 'youtube',
            'viber', 'whatsapp', 'pinterest',
        ];

        foreach ($socialSources as $socialSource) {
            if ($source === $socialSource || str_starts_with($source, $socialSource.'.')) {
                return true;
            }
        }

        return false;
    }

    public function conversionStatusSummary(): string
    {
        $parts = [];

        if ($this->ga4_conversion_sent) {
            $parts[] = 'GA4: отправлено';
        }

        if ($this->metrika_conversion_sent) {
            $parts[] = 'Метрика: отправлено';
        }

        if ($parts === []) {
            return 'Конверсия не отправлялась';
        }

        $summary = implode('; ', $parts);

        if ($this->conversion_sent_at) {
            $summary .= ' ('.$this->conversion_sent_at->timezone(config('app.timezone'))->format('d.m.Y H:i').')';
        }

        return $summary;
    }

    /**
     * @return array<string, mixed>
     */
    public static function adminFiltersFromRequest(Request $request, bool $withSort = true): array
    {
        $keys = [
            'period',
            'date_from',
            'date_to',
            'source',
            'qualification',
            'quality',
        ];

        if ($withSort) {
            $keys[] = 'sort';
            $keys[] = 'direction';
        }

        return $request->only($keys);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function scopeFiltered(Builder $query, array $filters, bool $withSort = true): Builder
    {
        $period = $filters['period'] ?? 'all';
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        $timezone = config('app.timezone');

        // Если выбран «Все», но заполнены даты — применяем диапазон.
        if ($period === 'all' && ($dateFrom || $dateTo)) {
            $period = 'custom';
        }

        if ($period === 'week') {
            $query->where('created_at', '>=', now($timezone)->subWeek()->startOfDay());
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now($timezone)->subMonth()->startOfDay());
        } elseif ($period === 'custom') {
            if ($dateFrom) {
                $query->where('created_at', '>=', Carbon::parse($dateFrom, $timezone)->startOfDay());
            }
            if ($dateTo) {
                $query->where('created_at', '<=', Carbon::parse($dateTo, $timezone)->endOfDay());
            }
        } elseif ($period === 'day') {
            $query->whereBetween('created_at', [
                now($timezone)->startOfDay(),
                now($timezone)->endOfDay(),
            ]);
        } elseif ($period === 'date' && $dateFrom) {
            $start = Carbon::parse($dateFrom, $timezone)->startOfDay();
            $query->whereBetween('created_at', [$start, $start->copy()->endOfDay()]);
        }

        if (! empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (array_key_exists('qualification', $filters) && $filters['qualification'] !== '' && $filters['qualification'] !== null) {
            if ($filters['qualification'] === 'empty') {
                $query->whereNull('qualification_status');
            } else {
                $query->where('qualification_status', $filters['qualification']);
            }
        }

        if (array_key_exists('quality', $filters) && $filters['quality'] !== '' && $filters['quality'] !== null) {
            if ($filters['quality'] === 'empty') {
                $query->whereNull('quality_status');
            } else {
                $query->where('quality_status', $filters['quality']);
            }
        }

        if ($withSort) {
            $sort = $filters['sort'] ?? 'created_at';
            $direction = $filters['direction'] ?? 'desc';

            $direction = $direction === 'asc' ? 'asc' : 'desc';

            if (in_array($sort, ['id', 'created_at', 'name', 'source'], true)) {
                $query->orderBy($sort, $direction);

                if ($sort === 'created_at') {
                    $query->orderBy('id', $direction);
                }
            } else {
                $query->orderByDesc('created_at')->orderByDesc('id');
            }
        }

        return $query;
    }
}
