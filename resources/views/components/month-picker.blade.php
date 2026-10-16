@php
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('es')->translatedFormat('F \\d\\e Y');
@endphp

<div class="month-picker-control" data-month-picker data-month-value="{{ $month }}">
    <input id="{{ $id }}" class="month-input" type="text" value="{{ $monthLabel }}" readonly aria-label="Seleccionar mes" aria-haspopup="dialog" aria-expanded="false">
    <input type="hidden" name="month" value="{{ $month }}" data-month-hidden>
    <div class="month-popover" data-month-popover hidden role="dialog" aria-label="Selector de mes">
        <div class="month-popover-header">
            <button type="button" class="month-year-button" data-month-prev aria-label="Año anterior">‹</button>
            <strong data-month-year>{{ substr($month, 0, 4) }}</strong>
            <button type="button" class="month-year-button" data-month-next aria-label="Año siguiente">›</button>
        </div>
        <div class="month-grid" role="grid">
            @foreach (['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'] as $index => $monthName)
                <button type="button" class="month-option" data-month-index="{{ $index }}" role="gridcell">{{ $monthName }}</button>
            @endforeach
        </div>
        <div class="month-popover-footer">
            <button type="button" class="month-footer-button" data-month-clear>Borrar</button>
            <button type="button" class="month-footer-button" data-month-today>Este mes</button>
        </div>
    </div>
</div>
