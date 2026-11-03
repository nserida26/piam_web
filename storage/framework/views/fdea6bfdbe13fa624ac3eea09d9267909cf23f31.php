
<?php $editing = $facilitateur->exists; ?>
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('coordinateur.facilitateurs'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo e($editing ? __('coordinateur.edit_facilitateur') : __('coordinateur.new_facilitateur')); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('coordinateur.facilitateurs.index')); ?>"><?php echo app('translator')->get('coordinateur.facilitateurs'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo e($editing ? __('coordinateur.edit_facilitateur') : __('coordinateur.new_facilitateur')); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post"
                    action="<?php echo e($editing ? route('coordinateur.facilitateurs.update', $facilitateur->id) : route('coordinateur.facilitateurs.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if($editing): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo app('translator')->get('coordinateur.name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $facilitateur->username)); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo app('translator')->get('coordinateur.email'); ?> <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $facilitateur->email)); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><?php echo app('translator')->get('coordinateur.password'); ?> <?php if (! ($editing)): ?><span class="text-danger">*</span><?php endif; ?></label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password"
                                <?php if($editing): ?> placeholder="<?php echo e(__('coordinateur.password_keep')); ?>" <?php else: ?> required <?php endif; ?>>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php echo app('translator')->get('coordinateur.password_confirm'); ?></label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo app('translator')->get('coordinateur.projets'); ?></label>
                        <div>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="projet_ids[]" value="<?php echo e($projet->id); ?>"
                                        <?php echo e(in_array($projet->id, $assignedProjets) ? 'checked' : ''); ?>>
                                    <?php echo e($projet->intitule_resume); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo app('translator')->get('coordinateur.operateurs'); ?></label>
                        <div>
                            <?php $__currentLoopData = $operateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operateur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="mr-3 font-weight-normal">
                                    <input type="checkbox" name="operateur_ids[]" value="<?php echo e($operateur->id); ?>"
                                        <?php echo e(in_array($operateur->id, $assignedOperateurs) ? 'checked' : ''); ?>>
                                    <?php echo e($operateur->intitule); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <small class="text-muted"><?php echo app('translator')->get('coordinateur.assign_help'); ?></small>
                    </div>

                    <?php echo $__env->make('admin.users._localites', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> <?php echo app('translator')->get('coordinateur.save'); ?></button>
                        <a href="<?php echo e(route('coordinateur.facilitateurs.index')); ?>" class="btn btn-secondary"><?php echo app('translator')->get('coordinateur.cancel'); ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/coordinateur/facilitateurs/form.blade.php ENDPATH**/ ?>