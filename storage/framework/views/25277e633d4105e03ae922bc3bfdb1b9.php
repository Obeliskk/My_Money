<?php
    $sectionTitles = [
        'weekly' => 'Resumen semanal',
        'categories' => 'Nuevo movimiento',
        'debts' => 'Deudas y cobros',
        'goal' => 'Fijar meta',
        'history' => 'Historial de gastos del mes',
    ];
    $sectionTitle = $sectionTitles[$section] ?? $sectionTitles['weekly'];
    $movementDateIso = (string) old('movement_date', $editingMovement?->movement_date?->format('Y-m-d') ?? now()->format('Y-m-d'));
    $movementDateDisplay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $movementDateIso)
        ? \Carbon\Carbon::createFromFormat('Y-m-d', $movementDateIso)->format('d/m/Y')
        : $movementDateIso;
    $debtDueDateIso = (string) old('due_date', $editingDebt?->due_date?->format('Y-m-d') ?? '');
    $debtDueDateDisplay = preg_match('/^\d{4}-\d{2}-\d{2}$/', $debtDueDateIso)
        ? \Carbon\Carbon::createFromFormat('Y-m-d', $debtDueDateIso)->format('d/m/Y')
        : $debtDueDateIso;
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => $sectionTitle . ' | GastosLocal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sectionTitle . ' | GastosLocal')]); ?>
    <section class="intro">
        <div>
            <p class="eyebrow">CONTROL PERSONAL</p>
            <h2><?php echo e($sectionTitle); ?></h2>
            <p class="muted">Organiza tus finanzas con información clara y accionable.</p>
        </div>
        <?php if($section === 'history' || $section === 'goal'): ?>
            <form method="GET" class="month-picker">
                <input type="hidden" name="section" value="<?php echo e($section); ?>">
                <label for="month">Mes</label>
                <?php echo $__env->make('components.month-picker', ['id' => 'month', 'month' => $month], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </form>
        <?php elseif($section === 'weekly' && $summaryPeriod === 'week'): ?>
            <div class="intro-actions">
                <section class="summary-switch" aria-label="Periodo de KPI">
                    <a class="<?php echo e($summaryPeriod === 'week' ? 'active' : ''); ?>" href="<?php echo e(route('dashboard', ['section' => 'weekly', 'period' => 'week', 'week' => $weekStart->format('Y-m-d'), 'month' => $month])); ?>">Semana</a>
                    <a class="<?php echo e($summaryPeriod === 'month' ? 'active' : ''); ?>" href="<?php echo e(route('dashboard', ['section' => 'weekly', 'period' => 'month', 'month' => $month, 'week' => $weekStart->format('Y-m-d')])); ?>">Mes</a>
                </section>
                <form method="GET" class="month-picker">
                    <input type="hidden" name="section" value="weekly">
                    <input type="hidden" name="period" value="week">
                    <label for="week">Semana desde</label>
                    <span class="date-control"><input id="week" class="date-display" type="text" inputmode="numeric" data-date-display data-submit-form placeholder="dd/mm/aaaa" value="<?php echo e($weekStart->format('d/m/Y')); ?>" aria-label="Semana desde, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="<?php echo e($weekStart->format('Y-m-d')); ?>" tabindex="-1" aria-hidden="true"><input type="hidden" name="week" data-date-value value="<?php echo e($weekStart->format('Y-m-d')); ?>"></span>
                </form>
            </div>
        <?php elseif($section === 'weekly'): ?>
            <div class="intro-actions">
                <section class="summary-switch" aria-label="Periodo de KPI">
                    <a class="<?php echo e($summaryPeriod === 'week' ? 'active' : ''); ?>" href="<?php echo e(route('dashboard', ['section' => 'weekly', 'period' => 'week', 'week' => $weekStart->format('Y-m-d'), 'month' => $month])); ?>">Semana</a>
                    <a class="<?php echo e($summaryPeriod === 'month' ? 'active' : ''); ?>" href="<?php echo e(route('dashboard', ['section' => 'weekly', 'period' => 'month', 'month' => $month, 'week' => $weekStart->format('Y-m-d')])); ?>">Mes</a>
                </section>
                <form method="GET" class="month-picker">
                    <input type="hidden" name="section" value="weekly">
                    <input type="hidden" name="period" value="month">
                    <label for="summary-month">Mes</label>
                    <?php echo $__env->make('components.month-picker', ['id' => 'summary-month', 'month' => $month], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </form>
            </div>
        <?php endif; ?>
    </section>

    <nav class="module-nav" aria-label="Módulos de GastosLocal">
        <?php $__currentLoopData = $sectionTitles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a class="<?php echo e($section === $key ? 'active' : ''); ?>" href="<?php echo e(route('dashboard', ['section' => $key, 'month' => $month, 'week' => $weekStart->format('Y-m-d')])); ?>"><?php echo e($title); ?></a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <?php if($section === 'weekly'): ?>
        <section class="kpi-grid" aria-label="Indicadores del periodo seleccionado">
            <article class="kpi-card"><span>Ingresos del <?php echo e($summaryPeriod === 'week' ? 'periodo' : 'mes'); ?></span><strong>&dollar;<?php echo e(number_format($summaryIncome, 0, ',', '.')); ?></strong><small><?php echo e($summaryStart->format('d/m')); ?> al <?php echo e($summaryEnd->format('d/m/Y')); ?></small></article>
            <article class="kpi-card"><span>Gastos del <?php echo e($summaryPeriod === 'week' ? 'periodo' : 'mes'); ?></span><strong>&dollar;<?php echo e(number_format($summaryExpenses, 0, ',', '.')); ?></strong><small>Consumo registrado</small></article>
            <article class="kpi-card accent"><div class="kpi-card-heading"><span>Disponible</span><details class="balance-editor"><summary class="kpi-edit">Editar</summary><div class="balance-popover"><form method="POST" action="<?php echo e(route('balance.store')); ?>"><?php echo csrf_field(); ?><input type="hidden" name="period_type" value="<?php echo e($summaryBalancePeriodType); ?>"><input type="hidden" name="period_key" value="<?php echo e($summaryBalancePeriodKey); ?>"><input type="hidden" name="month" value="<?php echo e($month); ?>"><input type="hidden" name="week" value="<?php echo e($weekStart->format('Y-m-d')); ?>"><label>Saldo disponible<input name="amount" type="number" min="0" step="0.01" value="<?php echo e(number_format((float) ($summaryBalance?->amount ?? $summarySavings), 2, '.', '')); ?>" required></label><p class="balance-help">Este valor se actualizará con tus nuevos movimientos.</p><div class="form-actions"><button class="button primary" type="submit">Guardar</button></div></form><?php if($summaryBalanceOverridden): ?><form method="POST" action="<?php echo e(route('balance.destroy')); ?>" class="balance-reset-form"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><input type="hidden" name="period_type" value="<?php echo e($summaryBalancePeriodType); ?>"><input type="hidden" name="period_key" value="<?php echo e($summaryBalancePeriodKey); ?>"><input type="hidden" name="month" value="<?php echo e($month); ?>"><input type="hidden" name="week" value="<?php echo e($weekStart->format('Y-m-d')); ?>"><button class="balance-reset" type="submit">Restablecer cálculo</button></form><?php endif; ?></div></details></div><strong>&dollar;<?php echo e(number_format($summarySavings, 0, ',', '.')); ?></strong><small><?php echo e($summarySavingRate); ?>% de tus ingresos del mes<?php echo e($summaryBalanceOverridden ? ' · Base ajustada' : ''); ?></small></article>
            <article class="kpi-card"><span>Movimientos</span><strong><?php echo e($summaryMovements->count()); ?></strong><small>En el periodo seleccionado</small></article>
        </section>

        <section class="content-grid">
            <article class="panel">
                <div class="panel-heading"><div><p class="eyebrow">DISTRIBUCIÓN</p><h3>Gastos por etiqueta</h3></div></div>
                <?php $__empty_1 = true; $__currentLoopData = $summaryByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($percentage = $summaryExpenses > 0 ? ($amount / $summaryExpenses) * 100 : 0); ?>
                    <div class="category-row"><div class="category-label"><span><?php echo e($category); ?></span><strong>&dollar;<?php echo e(number_format($amount, 0, ',', '.')); ?></strong></div><div class="bar"><i style="width: <?php echo e($percentage); ?>%"></i></div></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="empty">Todavía no hay gastos en el periodo seleccionado.</p>
                <?php endif; ?>
            </article>
            <article class="panel">
                <div class="panel-heading"><div><p class="eyebrow">MOVIMIENTOS</p><h3>Actividad reciente</h3></div></div>
                <?php if($summaryMovements->isEmpty()): ?>
                    <p class="empty">Agrega movimientos desde el módulo Nuevo movimiento.</p>
                <?php else: ?>
                    <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Detalle</th><th class="right">Monto</th></tr></thead><tbody>
                    <?php $__currentLoopData = $summaryMovements->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr><td><?php echo e($movement->movement_date->format('d/m')); ?></td><td><?php echo e($movement->description ?: $movement->category); ?></td><td class="right amount <?php echo e($movement->type); ?>"><?php echo e($movement->type === 'income' ? '+' : '-'); ?>&dollar;<?php echo e(number_format((float) $movement->amount, 0, ',', '.')); ?></td></tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody></table></div>
                <?php endif; ?>
            </article>
        </section>
    <?php elseif($section === 'categories'): ?>
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">NUEVO MOVIMIENTO</p><h3><?php echo e($editingMovement ? 'Editar movimiento' : 'Nuevo movimiento'); ?></h3></div></div>
            <form id="movement-form" method="POST" action="<?php echo e($editingMovement ? route('movements.update', $editingMovement) : route('movements.store')); ?>" class="movement-form">
                <?php echo csrf_field(); ?>
                <?php if($editingMovement): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                <div class="field-row"><label>Tipo<select name="type" required><option value="expense" <?php if(old('type', $editingMovement?->type ?? 'expense') === 'expense'): echo 'selected'; endif; ?>>Gasto</option><option value="income" <?php if(old('type', $editingMovement?->type) === 'income'): echo 'selected'; endif; ?>>Ingreso</option></select></label><label>Monto<input name="amount" type="number" min="0.01" step="0.01" placeholder="0" value="<?php echo e(old('amount', $editingMovement?->amount)); ?>" required></label></div>
                <div class="field-row"><label>Etiqueta<select name="category" required><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($category->name); ?>" <?php if(old('category', $editingMovement?->category) === $category->name): echo 'selected'; endif; ?>><?php echo e($category->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></label><label>Fecha <span class="optional">dd/mm/aaaa</span><span class="date-control"><input class="date-display" type="text" inputmode="numeric" data-date-display placeholder="dd/mm/aaaa" value="<?php echo e($movementDateDisplay); ?>" required aria-label="Fecha, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="<?php echo e($movementDateIso); ?>" tabindex="-1" aria-hidden="true"><input type="hidden" name="movement_date" data-date-value value="<?php echo e($movementDateIso); ?>"></span></label></div>
                <label>Descripción <span class="optional">opcional</span><input name="description" type="text" maxlength="255" placeholder="Ej. supermercado, sueldo..." value="<?php echo e(old('description', $editingMovement?->description)); ?>"></label>
                <div class="form-actions"><button class="button primary" type="submit"><?php echo e($editingMovement ? 'Guardar cambios' : 'Guardar movimiento'); ?></button><?php if($editingMovement): ?><a class="button secondary" href="<?php echo e(route('dashboard', ['section' => 'categories', 'month' => $month])); ?>">Cancelar</a><?php endif; ?></div>
            </form>
        </section>

        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">ETIQUETAS</p><h3><?php echo e($editingCategory ? 'Editar etiqueta' : 'Administrar etiquetas'); ?></h3></div></div>
            <p class="muted tags-help">Regalos ya está disponible. Crea etiquetas propias para clasificar tus movimientos.</p>
            <form method="POST" action="<?php echo e($editingCategory ? route('categories.update', $editingCategory) : route('categories.store')); ?>" class="tag-form">
                <?php echo csrf_field(); ?>
                <?php if($editingCategory): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                <input type="hidden" name="month" value="<?php echo e($month); ?>">
                <input name="name" type="text" maxlength="80" placeholder="Ej. Mascotas, Viajes..." value="<?php echo e(old('name', $editingCategory?->name)); ?>" required>
                <button class="button primary" type="submit"><?php echo e($editingCategory ? 'Guardar cambios' : 'Crear etiqueta'); ?></button>
                <?php if($editingCategory): ?><a class="button secondary" href="<?php echo e(route('dashboard', ['section' => 'categories'])); ?>">Cancelar</a><?php endif; ?>
            </form>
            <div class="tag-list">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="category-chip"><?php echo e($category->name); ?> <?php if(!$category->is_default): ?><a href="<?php echo e(route('dashboard', ['section' => 'categories', 'edit_category' => $category->id])); ?>" title="Editar etiqueta">✎</a><form method="POST" action="<?php echo e(route('categories.destroy', $category)); ?>" onsubmit="return confirm('¿Eliminar esta etiqueta?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button type="submit" title="Eliminar etiqueta">×</button></form><?php endif; ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php elseif($section === 'debts'): ?>
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">DEUDAS PERSONALES</p><h3><?php echo e($editingDebt ? 'Editar deuda o cobro' : 'Deudas y cobros'); ?></h3></div><div class="debt-totals"><strong class="debt-total">Debo: &dollar;<?php echo e(number_format($pendingDebtTotal, 0, ',', '.')); ?></strong><strong class="receivable-total">Me deben: &dollar;<?php echo e(number_format($pendingReceivableTotal, 0, ',', '.')); ?></strong></div></div>
            <form method="POST" action="<?php echo e($editingDebt ? route('debts.update', $editingDebt) : route('debts.store')); ?>" class="debt-form">
                <?php echo csrf_field(); ?>
                <?php if($editingDebt): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
                <div class="field-row"><label>Tipo<select name="direction" required><option value="to_pay" <?php if(old('direction', $editingDebt?->direction ?? 'to_pay') === 'to_pay'): echo 'selected'; endif; ?>>Yo debo</option><option value="to_collect" <?php if(old('direction', $editingDebt?->direction) === 'to_collect'): echo 'selected'; endif; ?>>Me deben</option></select></label><label>Persona<input name="person" type="text" maxlength="120" placeholder="Ej. Camila" value="<?php echo e(old('person', $editingDebt?->person)); ?>" required></label></div>
                <div class="field-row"><label>Monto<input name="amount" type="number" min="0.01" step="0.01" placeholder="0" value="<?php echo e(old('amount', $editingDebt?->amount)); ?>" required></label><label>Fecha de pago <span class="optional">dd/mm/aaaa · opcional</span><span class="date-control"><input class="date-display" type="text" inputmode="numeric" data-date-display placeholder="dd/mm/aaaa" value="<?php echo e($debtDueDateDisplay); ?>" aria-label="Fecha de pago, día, mes y año"><button type="button" class="date-picker-trigger" data-date-trigger aria-label="Abrir calendario"><span class="calendar-icon" aria-hidden="true"></span></button><input class="date-native-picker" type="date" data-date-native value="<?php echo e($debtDueDateIso); ?>" tabindex="-1" aria-hidden="true"><input type="hidden" name="due_date" data-date-value value="<?php echo e($debtDueDateIso); ?>"></span></label></div>
                <label>Nota <span class="optional">opcional</span><input name="description" type="text" maxlength="255" placeholder="Ej. cena, préstamo..." value="<?php echo e(old('description', $editingDebt?->description)); ?>"></label>
                <div class="form-actions"><button class="button primary" type="submit"><?php echo e($editingDebt ? 'Guardar cambios' : 'Agregar registro'); ?></button><?php if($editingDebt): ?><a class="button secondary" href="<?php echo e(route('dashboard', ['section' => 'debts'])); ?>">Cancelar</a><?php endif; ?></div>
            </form>
            <?php if($debts->isEmpty()): ?>
                <p class="empty">No tienes deudas ni cobros registrados.</p>
            <?php else: ?>
                <div class="table-wrap"><table><thead><tr><th>Tipo</th><th>Persona</th><th>Nota</th><th>Vence</th><th>Estado</th><th class="right">Monto</th><th></th></tr></thead><tbody>
                <?php $__currentLoopData = $debts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $debt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($debt->status === 'paid' ? 'debt-paid' : ''); ?>"><td><span class="debt-direction <?php echo e($debt->direction); ?>"><?php echo e($debt->direction === 'to_pay' ? 'Yo debo' : 'Me deben'); ?></span></td><td><strong><?php echo e($debt->person); ?></strong></td><td><?php echo e($debt->description ?: 'Sin nota'); ?></td><td><?php echo e($debt->due_date?->format('d/m/Y') ?: 'Sin fecha'); ?></td><td><span class="debt-status <?php echo e($debt->status); ?>"><?php echo e($debt->status === 'paid' ? 'Pagada' : 'Pendiente'); ?></span></td><td class="right amount <?php echo e($debt->direction === 'to_collect' ? 'income' : 'expense'); ?>">&dollar;<?php echo e(number_format((float) $debt->amount, 0, ',', '.')); ?></td><td class="right actions"><a class="small-action" href="<?php echo e(route('dashboard', ['section' => 'debts', 'edit_debt' => $debt->id])); ?>">Editar</a><form method="POST" action="<?php echo e(route('debts.toggle', $debt)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="small-action" type="submit"><?php echo e($debt->status === 'paid' ? 'Reabrir' : ($debt->direction === 'to_collect' ? 'Cobrado' : 'Pagar')); ?></button></form><form method="POST" action="<?php echo e(route('debts.destroy', $debt)); ?>" onsubmit="return confirm('¿Eliminar este registro?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="delete" title="Eliminar" type="submit">×</button></form></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>
    <?php elseif($section === 'goal'): ?>
        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">PLANIFICACIÓN</p><h3>Meta de ahorro para <?php echo e($month); ?></h3></div><strong class="goal-percent"><?php echo e($goalProgress); ?>%</strong></div>
            <div class="goal-track"><i style="width: <?php echo e($goalProgress); ?>%"></i></div>
            <p class="muted goal-copy">Has logrado &dollar;<?php echo e(number_format($goalSavedAmount, 0, ',', '.')); ?> de una meta de &dollar;<?php echo e(number_format($goalAmount, 0, ',', '.')); ?>.</p>
            <form method="POST" action="<?php echo e(route('goal.store')); ?>" class="goal-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="month" value="<?php echo e($month); ?>">
                <div class="field-row"><label>Monto objetivo<input name="target_amount" type="number" min="0" step="0.01" value="<?php echo e($goalAmount); ?>" required></label><label>Ahorro logrado<input name="saved_amount" type="number" min="0" step="0.01" value="<?php echo e($goalSavedAmount); ?>" required></label></div>
                <p class="muted goal-help">El ahorro logrado se registra por separado y no incluye automáticamente tu saldo disponible.</p>
                <div class="form-actions"><button class="button primary" type="submit">Guardar meta</button></div>
            </form>
        </section>
    <?php else: ?>
        <section class="kpi-grid history-kpis" aria-label="Indicadores del mes">
            <article class="kpi-card"><span>Ingresos</span><strong>&dollar;<?php echo e(number_format($income, 0, ',', '.')); ?></strong><small>Total del mes</small></article>
            <article class="kpi-card"><span>Gastos</span><strong>&dollar;<?php echo e(number_format($expenses, 0, ',', '.')); ?></strong><small>Consumo del mes</small></article>
            <article class="kpi-card accent"><span>Disponible</span><strong>&dollar;<?php echo e(number_format($savings, 0, ',', '.')); ?></strong><small><?php echo e($savingRate); ?>% de tus ingresos</small></article>
            <article class="kpi-card"><span>Movimientos</span><strong><?php echo e($movements->count()); ?></strong><small><?php echo e($month); ?></small></article>
        </section>

        <section class="panel module-panel">
            <div class="panel-heading"><div><p class="eyebrow">HISTORIAL</p><h3>Gastos e ingresos de <?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $month)->locale('es')->translatedFormat('F Y')); ?></h3></div></div>
            <?php if($movements->isEmpty()): ?>
                <p class="empty">Agrega tu primer movimiento para comenzar a ver tu evolución.</p>
            <?php else: ?>
                <div class="table-wrap"><table><thead><tr><th>Fecha</th><th>Descripción</th><th>Etiqueta</th><th>Tipo</th><th class="right">Monto</th><th></th></tr></thead><tbody>
                <?php $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr><td><?php echo e($movement->movement_date->format('d/m/Y')); ?></td><td><?php echo e($movement->description ?: 'Sin descripción'); ?></td><td><?php echo e($movement->category); ?></td><td><span class="tag <?php echo e($movement->type); ?>"><?php echo e($movement->type === 'income' ? 'Ingreso' : 'Gasto'); ?></span></td><td class="right amount <?php echo e($movement->type); ?>"><?php echo e($movement->type === 'income' ? '+' : '-'); ?>&dollar;<?php echo e(number_format((float) $movement->amount, 0, ',', '.')); ?></td><td class="right"><a class="small-action" href="<?php echo e(route('dashboard', ['section' => 'categories', 'month' => $month, 'edit' => $movement->id])); ?>#movement-form">Editar</a><form class="inline-form" method="POST" action="<?php echo e(route('movements.destroy', $movement)); ?>" onsubmit="return confirm('¿Eliminar este movimiento?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="delete" title="Eliminar" type="submit">×</button></form></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody></table></div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\diego\Documents\Codex\2026-09-27\qu\resources\views/dashboard.blade.php ENDPATH**/ ?>