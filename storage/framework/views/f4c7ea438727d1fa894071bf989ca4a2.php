<?php
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->locale('es')->translatedFormat('F \\d\\e Y');
?>

<div class="month-picker-control" data-month-picker data-month-value="<?php echo e($month); ?>">
    <input id="<?php echo e($id); ?>" class="month-input" type="text" value="<?php echo e($monthLabel); ?>" readonly aria-label="Seleccionar mes" aria-haspopup="dialog" aria-expanded="false">
    <input type="hidden" name="month" value="<?php echo e($month); ?>" data-month-hidden>
    <div class="month-popover" data-month-popover hidden role="dialog" aria-label="Selector de mes">
        <div class="month-popover-header">
            <button type="button" class="month-year-button" data-month-prev aria-label="Año anterior">‹</button>
            <strong data-month-year><?php echo e(substr($month, 0, 4)); ?></strong>
            <button type="button" class="month-year-button" data-month-next aria-label="Año siguiente">›</button>
        </div>
        <div class="month-grid" role="grid">
            <?php $__currentLoopData = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $monthName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" class="month-option" data-month-index="<?php echo e($index); ?>" role="gridcell"><?php echo e($monthName); ?></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="month-popover-footer">
            <button type="button" class="month-footer-button" data-month-clear>Borrar</button>
            <button type="button" class="month-footer-button" data-month-today>Este mes</button>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\diego\Documents\Codex\2026-09-27\qu\resources\views\components\month-picker.blade.php ENDPATH**/ ?>