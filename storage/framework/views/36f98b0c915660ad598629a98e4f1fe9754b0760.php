
<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('user.users'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('user.users'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.users.index')); ?>"> <?php echo app('translator')->get('sidebar.users'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('user.users'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-md-10 col-sm-offset-1">
        <div class="card">
            <div class="card-header"><?php echo app('translator')->get('user.create_new'); ?></div>
            <div class="card-body">

                <form class="form-horizontal" action="<?php echo e(route('admin.users.store')); ?>" method="post"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label for="name" class="control-label"><?php echo app('translator')->get('user.name'); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('user.name')); ?>"
                            value="<?php echo e(old('name')); ?>">
                    </div>

                    <div class="form-group">
                        <label for="email" class="control-label"><?php echo app('translator')->get('user.email'); ?> <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="<?php echo e(__('user.email')); ?>"
                            value="<?php echo e(old('email')); ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="control-label"><?php echo app('translator')->get('user.password'); ?> <span class="text-danger">*</span></label>
                        <input id="password" type="password" class="form-control" name="password"
                            autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="password-confirm" class="control-label"><?php echo app('translator')->get('user.confirm_password'); ?> <span class="text-danger">*</span></label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            autocomplete="new-password">
                    </div>

                    <div class="form-group">
                        <label for="operateur_id" class="control-label"><?php echo app('translator')->get('coordinateur.ong'); ?></label>
                        <select name="operateur_id" id="operateur_id" class="form-control">
                            <option value="">—</option>
                            <?php $__currentLoopData = $operateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operateur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($operateur->id); ?>" <?php echo e((string) old('operateur_id', '') === (string) $operateur->id ? 'selected' : ''); ?>>
                                    <?php echo e($operateur->intitule); ?><?php echo e($operateur->type_struct ? ' — ' . $operateur->type_struct : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">Obligatoire pour un coordinateur : il ne verra que les données de cette ONG.</small>
                        <?php $__errorArgs = ['operateur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group" style="margin-top:10px">
                        <label for="role_id" class="col-sm-2 control-label"><?php echo app('translator')->get('user.roles_label'); ?> <span class="text-danger">*</span></label>
                        <div class="col-sm-offset-2 col-sm-10" style="margin-top:10px">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label>
                                    <input type="checkbox" name="role_id[]" class="minimal" value="<?php echo e($role->id); ?>"
                                        <?php echo e(in_array($role->id, old('role_id', [])) ? 'checked' : ''); ?>>
                                    <span style="display:inline-block;margin-right:15px"><?php echo e(ucfirst($role->name)); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <?php echo $__env->make('admin.users._localites', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label"><?php echo app('translator')->get('user.photo'); ?></label>
                        <div class="col-sm-10">
                            <input type="file" name="image" class="form-control-file" id="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" style="margin-top:10px"><?php echo app('translator')->get('user.add_new'); ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/users/create.blade.php ENDPATH**/ ?>