<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('coordinateur.facilitateurs'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('coordinateur.facilitateurs'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('coordinateur.dashboard')); ?>"><?php echo app('translator')->get('coordinateur.space'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('coordinateur.facilitateurs'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a class="btn btn-primary btn-sm" href="<?php echo e(route('coordinateur.facilitateurs.create')); ?>">
                <i class="fa fa-plus"></i> <?php echo app('translator')->get('coordinateur.new_facilitateur'); ?>
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th><?php echo app('translator')->get('coordinateur.name'); ?></th>
                    <th><?php echo app('translator')->get('coordinateur.email'); ?></th>
                    <th><?php echo app('translator')->get('coordinateur.localites'); ?></th>
                    <th><?php echo app('translator')->get('coordinateur.projets'); ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $facilitateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($f->username); ?></td>
                        <td><?php echo e($f->email); ?></td>
                        <td>
                            <?php $__currentLoopData = $f->localites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-success"><?php echo e($l->intitule); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td>
                            <?php $__currentLoopData = $f->projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-info"><?php echo e($p->intitule_resume); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="text-right">
                            <a href="<?php echo e(route('coordinateur.facilitateurs.edit', $f->id)); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> <?php echo app('translator')->get('coordinateur.edit'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted p-4"><?php echo app('translator')->get('coordinateur.none'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/coordinateur/facilitateurs/index.blade.php ENDPATH**/ ?>