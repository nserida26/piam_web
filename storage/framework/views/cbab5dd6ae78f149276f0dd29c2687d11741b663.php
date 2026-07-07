<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('localite.title'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('localite.title'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(url('admin/localites')); ?>"> <?php echo app('translator')->get('localite.title'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
    <?php echo app('translator')->get('localite.title'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="card-title"><?php echo app('translator')->get('localite.title'); ?></h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo e(url('/admin/localites/create')); ?>" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> <?php echo app('translator')->get('localite.add_new'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="localites-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo app('translator')->get('localite.code'); ?></th>
                                    <th><?php echo app('translator')->get('localite.intitule'); ?></th>
                                    <th><?php echo app('translator')->get('localite.intitule_fr'); ?></th>
                                    <th><?php echo app('translator')->get('localite.wilaya'); ?></th>
                                    <th><?php echo app('translator')->get('localite.moughata'); ?></th>
                                    <th><?php echo app('translator')->get('localite.commune'); ?></th>
                                    <th><?php echo app('translator')->get('localite.valider'); ?></th>
                                    <th><?php echo app('translator')->get('localite.actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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
    $('#localites-table').DataTable({
        language: dtLang,
        responsive: true,
        processing: true,
        serverSide: true,
        pageLength: 25,
        ajax: {
            url: '<?php echo e(route('admin.localites.datatable')); ?>',
            type: 'GET'
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4, orderable: false },
            { data: 5, orderable: false },
            { data: 6, orderable: false },
            { data: 7, orderable: false },
            { data: 8, orderable: false }
        ]
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/localites/index.blade.php ENDPATH**/ ?>