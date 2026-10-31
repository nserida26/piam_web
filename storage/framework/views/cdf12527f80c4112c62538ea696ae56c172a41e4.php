<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('coordinateur.validation'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('coordinateur.validation'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('coordinateur.dashboard')); ?>"><?php echo app('translator')->get('coordinateur.space'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('coordinateur.validation'); ?> <?php $__env->stopSection(); ?>

<?php
    $statuts = ['attente', 'valide_coord', 'rejete', 'valide_admin', 'tous'];
    $badge = function ($r) {
        if ($r->confirmed) return ['success', __('coordinateur.statut_valide_admin')];
        if ($r->statut_coordinateur === 'valide') return ['primary', __('coordinateur.statut_valide_coord')];
        if ($r->statut_coordinateur === 'rejete') return ['danger', __('coordinateur.statut_rejete')];
        return ['warning', __('coordinateur.statut_attente')];
    };
    $editable = in_array($statut, ['attente', 'valide_coord', 'rejete', 'tous']);
?>

<?php $__env->startSection('content'); ?>
<ul class="nav nav-tabs mb-3">
    <?php $__currentLoopData = ['localite', 'infrastructure']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="nav-item">
            <a class="nav-link <?php echo e($type === $t ? 'active' : ''); ?>"
                href="<?php echo e(route('coordinateur.validation', ['type' => $t, 'statut' => $statut])); ?>"><?php echo app('translator')->get('coordinateur.type_' . $t); ?></a>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</ul>

<div class="card">
    <div class="card-header">
        <form method="get" class="form-inline">
            <input type="hidden" name="type" value="<?php echo e($type); ?>">
            <select name="statut" class="form-control form-control-sm mr-2 mb-1">
                <?php $__currentLoopData = $statuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s); ?>" <?php echo e($statut === $s ? 'selected' : ''); ?>><?php echo app('translator')->get('coordinateur.statut_' . $s); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control form-control-sm mr-2 mb-1"
                style="min-width:260px" placeholder="<?php echo e(__('coordinateur.search')); ?>">
            <button class="btn btn-sm btn-secondary mb-1"><i class="fas fa-filter"></i> <?php echo app('translator')->get('coordinateur.filter'); ?></button>
        </form>
    </div>

    <form method="post" action="<?php echo e(route('coordinateur.validation.store')); ?>" id="validation-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="type" value="<?php echo e($type); ?>">
        <input type="hidden" name="action" id="validation-action">

        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:30px"><?php if($editable): ?><input type="checkbox" id="check-all" title="<?php echo e(__('coordinateur.select_all')); ?>"><?php endif; ?></th>
                        <th><?php echo app('translator')->get('coordinateur.col_localite'); ?></th>
                        <?php if($type === 'infrastructure'): ?><th><?php echo app('translator')->get('coordinateur.col_infrastructure'); ?></th><?php endif; ?>
                        <th><?php echo app('translator')->get('coordinateur.col_commune'); ?></th>
                        <th><?php echo app('translator')->get('coordinateur.col_operation'); ?></th>
                        <th><?php echo app('translator')->get('coordinateur.col_projet'); ?></th>
                        <th><?php echo app('translator')->get('coordinateur.col_facilitateur'); ?></th>
                        <th><?php echo app('translator')->get('coordinateur.col_date'); ?></th>
                        <th><?php echo app('translator')->get('coordinateur.statut'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php [$color, $label] = $badge($r); ?>
                        <tr>
                            <td>
                                <?php if (! ($r->confirmed)): ?>
                                    <input type="checkbox" class="row-check" name="ids[]" value="<?php echo e($r->id); ?>">
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($r->localite); ?> <?php if($r->code_ansade): ?><small class="text-muted">(<?php echo e($r->code_ansade); ?>)</small><?php endif; ?></td>
                            <?php if($type === 'infrastructure'): ?><td><?php echo e($r->infrastructure); ?></td><?php endif; ?>
                            <td><?php echo e($r->commune); ?></td>
                            <td><?php echo e($r->operation); ?></td>
                            <td><?php echo e($r->projet); ?></td>
                            <td><?php echo e($r->facilitateur); ?></td>
                            <td class="text-nowrap"><?php echo e($r->date_op); ?></td>
                            <td>
                                <span class="badge badge-<?php echo e($color); ?>"><?php echo e($label); ?></span>
                                <?php if($r->coordinateur_remarque): ?>
                                    <br><small class="text-muted"><?php echo e($r->coordinateur_remarque); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-info btn-detail"
                                    data-url="<?php echo e(route('coordinateur.validation.detail', [$type, $r->id])); ?>">
                                    <i class="fas fa-eye"></i> <?php echo app('translator')->get('coordinateur.detail'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="10" class="text-center text-muted p-4"><?php echo app('translator')->get('coordinateur.no_reports'); ?></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($editable && $reports->count()): ?>
            <div class="card-footer">
                <div class="form-group mb-2">
                    <textarea name="remarque" class="form-control form-control-sm" rows="2"
                        placeholder="<?php echo e(__('coordinateur.remarque')); ?>"><?php echo e(old('remarque')); ?></textarea>
                </div>
                <button type="button" class="btn btn-success btn-sm" data-action="valide">
                    <i class="fas fa-check"></i> <?php echo app('translator')->get('coordinateur.btn_valider'); ?>
                </button>
                <button type="button" class="btn btn-danger btn-sm" data-action="rejete">
                    <i class="fas fa-times"></i> <?php echo app('translator')->get('coordinateur.btn_rejeter'); ?>
                </button>
                <span class="text-muted small ml-2"><?php echo app('translator')->get('coordinateur.help_validation'); ?></span>
            </div>
        <?php endif; ?>
    </form>

    <div class="p-2"><?php echo e($reports->links('pagination::bootstrap-4')); ?></div>
</div>

<?php if($errors->any()): ?>
    <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
<?php endif; ?>

<div class="modal fade" id="detail-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo app('translator')->get('coordinateur.detail'); ?></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body"><table class="table table-sm mb-0"><tbody id="detail-body"></tbody></table></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom'); ?>
<script>
$(function () {
    $('#check-all').on('change', function () { $('.row-check').prop('checked', this.checked); });

    $('[data-action]').on('click', function () {
        var action = $(this).data('action');
        if (!$('.row-check:checked').length) { alert(<?php echo json_encode(__('coordinateur.select_all'), 15, 512) ?>); return; }
        if (action === 'valide' && !confirm(<?php echo json_encode(__('coordinateur.confirm_valider'), 15, 512) ?>)) return;
        $('#validation-action').val(action);
        $('#validation-form').submit();
    });

    $('.btn-detail').on('click', function () {
        var body = $('#detail-body').html('<tr><td>…</td></tr>');
        $('#detail-modal').modal('show');
        $.getJSON($(this).data('url'), function (res) {
            body.empty();
            $.each(res.fields, function (k, v) {
                if (typeof v === 'object') v = JSON.stringify(v);
                body.append($('<tr>').append($('<th class="text-nowrap">').text(k.replace(/_/g, ' '))).append($('<td>').text(v)));
            });
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/coordinateur/validation.blade.php ENDPATH**/ ?>