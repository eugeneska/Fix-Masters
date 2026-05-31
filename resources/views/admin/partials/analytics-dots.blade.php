<div class="admin-analytics-dots" data-lead-id="{{ $lead->id }}">
  <div class="admin-analytics-dots__item">
    <span>Метрика</span>
    <span class="admin-dot @if($lead->metrika_conversion_sent) is-sent @endif" data-metrika-dot title="@if($lead->metrika_conversion_sent) Отправлено @else Не отправлено @endif"></span>
  </div>
  <div class="admin-analytics-dots__item">
    <span>GA4</span>
    <span class="admin-dot @if($lead->ga4_conversion_sent) is-sent @endif" data-ga4-dot title="@if($lead->ga4_conversion_sent) Отправлено @else Не отправлено @endif"></span>
  </div>
</div>
