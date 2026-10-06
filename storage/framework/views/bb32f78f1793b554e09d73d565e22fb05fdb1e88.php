<!DOCTYPE html>
<!--
   This is a starter template page. Use this page to start your new project from
   scratch. This page gets rid of all links and provides the needed markup only.
   -->
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(app()->getLocale() == 'ar' ? 'rtl' : 'ltr'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?php echo $__env->yieldContent('title'); ?> | PIAM</title>
    <link href="<?php echo e(asset('assets/admin/imgs/logo.png')); ?>" rel="icon" type="image/png">
    <!-- Cairo Font (Google Fonts) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/fontawesome-free/css/all.min.css')); ?>">
    <?php echo $__env->yieldPushContent('css'); ?>
    <!-- AdminLTE Theme -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/css/adminlte.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/modern-theme.css')); ?>">
    <!-- Source Sans Pro (fallback) -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/fonts/SansPro/SansPro.min.css')); ?>">
    <?php if(LaravelLocalization::getCurrentLocale() == 'fr'): ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/bootstrap-4.0.0-dist/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/mycustomstyle.css')); ?>">
    <?php endif; ?>
    <?php if(LaravelLocalization::getCurrentLocale() == 'ar'): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/bootstrap_rtl-v4.2.1/bootstrap.min.css')); ?>">
        <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/bootstrap_rtl-v4.2.1/custom_rtl.css')); ?>">
    <?php endif; ?>
    <!-- PIAM Theme — palette officielle + Cairo (chargé en dernier pour priorité) -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/css/piam-theme.css')); ?>?v=<?php echo e(filemtime(public_path('assets/admin/css/piam-theme.css'))); ?>">
</head>

<body class="fixed">

    <div class="wrapper">
        <!-- Navbar -->
        <?php echo $__env->make('admin.includes.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        <?php echo $__env->make('admin.includes.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!--  End Main Sidebar Container -->
        <!-- Content Wrapper. Contains page content -->
        <?php echo $__env->make('admin.includes.content', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- /.content-wrapper -->
        <?php echo $__env->make('admin.includes.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <!-- Main Footer -->
    </div>
    <!-- ./wrapper -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo app('translator')->get('auth.logout'); ?></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body"><?php echo app('translator')->get('auth.logout_confirm'); ?></div>
                <div class="modal-footer">
                    <button class="btn btn-link" type="button" data-dismiss="modal"><?php echo app('translator')->get('general.cancel'); ?></button>
                    <a href="<?php echo e(route('admin.logout')); ?>" class="btn btn-danger"><?php echo app('translator')->get('auth.logout'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="<?php echo e(asset('assets/admin/plugins/jquery/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo e(asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('script'); ?>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('assets/admin/dist/js/adminlte.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/js/general.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('custom'); ?>

</body>

</html>
<?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/layouts/admin.blade.php ENDPATH**/ ?>