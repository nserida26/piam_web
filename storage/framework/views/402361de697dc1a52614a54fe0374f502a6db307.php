
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('user.users'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('user.users'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.users.index')); ?>"> <?php echo app('translator')->get('sidebar.users'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
    <?php echo app('translator')->get('user.users'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('admin.includes.alerts.success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('admin.includes.alerts.warning', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="card">
        <div class="card-header">

            <div class="card-tools">

                <a class="btn btn-primary btn-sm" href="<?php echo e(route('admin.users.create')); ?>"> <i class="fa fa-plus"></i>
                    <?php echo app('translator')->get('user.add'); ?></a>


                <a class="btn btn-primary btn-sm" href="<?php echo e(url('admin/users/roles/create')); ?>"> <i class="fa fa-plus"></i>
                    <?php echo app('translator')->get('user.add_role'); ?></a>


            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table class="table table-bordered table-striped" id="datatable">
                <thead>
                    <tr>

                        <th> <?php echo app('translator')->get('user.name'); ?></th>
                        <th> <?php echo app('translator')->get('user.email'); ?></th>
                        <th> <?php echo app('translator')->get('user.roles'); ?></th>
                        <th> <?php echo app('translator')->get('user.status'); ?></th>

                        <th> <?php echo app('translator')->get('user.photo'); ?></th>
                        <th> <?php echo app('translator')->get('user.actions'); ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>

                            <td><?php echo e($user->username); ?></td>
                            <td><?php echo e($user->email); ?></td>

                            <td>
                                <ul class="list">
                                    <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="list-item"><?php echo e(ucfirst($role->name)); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </td>
                            <td>
                                <?php if($user->isActive()): ?>
                                    <span class="badge badge-success"><?php echo app('translator')->get('user.status_active'); ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?php echo app('translator')->get('user.status_disabled'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><img src="<?php echo e(asset('assets/admin/imgs/' . $user->photo)); ?>" alt=""
                                    style="width:60px;border-radius:5px" class="table-avatar"></td>
                            <td>

                                <div class="button-group">


                                    <a href="<?php echo e(route('admin.users.show', $user->id)); ?>"><i class="fa fa-eye text-success"
                                            style="display:inline-block;margin-right:10px"></i></a>



                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"><i
                                            class="fas fa-edit text-info"></i></a>

                                    <form action="<?php echo e(route('admin.users.toggle_status', $user->id)); ?>"
                                        method="post" style="display: inline;border:0">
                                        <?php echo csrf_field(); ?>
                                        <?php if($user->isActive()): ?>
                                            <button class="text-warning toggle-status" type="submit" style="border:0;background:none"
                                                title="<?php echo app('translator')->get('user.disable'); ?>" data-confirm="<?php echo app('translator')->get('user.disable_confirm'); ?>">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="text-success" type="submit" style="border:0;background:none"
                                                title="<?php echo app('translator')->get('user.enable'); ?>">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>


                                    <form id="delete-form" action="<?php echo e(route('admin.users.destroy', $user->id)); ?>"
                                        method="post" style="display: inline;border:0">

                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>

                                        <button class="text-danger delete" type="submit" style="border:0;background:none">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                </div>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



                </tbody>

                <tfoot>
                    <tr>

                        <th> <?php echo app('translator')->get('user.name'); ?></th>
                        <th> <?php echo app('translator')->get('user.email'); ?></th>
                        <th> <?php echo app('translator')->get('user.roles'); ?></th>
                        <th> <?php echo app('translator')->get('user.status'); ?></th>

                        <th> <?php echo app('translator')->get('user.photo'); ?></th>
                        <th> <?php echo app('translator')->get('user.actions'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo e(asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/sweetalert2/sweetalert2.all.min.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('custom'); ?>
    <script>
        $('#datatable').DataTable();
        $(document).on('click', '.toggle-status', function(e) {
            if (!confirm($(this).data('confirm'))) e.preventDefault();
        });
        $(document).on('click', '.delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');

            // Sans SweetAlert (fichier absent), on garde une confirmation native.
            if (typeof Swal === 'undefined') {
                if (confirm(<?php echo json_encode(__('user.delete_confirm'), 15, 512) ?>)) form.submit();
                return;
            }

            Swal.fire({
                title: <?php echo json_encode(__('user.delete_confirm'), 15, 512) ?>,
                text: <?php echo json_encode(__('user.delete_confirm_text'), 15, 512) ?>,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: <?php echo json_encode(__('user.delete_yes'), 15, 512) ?>,
                cancelButtonText: <?php echo json_encode(__('user.delete_no'), 15, 512) ?>
            }).then(function (result) {
                if (result.value || result.isConfirmed) form.submit();
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/users/index.blade.php ENDPATH**/ ?>