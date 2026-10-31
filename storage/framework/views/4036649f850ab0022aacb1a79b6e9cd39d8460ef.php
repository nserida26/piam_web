
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
            <div class="card-header"><?php echo app('translator')->get('user.update_user'); ?></div>
            <div class="card-body">

                <form class="form-horizontal" action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="post"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label"><?php echo app('translator')->get('user.name'); ?> <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('user.name')); ?>"
                                value="<?php echo e($user->username); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label"><?php echo app('translator')->get('user.email'); ?> <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" placeholder="<?php echo e(__('user.email')); ?>"
                                value="<?php echo e($user->email); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="operateur_id" class="control-label"><?php echo app('translator')->get('coordinateur.ong'); ?></label>
                        <select name="operateur_id" id="operateur_id" class="form-control">
                            <option value="">—</option>
                            <?php $__currentLoopData = $operateurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $operateur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($operateur->id); ?>" <?php echo e((string) old('operateur_id', $user->operateur_id) === (string) $operateur->id ? 'selected' : ''); ?>>
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
                            <?php
                                $userRoles = DB::table('role_user')->where('user_id', $user->id)->get();
                                $userRolesArray = $userRoles->pluck('role_id')->toArray();
                            ?>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label>
                                    <input type="checkbox" name="role_id[]" class="minimal" value="<?php echo e($role->id); ?>"
                                        <?php if(in_array($role->id, old('role_id', $userRolesArray))): ?> checked <?php endif; ?>>
                                    <span style="display:inline-block;margin-right:15px"><?php echo e(ucfirst($role->name)); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <?php echo $__env->make('admin.users._localites', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label"><?php echo app('translator')->get('user.photo'); ?></label>
                        <div class="col-sm-10">
                            <img src="<?php echo e(asset('assets/admin/imgs/' . $user->photo)); ?>" id="photo"
                                style="margin-bottom: 8px;height:100px;border-radius:5px"><br>
                            <input type="file" name="image" class="form-control-file" id="image">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success" style="margin-top:10px"><?php echo app('translator')->get('user.update_user'); ?></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('custom'); ?>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#photo').attr('src', e.target.result).height(80).width(80);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#image").change(function() { readURL(this); });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>