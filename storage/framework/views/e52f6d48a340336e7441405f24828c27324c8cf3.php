<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('sidebar.communes'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?> <?php echo app('translator')->get('sidebar.communes'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(url('admin/communes')); ?>"><?php echo app('translator')->get('sidebar.communes'); ?></a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?> <?php echo app('translator')->get('sidebar.communes'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title font-weight-bold"><?php echo app('translator')->get('sidebar.communes'); ?></h3>
                <a href="<?php echo e(url('/admin/communes/create')); ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> <?php echo app('translator')->get('crud.add_new'); ?>
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dt-communes" class="table table-bordered table-striped table-hover w-100">
                        <thead class="thead-dark">
                            <tr>
                                <th><?php echo app('translator')->get('crud.no'); ?></th>
                                <th><?php echo app('translator')->get('crud.code'); ?></th>
                                <th><?php echo app('translator')->get('crud.intitule'); ?></th>
                                <th><?php echo app('translator')->get('crud.intitule_fr'); ?></th>
                                <th><?php echo app('translator')->get('crud.moughata'); ?></th>
                                <th><?php echo app('translator')->get('crud.wilaya'); ?></th>
                                <th class="text-center"><?php echo app('translator')->get('crud.actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $commune; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><span class="badge badge-secondary"><?php echo e($item->code); ?></span></td>
                                <td><?php echo e($item->intitule); ?></td>
                                <td><?php echo e($item->intitule_fr); ?></td>
                                <td><?php echo e($item->moughata->intitule_fr ?? '—'); ?></td>
                                <td><?php echo e($item->wilaya->intitule_fr ?? '—'); ?></td>
                                <td class="text-center text-nowrap">
                                    <a href="<?php echo e(url('/admin/communes/' . $item->id)); ?>" class="btn btn-info btn-xs" title="<?php echo app('translator')->get('crud.view'); ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(url('/admin/communes/' . $item->id . '/edit')); ?>" class="btn btn-primary btn-xs" title="<?php echo app('translator')->get('crud.edit'); ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(url('/admin/communes/' . $item->id)); ?>" style="display:inline">
                                        <?php echo method_field('DELETE'); ?> <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-danger btn-xs" title="<?php echo app('translator')->get('crud.delete'); ?>"
                                            onclick="return confirm('<?php echo app('translator')->get('crud.confirm_delete'); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
<script src="<?php echo e(asset('assets/admin/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('custom'); ?>
<script>
$(function () {
    <?php echo $__env->make('admin.includes.datatable-lang', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    $('#dt-communes').DataTable({
        language: dtLang,
        responsive: true,
        order: [[1, 'asc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        pageLength: 25
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/communes/index.blade.php ENDPATH**/ ?>