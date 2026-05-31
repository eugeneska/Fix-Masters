@php
  $field = $field ?? 'quality_status';
  $value = $lead->{$field};
  $isQuality = $field === 'quality_status';
@endphp
<div class="admin-status-cell" data-lead-id="{{ $lead->id }}" data-field="{{ $field }}">
  <span class="admin-status-badge {{ $lead->statusCssClass($value) }}" data-status-badge>{{ $lead->statusLabel($value) }}</span>
  <div class="admin-segmented" role="group" aria-label="{{ $isQuality ? 'Качество лида' : 'Квалификация лида' }}">
    <button type="button" class="admin-segmented__btn @if($value === null) is-active @endif" data-status="" title="Не задано">—</button>
    <button type="button" class="admin-segmented__btn @if($value === 'yes') is-active @endif" data-status="yes">Да</button>
    <button type="button" class="admin-segmented__btn @if($value === 'no') is-active @endif" data-status="no">Нет</button>
  </div>
</div>
