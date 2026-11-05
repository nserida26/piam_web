<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="brand-link">
        <img src="<?php echo e(asset('/assets/admin/imgs/logo.png')); ?>" alt="MRT Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">PIAM</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo e(asset('/assets/admin/imgs/' . Auth::user()->photo)); ?>"
                    class="img-profile rounded-circle avatar user-image" width="160px" height="160px"
                    alt="User Image" />
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <i class="fas fa-user-circle mr-2"></i><?php echo e(auth()->user()->username); ?>

                </a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <?php ($isDrha = auth()->user()->isDrha()); ?>
                <?php if(auth()->user()->isCoordinateur()): ?>
                    
                    <li class="nav-header"><?php echo app('translator')->get('coordinateur.space'); ?> — <?php echo e(optional(auth()->user()->operateur)->intitule); ?></li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('coordinateur.dashboard')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('coordinateur.dashboard') ? 'active' : ''); ?>">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p><?php echo app('translator')->get('coordinateur.dashboard'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('coordinateur.facilitateurs.index')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('coordinateur.facilitateurs.*') ? 'active' : ''); ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p><?php echo app('translator')->get('coordinateur.facilitateurs'); ?></p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo e(route('coordinateur.validation')); ?>"
                            class="nav-link <?php echo e(request()->routeIs('coordinateur.validation*') ? 'active' : ''); ?>">
                            <i class="nav-icon fas fa-check-square"></i>
                            <p><?php echo app('translator')->get('coordinateur.validation'); ?></p>
                        </a>
                    </li>
                <?php else: ?>
                <li class="nav-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>"
                        class="nav-link <?php echo e(request()->is('dashboard') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.dashboard'); ?>

                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo e(route('admin.maps.index')); ?>"
                        class="nav-link <?php echo e(request()->is('maps') ? 'active' : ''); ?>">
                        <i class="nav-icon fas fa-map"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.maps'); ?>

                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-copy"></i>
                        <p>

                            <?php echo app('translator')->get('sidebar.reporting'); ?>

                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/reporting')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reporting_territoire'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reporting.fetch_reporting_annuel')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reporting_annuel'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reporting.fetch_reporting_projet')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reporting_projet'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reporting.fetch_reporting_localite')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reporting_localite'); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>

                
                <?php if (! ($isDrha)): ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.injection'); ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.imports.administrations.index')); ?>" class="nav-link">
                                <i class="fas fa-school"></i>
                                <p><?php echo app('translator')->get('sidebar.admins'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.imports.reports.index')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.operations'); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-edit"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.validation'); ?>
                            <i class="fas fa-angle-left right"></i>

                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/reports/localites')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.family'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.reports.menages')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.menages'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/reports/infrastructures')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.institution'); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <?php if (! ($isDrha)): ?>
                    <a href="" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p><?php echo app('translator')->get('sidebar.musers'); ?>

                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.users.index')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.users'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.users.roles')); ?>" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.roles'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/wilaya-user')); ?>" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.wilaya_user'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('admin/user-operateur')); ?>" class="nav-link ">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.user_operateur'); ?></p>
                            </a>
                        </li>
                    </ul>
                    <?php endif; ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(url('/admin/projets')); ?>" class="nav-link">
                                        <i class="nav-icon far fa-calendar-alt"></i>
                                        <p><?php echo app('translator')->get('sidebar.projects'); ?></p>
                                    </a>
                                    
                                </li>
                                <li class="nav-item">
                                    <a href="<?php echo e(url('/admin/operateurs')); ?>" class="nav-link">
                                        <i class="fas fa-business-time nav-icon"></i>
                                        <p><?php echo app('translator')->get('sidebar.operators'); ?></p>
                                    </a>
                                </li>
                                
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.settings'); ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/wilayas')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.wilayas'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/moughatas')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.moughatas'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/communes')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.communes'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/localites')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.localites'); ?></p>
                            </a>
                        </li>
                        <?php if (! ($isDrha)): ?>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/reorganisation/localites')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reorganisation_localites'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/reorganisation/communes')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reorganisation_communes'); ?></p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/reorganisation/moughatas')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.reorganisation_moughatas'); ?></p>
                            </a>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a href="<?php echo e(url('/admin/infrastructures')); ?>" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p><?php echo app('translator')->get('sidebar.infrastructures'); ?></p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-language"></i>
                        <p>
                            <?php echo app('translator')->get('sidebar.languages'); ?>
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item">

                                <a class="nav-link" rel="alternate" hreflang="<?php echo e($localeCode); ?>"
                                    href="<?php echo e(LaravelLocalization::getLocalizedURL($localeCode, null, [], true)); ?>">
                                    <i class="far fa-circle nav-icon"></i>
                                    <?php echo e($properties['native']); ?>

                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
<?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/includes/sidebar.blade.php ENDPATH**/ ?>