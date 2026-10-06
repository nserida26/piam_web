
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('sidebar.dashboard'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('sidebar.dashboard'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderlink'); ?>
    <a href="<?php echo e(route('admin.dashboard')); ?>">
        <?php echo app('translator')->get('sidebar.dashboard'); ?> </a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheaderactive'); ?>
    <?php echo app('translator')->get('sidebar.dashboard'); ?>
<?php $__env->stopSection(); ?>
<?php
    $formatNumberSafe = function ($value, $decimals = 0) {
        return number_format((float) ($value ?? 0), $decimals, ',', ' ');
    };

    $formatPercentSafe = function ($numerator, $denominator, $decimals = 1) {
        $denominator = (float) ($denominator ?? 0);

        if ($denominator == 0.0) {
            return number_format(0, $decimals, ',', ' ');
        }

        return number_format(((float) ($numerator ?? 0) * 100) / $denominator, $decimals, ',', ' ');
    };
?>
<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">
    <!-- daterange picker -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.css')); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;


        }

        .leaflet-container {
            height: 800px;
            width: 1200px;
            max-width: 100%;
            max-height: 100%;
        }



        .actions li {
            display: block;
            list-style-type: none;
        }

        .indicateur-texte {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            /* Optionnel pour le contraste */
        }
    </style>
    <style>
        .wilaya {
            width: 100% !important;
        }
    </style>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title card_title_center"><?php echo app('translator')->get('general.filter'); ?>
            </h2>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="wilaya" id="wilaya" class="form-control wilaya" runat="server">
                            <option value="">-- Select Wilaya --</option>
                            <option value="all" selected>TOUTES LES WILAYAS</option>

                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="moughata" id="moughata" class="form-control moughata">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="commune" id="commune" class="form-control commune">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="type" id="type" class="form-control type">
                            <option value="deux">Les deux</option>
                            <option value="familial">Assainissement familial</option>
                            <option value="public">Assainissement public </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(
                isset(
                $dispo,
                $invest_tot_men_latr,

                $couvertures_dernier,
                $couvertures_EDL,
                $nb_tot_men_cible_ATPC,
                $pop_tot_ciblee_ATPC,
                $nb_loc_benefic_appui_post_FDAL,
                $nb_loc_redeclench_non_cod_ANSADE,
                $nb_loc_redeclench_cod_ANSADE,
                $nb_loc_certif_FDAL,
                $nb_loc,
                $nb_loc_declench_non_cod_ANSADE,
                $nb_loc_declench_cod_ANSADE,
                $nb_wilaya,
                $nb_moughataa,
                $nb_commune,
                $nb_loc_cod_ANSADE,
                $nb_loc_non_cod_ANSADE,
                $nb_tot_loc,
                $nb_pop_rgph,
                $nb_ecole_fond,
                $nb_eleves,
                $nb_struct_sante
            )
        ): ?>
        <div id="familial">
            <h2 class="mb-2"><?php echo app('translator')->get('tb.title1'); ?></h2>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_wilaya"><?php echo e(number_format($nb_wilaya, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_wilaya'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_moughataa"> <?php echo e(number_format($nb_moughataa, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_moughataa'); ?></p>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_commune"><?php echo e(number_format($nb_commune, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_commune'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <!-- /.info-box -->
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_cod_ANSADE"><?php echo e(number_format($nb_loc_cod_ANSADE, 0, ',', ' ')); ?></h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_cod_ANSADE'); ?></p>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_non_cod_ANSADE"> <?php echo e(number_format($nb_loc_non_cod_ANSADE, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_non_cod_ANSADE'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_tot_loc"><?php echo e(number_format($nb_tot_loc, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_tot_loc'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>

                <!-- /.col -->
            </div>

            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_pop_rgph"> <?php echo e(number_format($nb_pop_rgph, 0, ',', ' ')); ?></h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_pop_rgph'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <h2 class="mb-2"><?php echo app('translator')->get('tb.title2'); ?></h2>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="pop_tot_ciblee_ATPC"> <?php echo e(number_format($pop_tot_ciblee_ATPC, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.pop_tot_ciblee_ATPC'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_tot_men_cible_ATPC"><?php echo e(number_format($nb_tot_men_cible_ATPC, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_tot_men_cible_ATPC'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="couvertures_EDL"> <?php echo e(number_format($couvertures_EDL, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.couvertures_EDL'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="couvert_initial_latr_fam">
                                <?php echo e($formatPercentSafe($couvertures_EDL, $nb_tot_men_cible_ATPC)); ?>

                                %

                            </h3>
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.couvert_initial_latr_fam'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_declench_cod_ANSADE">
                                <?php echo e(number_format($nb_loc_declench_cod_ANSADE, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_declench_cod_ANSADE'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_loc_declench_cod_ANADE">
                                <?php echo e($formatPercentSafe($nb_loc_declench_cod_ANSADE, $nb_loc_cod_ANSADE)); ?>

                                %
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_loc_declench_cod_ANADE'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <?php
                $red = $nb_loc_redeclench_non_cod_ANSADE + $nb_loc_redeclench_cod_ANSADE;
                $dec = $nb_loc_declench_cod_ANSADE + $nb_loc_declench_non_cod_ANSADE;
            ?>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_declench_non_cod_ANSADE">
                                <?php echo e(number_format($nb_loc_declench_non_cod_ANSADE, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_declench_non_cod_ANSADE'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="total_loc_declench">

                                <?php echo e(number_format($dec, 0, ',', ' ')); ?>


                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.total_loc_declench'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_tot_loc_redeclench">

                                <?php echo e(number_format($nb_loc_redeclench_non_cod_ANSADE + $nb_loc_redeclench_cod_ANSADE, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_tot_loc_redeclench'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_loc_redeclench">
                                <?php echo e($formatPercentSafe($red, $dec)); ?>

                                %
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_loc_redeclench'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_certif_FDAL"><?php echo e(number_format($nb_loc_certif_FDAL, 0, ',', ' ')); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_certif_FDAL'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_loc_certif_FDAL">
                                <?php echo e($formatPercentSafe($nb_loc_certif_FDAL, $nb_loc_declench_cod_ANSADE + $nb_loc_declench_non_cod_ANSADE)); ?>

                                %
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_loc_certif_FDAL'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_nouv_latr_construit">

                                <?php echo e(number_format($couvertures_dernier - $couvertures_EDL, 0, ',', ' ')); ?>


                            </h3>
                            <?php
                                $nb_nouv_latr_construit = $couvertures_dernier - $couvertures_EDL;
                            ?>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_nouv_latr_construit'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="couvertures_dernier">
                                <?php echo e(number_format($couvertures_dernier, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_final_latr_fam_dernier_suivi'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-6 col-sm-6 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="couvert_actuelle_latr_fam">
                                <?php echo e($formatPercentSafe($couvertures_dernier, $nb_tot_men_cible_ATPC)); ?>

                                %
                            </h3>
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.couvert_actuelle_latr_fam'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-4 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">

                            <h3 id="invest_tot_men_latr">
                                <?php if($dispo != 0): ?>
                                    <?php echo e($formatPercentSafe($invest_tot_men_latr, $dispo, 0)); ?>

                                <?php endif; ?>
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.invest_tot_men_latr'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-4 col-sm-4 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_loc_jamais_declench">
                                <?php echo e(number_format($nb_tot_loc - $nb_loc_declench_cod_ANSADE, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_loc_jamais_declench'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-md-4 col-sm-4 col-12">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_loc_jamais_declench">
                                <?php echo e($formatPercentSafe($nb_loc_cod_ANSADE - $nb_loc_declench_cod_ANSADE, $nb_loc_cod_ANSADE)); ?>

                                %
                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_loc_jamais_declench'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- BAR CHART -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo app('translator')->get('tb.chart1'); ?></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="chart1"
                                    style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <!-- DONUT CHART -->
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo app('translator')->get('tb.chart2'); ?></h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chart2"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <div class="col-md-6">
                    <!-- DONUT CHART -->
                    <div class="card card-yellow">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo app('translator')->get('tb.chart3'); ?></h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="chart3"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>


            </div>

            <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-yellow.png"><?php echo app('translator')->get('map.title'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-green.png"><?php echo app('translator')->get('map.title2'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-red.png"><?php echo app('translator')->get('map.title3'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-orange.png"><?php echo app('translator')->get('map.title4'); ?>
                | <img src="/assets/admin/dist/img/marker-icon-blue.png"><?php echo app('translator')->get('map.title5'); ?> </h2>
            <div class="row">
                <div class="col-12">
                    <div class="leaflet-container" id="map"></div>
                </div>
            </div>
        </div>
        <div id="public">
            <h2 class="mb-2"><?php echo app('translator')->get('tb.title3'); ?></h2>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_ecole_fond"><?php echo e($nb_ecole_fond); ?></h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_ecole_fond'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_eleves"> <?php echo e(number_format($nb_eleves, 0, ',', ' ')); ?></h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_eleves'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_ecole_besoin_latr_satisf">
                                <?php echo e(number_format($nb_ecole_besoin_latr_satisf, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_ecole_besoin_latr_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_ecole_besoin_latr_satisf">
                                <?php echo e($formatPercentSafe($nb_ecole_besoin_latr_satisf, $nb_ecole_fond)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_ecole_besoin_latr_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

            </div>

            <div class="row">
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_ecole_sans_latr">
                                <?php echo e(number_format($nb_ecole_sans_latr, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_ecole_sans_latr'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_ecole_sans_latr">
                                <?php echo e($formatPercentSafe($nb_ecole_sans_latr, $nb_ecole_fond)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_ecole_sans_latr'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-4 col-md-4">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nbr_ecole_cabine_a_const">

                                <?php echo e(number_format($nbr_cabine_a_const, 0, ',', ' ')); ?>

                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nbr_cabine_a_const'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_ecole_besoin_latr_non_satisf">

                                <?php echo e(number_format($nb_ecole_besoin_latr_non_satisf, 0, ',', ' ')); ?>


                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_ecole_besoin_latr_non_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_ecole_besoin_latr_non_satisf">
                                <?php echo e($formatPercentSafe($nb_ecole_besoin_latr_non_satisf, $nb_ecole_fond)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_ecole_besoin_latr_non_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

            </div>
            <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png"><?php echo app('translator')->get('map.title6'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-grey.png"><?php echo app('translator')->get('map.title7'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-violet.png"><?php echo app('translator')->get('map.title8'); ?></h2>
            <div class="row">
                <div class="col-12">
                    <div class="leaflet-container" id="map2"></div>
                </div>
            </div>
            <h2 class="mb-2"><?php echo app('translator')->get('tb.title4'); ?></h2>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_struct_sante"><?php echo e(number_format($nb_struct_sante, 0, ',', ' ')); ?></h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_struct_sante'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nbr_sante_cabine_a_const">

                                <?php echo e(number_format($sante_nbr_cabine_a_const, 0, ',', ' ')); ?>

                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nbr_cabine_a_const'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_sante_besoin_latr_satisf">

                                <?php echo e(number_format($nb_sante_besoin_latr_satisf, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_sante_besoin_latr_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_sante_besoin_latr_satisf">
                                <?php echo e($formatPercentSafe($nb_sante_besoin_latr_satisf, $nb_struct_sante)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_sante_besoin_latr_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->


            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_sante_sans_latr">

                                <?php echo e(number_format($nb_sante_sans_latr, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_sante_sans_latr'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_sante_sans_latr">
                                <?php echo e($formatPercentSafe($nb_sante_sans_latr, $nb_struct_sante)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_sante_sans_latr'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->


            </div>

            <div class="row">
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="nb_sante_besoin_latr_non_satisf">

                                <?php echo e(number_format($nb_sante_besoin_latr_non_satisf, 0, ',', ' ')); ?>

                            </h3>
                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.nb_sante_besoin_latr_non_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-6">
                    <div class="small-box bg-yellow">
                        <div class="inner">
                            <h3 id="taux_sante_besoin_latr_non_satisf">
                                <?php echo e($formatPercentSafe($nb_sante_besoin_latr_non_satisf, $nb_struct_sante)); ?>

                                %
                            </h3>

                            <p class="indicateur-texte"><?php echo app('translator')->get('tb.taux_sante_besoin_latr_non_satisf'); ?></p>
                        </div>
                    </div>
                    <!-- /.info-box -->
                </div>
                <!-- /.col -->

            </div>
            <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png"><?php echo app('translator')->get('map.title9'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-grey.png"><?php echo app('translator')->get('map.title10'); ?> | <img
                    src="/assets/admin/dist/img/marker-icon-violet.png"><?php echo app('translator')->get('map.title11'); ?></h2>
            <div class="row">
                <div class="col-12">
                    <div class="leaflet-container" id="map3"></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <h6 class="card-title card_title_center">
            No data
        </h6>
    <?php endif; ?>



<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('assets/admin/plugins/moment/moment.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/daterangepicker/daterangepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/plugins/select2/js/select2.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script src="<?php echo e(asset('assets/admin/js/BoundaryCanvas.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/dist/js/functions.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('custom'); ?>
    <script>
        Chart.register(ChartDataLabels);
        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.wilaya').select2()
            $('.moughata').select2()
            $('.commune').select2()
            $('.type').select2()
        })
    </script>
    <script>
        function safeNumber(value) {
            var number = parseFloat(value);
            return Number.isFinite(number) ? number : 0;
        }

        function safePercentValue(numerator, denominator, decimals = 1) {
            denominator = safeNumber(denominator);

            if (denominator === 0) {
                return Number(0).toFixed(decimals);
            }

            return ((safeNumber(numerator) * 100) / denominator).toFixed(decimals);
        }

        function safePercentText(numerator, denominator, decimals = 1) {
            return formatNumber(safePercentValue(numerator, denominator, decimals), decimals) + "%";
        }

        $('#wilaya').on('change', function () {
            if ($(this).val() === 'all') {
                window.location.href = "<?php echo e(route('admin.dashboard')); ?>";
            }
        });
        $(document).ready(function () {
            var chart_1 = null;
            var chart_2 = null;
            var chart_3 = null;
            var c_menages_EDL_max = <?php echo json_encode($menages_EDL_max); ?>;


            var chart3 = <?php echo json_encode($access_hygiene); ?>;
            var chart3Labels = [<?php echo json_encode(__('tb.nbr_menages_dlm_complet'), 15, 512) ?>, <?php echo json_encode(__('tb.nbr_menages_dlm_incomplet'), 15, 512) ?>,
                <?php echo json_encode(__('tb.nbr_menages_sans_dlm'), 15, 512) ?>
            ];

            var c_nbr_menages_dlm = safeNumber(chart3.nbr_menages_dlm_complet) + safeNumber(chart3.nbr_menages_dlm_incomplet) + safeNumber(chart3.nbr_menages_sans_dlm);
            var taux_menages_dlm_complet = safePercentValue(chart3.nbr_menages_dlm_complet, c_nbr_menages_dlm);
            var taux_menages_dlm_incomplet = safePercentValue(chart3.nbr_menages_dlm_incomplet, c_nbr_menages_dlm);
            var taux_menages_sans_dlm = safePercentValue(chart3.nbr_menages_sans_dlm, c_nbr_menages_dlm);


            var chart3Data = [taux_menages_dlm_complet, taux_menages_dlm_incomplet, taux_menages_sans_dlm];

            var chart3ChartCanvas = $('#chart3').get(0).getContext('2d')
            var chart3 = {
                labels: chart3Labels,
                datasets: [{
                    data: chart3Data,
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                }]
            }
            var chart3Options = {
                maintainAspectRatio: false,
                responsive: true,
            }

            chart_3 = new Chart(chart3ChartCanvas, {
                type: 'doughnut',
                data: chart3,
                options: {
                    plugins: {
                        datalabels: {
                            backgroundColor: function (context) {
                                return context.dataset.backgroundColor;
                            },
                            borderColor: 'white',
                            borderRadius: 25,
                            borderWidth: 2,
                            color: 'white',
                            display: function (context) {
                                var dataset = context.dataset;
                                var count = dataset.data.length;
                                var value = dataset.data[context.dataIndex];
                                return value > 0;
                            },
                            font: {
                                weight: 'bold',
                            },
                            formatter: function (value, context) {
                                let val = value + '%';
                                return val;
                            }
                        }
                    }
                }
            })




            var securise = <?php echo json_encode($securise); ?>;
            var limite = <?php echo json_encode($limite); ?>;
            var base = <?php echo json_encode($base); ?>;
            var non_ameliores = <?php echo json_encode($non_ameliores); ?>;
            var men_dal = <?php echo json_encode($men_dal); ?>;

            var taux_securise = safePercentValue(securise, c_menages_EDL_max);
            var taux_limite = safePercentValue(limite, c_menages_EDL_max);

            var taux_non_ameliores = safePercentValue(non_ameliores, c_menages_EDL_max);
            var taux_men_dal = safePercentValue(men_dal, c_menages_EDL_max);

            var taux_base = (100 - taux_securise - taux_limite - taux_non_ameliores - taux_men_dal).toFixed(1);

            var chart2Labels = [<?php echo json_encode(__('tb.securise'), 15, 512) ?>,
                <?php echo json_encode(__('tb.base'), 15, 512) ?>,
                <?php echo json_encode(__('tb.limite'), 15, 512) ?>,

                <?php echo json_encode(__('tb.non_ameliores'), 15, 512) ?>, <?php echo json_encode(__('tb.men_dal'), 15, 512) ?>
            ];
            var chart2Data = [taux_securise, taux_base, taux_limite, taux_non_ameliores, taux_men_dal];

            var chart2ChartCanvas = $('#chart2').get(0).getContext('2d')
            var chart2 = {
                labels: chart2Labels,
                datasets: [{
                    data: chart2Data,
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
                }]
            }
            var chart2Options = {
                maintainAspectRatio: false,
                responsive: true,
            }


            chart_2 = new Chart(chart2ChartCanvas, {
                type: 'doughnut',
                data: chart2,
                options: {
                    plugins: {
                        datalabels: {
                            backgroundColor: function (context) {
                                return context.dataset.backgroundColor;
                            },
                            borderColor: 'white',
                            borderRadius: 25,
                            borderWidth: 2,
                            color: 'white',
                            display: function (context) {
                                var dataset = context.dataset;
                                var count = dataset.data.length;
                                var value = dataset.data[context.dataIndex];
                                return value > 0;
                            },
                            font: {
                                weight: 'bold',
                            },
                            formatter: function (value, context) {
                                let val = value + '%';
                                return val;
                            }
                        }
                    }
                }
            })
            var chart1 = <?php echo json_encode($nb_loc); ?>;
            var c_nb_loc_declench = <?php echo json_encode($nb_loc_declench); ?>;

            var labels = [];
            var data1 = [];
            var data2 = [];
            $.each(chart1, function (key, value) {
                labels.push(value.year);
                data1.push(value.localites_1);
                data2.push(value.localites_3);
                // data2.push((value.localites_3 * 100 / c_nb_loc_declench).toFixed(1));
                //data1.push((value.localites_1 * 100 / c_nb_loc_declench).toFixed(1));

            })
            var chart1 = {
                labels: labels,
                datasets: [{
                    label: <?php echo json_encode(__('tb.op_declechement'), 15, 512) ?>,
                    backgroundColor: 'rgba(245, 105, 84, 1)',
                    borderColor: 'rgba(60,141,188,0.8)',
                    pointRadius: false,
                    pointColor: '#3b8bba',
                    pointStrokeColor: 'rgba(60,141,188,1)',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(60,141,188,1)',
                    data: data1
                },
                {
                    label: <?php echo json_encode(__('tb.op_fdal'), 15, 512) ?>,
                    backgroundColor: 'rgba(0, 166, 90, 1)',
                    borderColor: 'rgba(210, 214, 222, 1)',
                    pointRadius: false,
                    pointColor: 'rgba(210, 214, 222, 1)',
                    pointStrokeColor: '#c1c7d1',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(220,220,220,1)',
                    data: data2
                },
                ]
            }
            var chart1Canvas = $('#chart1').get(0).getContext('2d')
            var chart1Data = $.extend(true, {}, chart1)


            var chart1Options = {
                responsive: true,
                maintainAspectRatio: false,
                datasetFill: false,

            }


            chart_1 = new Chart(chart1Canvas, {
                type: 'bar',
                data: chart1Data,
                options: {
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#f39c12',
                            display: function (context) {
                                return context.dataset.data[context.dataIndex] > 0;
                            },
                            font: {
                                weight: 'bold',
                            },
                            formatter: function (value, context) {
                                return value;
                            }
                        }
                    }
                }
            })

            $('#type').on('change', function () {

                var type = this.value;

                if (type === 'familial') {
                    $("#familial").show();
                    $("#public").hide();

                } else if (type === 'public') {
                    $("#familial").hide();
                    $("#public").show();

                } else if (type === 'deux') {
                    $("#familial").show();
                    $("#public").show();

                }

            });
            $('#wilaya').on('change', function () {
                $('#type').val(null).trigger('change');

                var idWilaya = this.value;

                $("#moughata").html('');
                $.ajax({

                    url: "<?php echo e(url('admin/fetch_general_wilaya')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (result) {


                        var chart3_wilaya = result.access_hygiene;

                        var chart3Labels_wilaya = [<?php echo json_encode(__('tb.nbr_menages_dlm_complet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_dlm_incomplet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_sans_dlm'), 15, 512) ?>
                        ];
                        var menages_dernier = parseFloat(chart3.nbr_menages_dlm_complet) + parseFloat(chart3.nbr_menages_dlm_incomplet) + parseFloat(chart3.nbr_menages_sans_dlm);
                        var nbr_menages_dlm_complet = (chart3_wilaya.nbr_menages_dlm_complet *
                            100 / menages_dernier).toFixed(1);
                        var nbr_menages_dlm_incomplet = (chart3_wilaya
                            .nbr_menages_dlm_incomplet * 100 / menages_dernier)
                            .toFixed(1);
                        var nbr_menages_sans_dlm = (chart3_wilaya.nbr_menages_sans_dlm * 100 /
                            menages_dernier).toFixed(1);
                        var chart3Data_wilaya = [nbr_menages_dlm_complet,
                            nbr_menages_dlm_incomplet, nbr_menages_sans_dlm
                        ];
                        var chart3ChartCanvas_wilaya = $('#chart3').get(0).getContext('2d')
                        var chart3_wilaya = {
                            labels: chart3Labels_wilaya,
                            datasets: [{
                                data: chart3Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart3Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }

                        if (chart_3) {
                            chart_3.data.labels = chart3Labels_wilaya;
                            chart_3.data.datasets[0].data = chart3Data_wilaya;

                            chart_3.update();
                        }



                        var securise = (securise * 100 / c_menages_EDL_max).toFixed(1);
                        var limite = (limite * 100 / c_menages_EDL_max).toFixed(
                            1);

                        var non_ameliores = (non_ameliores * 100 / c_menages_EDL_max).toFixed(1);
                        var men_dal = (men_dal * 100 / c_menages_EDL_max).toFixed(1);

                        var base = (100 - securise - limite - men_dal - non_ameliores).toFixed(1);
                        var chart2Labels_wilaya = [<?php echo json_encode(__('tb.securise'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.base'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.limite'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.non_ameliores'), 15, 512) ?>, <?php echo json_encode(__('tb.men_dal'), 15, 512) ?>
                        ];
                        var chart2Data_wilaya = [securise, base, limite, non_ameliores,
                            men_dal
                        ];

                        var chart2ChartCanvas_wilaya = $('#chart2').get(0).getContext('2d')
                        var chart2_wilaya = {
                            labels: chart2Labels_wilaya,
                            datasets: [{
                                data: chart2Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart2Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }

                        if (chart_2) {
                            chart_2.data.labels = chart2Labels_wilaya;
                            chart_2.data.datasets[0].data = chart2Data_wilaya;

                            chart_2.update();
                        }

                        var chart1_wilaya = result.nb_loc;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function (key, value) {
                            labels.push(value.year);
                            data1.push((value.localites_1 * 100 / result
                                .nb_loc_declench)
                                .toFixed(1));
                            data2.push((value.localites_3 * 100 / result
                                .nb_loc_declench)
                                .toFixed(1));
                        })


                        if (chart_1) {
                            chart_1.data.labels = labels;
                            chart_1.data.datasets[0].data = data1;
                            chart_1.data.datasets[0].label = <?php echo json_encode(__('tb.op_declechement'), 15, 512) ?>;
                            chart_1.data.datasets[1].data = data2;
                            chart_1.data.datasets[1].label = <?php echo json_encode(__('tb.op_fdal'), 15, 512) ?>;
                            chart_1.update();

                        }

                        const nb_wilaya = document.getElementById("nb_wilaya");
                        if (nb_wilaya) {
                            nb_wilaya.textContent = formatNumber(result.nb_wilaya,
                                0);
                        }
                        const nb_moughataa = document.getElementById("nb_moughataa");
                        if (nb_moughataa) {
                            nb_moughataa.textContent = formatNumber(result.nb_moughataa,
                                0);
                        }
                        const nb_commune = document.getElementById("nb_commune");
                        if (nb_commune) {
                            nb_commune.textContent = formatNumber(result.nb_commune,
                                0);
                        }
                        const nb_loc_cod_ANSADE = document.getElementById("nb_loc_cod_ANSADE");
                        if (nb_loc_cod_ANSADE) {
                            nb_loc_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_cod_ANSADE,
                                0);

                        }
                        const nb_loc_non_cod_ANSADE = document.getElementById(
                            "nb_loc_non_cod_ANSADE");
                        if (nb_loc_non_cod_ANSADE) {
                            nb_loc_non_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_non_cod_ANSADE,
                                0);

                        }
                        const nb_tot_loc = document.getElementById("nb_tot_loc");
                        if (nb_tot_loc) {
                            nb_tot_loc.textContent = formatNumber(result.nb_tot_loc,
                                0);

                        }


                        const nb_pop_rgph = document.getElementById("nb_pop_rgph");
                        if (nb_pop_rgph) {
                            nb_pop_rgph.textContent = formatNumber(result.nb_pop_rgph,
                                0);
                        }
                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = formatNumber(result.nb_eleves,
                                0);

                        }
                        const nb_ecole_fond = document.getElementById("nb_ecole_fond");
                        if (nb_ecole_fond) {
                            nb_ecole_fond.textContent = formatNumber(result.nb_ecole_fond,
                                0);
                        }
                        const nb_struct_sante = document.getElementById("nb_struct_sante");
                        if (nb_struct_sante) {
                            nb_struct_sante.textContent = formatNumber(result.nb_struct_sante,
                                0);
                        }
                        const pop_tot_ciblee_ATPC = document.getElementById(
                            "pop_tot_ciblee_ATPC");
                        if (pop_tot_ciblee_ATPC) {
                            pop_tot_ciblee_ATPC.textContent = formatNumber(result
                                .pop_tot_ciblee_ATPC,
                                0);

                        }
                        const nb_tot_men_cible_ATPC = document.getElementById(
                            "nb_tot_men_cible_ATPC");
                        if (nb_tot_men_cible_ATPC) {
                            nb_tot_men_cible_ATPC.textContent = formatNumber(result
                                .nb_tot_men_cible_ATPC,
                                0);

                        }


                        const nb_loc_declench_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_cod_ANSADE");
                        if (nb_loc_declench_cod_ANSADE) {
                            nb_loc_declench_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE,
                                0);
                        }

                        const taux_loc_declench_cod_ANADE = document.getElementById(
                            "taux_loc_declench_cod_ANADE");
                        if (taux_loc_declench_cod_ANADE) {
                            taux_loc_declench_cod_ANADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE * 100 / result
                                    .nb_loc_cod_ANSADE,
                                1) + "%";


                        }
                        const nb_loc_certif_FDAL = document.getElementById(
                            "nb_loc_certif_FDAL");
                        if (nb_loc_certif_FDAL) {
                            nb_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL,
                                0);
                        }

                        const taux_loc_certif_FDAL = document.getElementById(
                            "taux_loc_certif_FDAL");
                        if (taux_loc_certif_FDAL) {
                            taux_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL * 100 / (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_non_cod_ANSADE),
                                1) + "%";
                        }


                        const invest_tot_men_latr = document.getElementById(
                            "invest_tot_men_latr");
                        if (invest_tot_men_latr) {
                            invest_tot_men_latr.textContent = formatNumber(result
                                .invest_tot_men_latr,
                                0);

                        }

                        const nb_tot_loc_redeclench = document.getElementById(
                            "nb_tot_loc_redeclench");
                        if (nb_tot_loc_redeclench) {
                            nb_tot_loc_redeclench.textContent = formatNumber(result
                                .nb_loc_redeclench_cod_ANSADE,
                                0);

                        }

                        const nb_loc_declench_non_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_non_cod_ANSADE");
                        if (nb_loc_declench_non_cod_ANSADE) {
                            nb_loc_declench_non_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE,
                                0);

                        }
                        const total_loc_declench = document.getElementById(
                            "total_loc_declench");
                        if (total_loc_declench) {
                            total_loc_declench.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE + result
                                    .nb_loc_declench_cod_ANSADE,
                                0);
                        }
                        const taux_loc_redeclench = document.getElementById(
                            "taux_loc_redeclench");
                        if (taux_loc_redeclench) {
                            taux_loc_redeclench.textContent = formatNumber((result
                                .nb_loc_redeclench_cod_ANSADE + result
                                    .nb_loc_redeclench_non_cod_ANSADE) * 100 / (result
                                        .nb_loc_declench_cod_ANSADE + result
                                            .nb_loc_declench_non_cod_ANSADE),
                                1) + "%";

                        }
                        const nb_loc_jamais_declench = document.getElementById(
                            "nb_loc_jamais_declench");
                        if (nb_loc_jamais_declench) {
                            nb_loc_jamais_declench.textContent = formatNumber(result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE), 0);
                        }

                        const taux_loc_jamais_declench = document.getElementById(
                            "taux_loc_jamais_declench");
                        if (taux_loc_jamais_declench) {
                            taux_loc_jamais_declench.textContent = formatNumber((result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE)) * 100 / result
                                    .nb_tot_loc, 1) + "%";
                        }
                        const nb_nouv_latr_construit = document.getElementById(
                            "nb_nouv_latr_construit");
                        if (nb_nouv_latr_construit) {
                            nb_nouv_latr_construit.textContent = formatNumber(result
                                .nb_nouv_latr_construit, 0);
                        }
                        const couvertures_dernier = document.getElementById(
                            "couvertures_dernier");
                        if (couvertures_dernier) {
                            couvertures_dernier.textContent = formatNumber(result
                                .couvertures_dernier, 0);
                        }
                        const couvert_actuelle_latr_fam = document.getElementById(
                            "couvert_actuelle_latr_fam");
                        if (couvert_actuelle_latr_fam) {
                            couvert_actuelle_latr_fam.textContent = formatNumber(result
                                .couvertures_dernier * 100 / result.menages_dernier, 1) +
                                "%";
                        }




                        const couvertures_EDL = document.getElementById("couvertures_EDL");
                        if (couvertures_EDL) {
                            couvertures_EDL.textContent = formatNumber(result
                                .couvertures_EDL, 0);
                        }
                        const couvert_initial_latr_fam = document.getElementById(
                            "couvert_initial_latr_fam");
                        if (couvert_initial_latr_fam) {
                            couvert_initial_latr_fam.textContent = formatNumber(result
                                .couvertures_EDL * 100 / result.menages_EDL, 1) + "%";
                        }




                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf, 0);

                        }
                        const taux_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_satisf");
                        if (taux_ecole_besoin_latr_satisf) {
                            taux_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf *
                                100 / result.nb_ecole_fond, 1
                            ) + '%';
                        }



                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_sans_latr, 0);
                        }
                        const taux_ecole_sans_latr = document.getElementById(
                            "taux_ecole_sans_latr");
                        if (taux_ecole_sans_latr) {
                            taux_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_sans_latr * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }
                        const nbr_ecole_cabine_a_const = document.getElementById(
                            "nbr_ecole_cabine_a_const");
                        if (nbr_ecole_cabine_a_const) {
                            nbr_ecole_cabine_a_const.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }

                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf, 0);
                        }

                        const taux_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_non_satisf");
                        if (taux_ecole_besoin_latr_non_satisf) {
                            taux_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }




                        //});
                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf, 0);
                        }
                        const taux_sante_besoin_latr_satisf = document.getElementById(
                            "taux_sante_besoin_latr_satisf");
                        if (taux_sante_besoin_latr_satisf) {
                            taux_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf *
                                100 /
                                result.nb_struct_sante, 1) + '%';
                        }


                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr, 0);
                        }

                        const taux_sante_sans_latr = document.getElementById(
                            "taux_sante_sans_latr");
                        if (taux_sante_sans_latr) {
                            taux_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr * 100 /
                                result.nb_struct_sante, 1) + "%";
                        }

                        const nbr_sante_cabine_a_const = document.getElementById(
                            "nbr_sante_cabine_a_const");
                        if (nbr_sante_cabine_a_const) {
                            nbr_sante_cabine_a_const.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }
                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_non_satisf
                            );
                        }

                        const taux_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_sante_besoin_latr_non_satisf");
                        if (taux_sante_besoin_latr_non_satisf) {
                            taux_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf
                                .nb_sante_besoin_latr_non_satisf * 100 / result
                                    .nb_struct_sante, 1) + "%";
                        }


                    }

                });
                $.ajax({

                    url: "<?php echo e(url('admin/api/fetch-moughatas')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function (key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                    }

                });


            });
            $('#moughata').on('change', function () {
                $('#type').val(null).trigger('change');
                var idMoughata = this.value;
                var idWilaya = $("#wilaya").val();

                $("#commune").html('');
                $.ajax({

                    url: "<?php echo e(url('admin/fetch_general_wilaya')); ?>",

                    type: "POST",

                    data: {
                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (result) {
                        var chart3_wilaya = result.access_hygiene;

                        var chart3Labels_wilaya = [<?php echo json_encode(__('tb.nbr_menages_dlm_complet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_dlm_incomplet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_sans_dlm'), 15, 512) ?>
                        ];
                        var menages_dernier = parseFloat(chart3.nbr_menages_dlm_complet) + parseFloat(chart3.nbr_menages_dlm_incomplet) + parseFloat(chart3.nbr_menages_sans_dlm);
                        var nbr_menages_dlm_complet = (chart3_wilaya.nbr_menages_dlm_complet *
                            100 / menages_dernier).toFixed(1);
                        var nbr_menages_dlm_incomplet = (chart3_wilaya
                            .nbr_menages_dlm_incomplet * 100 / menages_dernier)
                            .toFixed(1);
                        var nbr_menages_sans_dlm = (chart3_wilaya.nbr_menages_sans_dlm * 100 /
                            menages_dernier).toFixed(1);
                        var chart3Data_wilaya = [nbr_menages_dlm_complet,
                            nbr_menages_dlm_incomplet, nbr_menages_sans_dlm
                        ];

                        var chart3ChartCanvas_wilaya = $('#chart3').get(0).getContext('2d')
                        var chart3_wilaya = {
                            labels: chart3Labels_wilaya,
                            datasets: [{
                                data: chart3Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart3Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        if (chart_3) {
                            chart_3.destroy();
                        }

                        chart_3 = new Chart(chart3ChartCanvas_wilaya, {
                            type: 'doughnut',
                            data: chart3_wilaya,
                            options: chart3Options_wilaya
                        })



                        var securise = (securise * 100 / c_menages_EDL_max).toFixed(1);
                        var limite = (limite * 100 / c_menages_EDL_max).toFixed(
                            1);

                        var non_ameliores = (non_ameliores * 100 / c_menages_EDL_max).toFixed(1);
                        var men_dal = (men_dal * 100 / c_menages_EDL_max).toFixed(1);
                        var base = (100 - securise - limite - men_dal - non_ameliores).toFixed(1);

                        var chart2Labels_wilaya = [<?php echo json_encode(__('tb.securise'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.base'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.limite'), 15, 512) ?>,

                            <?php echo json_encode(__('tb.non_ameliores'), 15, 512) ?>, <?php echo json_encode(__('tb.men_dal'), 15, 512) ?>
                        ];
                        var chart2Data_wilaya = [securise, base, limite, non_ameliores,
                            men_dal
                        ];

                        var chart2ChartCanvas_wilaya = $('#chart2').get(0).getContext('2d')
                        var chart2_wilaya = {
                            labels: chart2Labels_wilaya,
                            datasets: [{
                                data: chart2Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart2Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        if (chart_2) {
                            chart_2.destroy();
                        }
                        chart_2 = new Chart(chart2ChartCanvas_wilaya, {
                            type: 'doughnut',
                            data: chart2_wilaya,
                            options: chart2Options_wilaya
                        })

                        var chart1_wilaya = result.nb_loc;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function (key, value) {
                            labels.push(value.year);
                            data1.push(value.localites_1);
                            data2.push(value.localites_3);
                        })
                        var chart1_wilaya = {
                            labels: labels,
                            datasets: [{
                                label: <?php echo json_encode(__('tb.op_declechement'), 15, 512) ?>,
                                backgroundColor: 'rgba(60,141,188,0.9)',
                                borderColor: 'rgba(60,141,188,0.8)',
                                pointRadius: false,
                                pointColor: '#3b8bba',
                                pointStrokeColor: 'rgba(60,141,188,1)',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(60,141,188,1)',
                                data: data1
                            },
                            {
                                label: <?php echo json_encode(__('tb.op_fdal'), 15, 512) ?>,
                                backgroundColor: 'rgba(210, 214, 222, 1)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                pointRadius: false,
                                pointColor: 'rgba(210, 214, 222, 1)',
                                pointStrokeColor: '#c1c7d1',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(220,220,220,1)',
                                data: data2
                            },
                            ]
                        }

                        var chart1Canvas_wilaya = $('#chart1').get(0).getContext('2d')
                        var chart1Data_wilaya = $.extend(true, {}, chart1_wilaya)
                        var temp0 = chart1_wilaya.datasets[0]
                        var temp1 = chart1_wilaya.datasets[1]
                        chart1Data_wilaya.datasets[0] = temp1
                        chart1Data_wilaya.datasets[1] = temp0

                        var chart1Options_wilaya = {
                            responsive: true,
                            maintainAspectRatio: false,
                            datasetFill: false
                        }
                        if (chart_1) {
                            chart_1.destroy();
                        }

                        chart_1 = new Chart(chart1Canvas_wilaya, {
                            type: 'bar',
                            data: chart1Data_wilaya,
                            options: chart1Options_wilaya
                        })


                        const nb_wilaya = document.getElementById("nb_wilaya");
                        if (nb_wilaya) {
                            nb_wilaya.textContent = result.nb_wilaya;
                        }
                        const nb_moughataa = document.getElementById("nb_moughataa");
                        if (nb_moughataa) {
                            nb_moughataa.textContent = result.nb_moughataa;
                        }
                        const nb_commune = document.getElementById("nb_commune");
                        if (nb_commune) {
                            nb_commune.textContent = result.nb_commune;
                        }
                        const nb_loc_cod_ANSADE = document.getElementById("nb_loc_cod_ANSADE");
                        if (nb_loc_cod_ANSADE) {
                            nb_loc_cod_ANSADE.textContent = result.nb_loc_cod_ANSADE;
                        }
                        const nb_loc_non_cod_ANSADE = document.getElementById(
                            "nb_loc_non_cod_ANSADE");
                        if (nb_loc_non_cod_ANSADE) {
                            nb_loc_non_cod_ANSADE.textContent = result.nb_loc_non_cod_ANSADE;
                        }
                        const nb_tot_loc = document.getElementById("nb_tot_loc");
                        if (nb_tot_loc) {
                            nb_tot_loc.textContent = result.nb_tot_loc;
                        }
                        const nb_pop_rgph = document.getElementById("nb_pop_rgph");
                        if (nb_pop_rgph) {
                            nb_pop_rgph.textContent = result.nb_pop_rgph;
                        }
                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = result.nb_eleves;
                        }
                        const nb_ecole_fond = document.getElementById("nb_ecole_fond");
                        if (nb_ecole_fond) {
                            nb_ecole_fond.textContent = result.nb_ecole_fond;
                        }
                        const nb_struct_sante = document.getElementById("nb_struct_sante");
                        if (nb_struct_sante) {
                            nb_struct_sante.textContent = result.nb_struct_sante;
                        }

                        const pop_tot_ciblee_ATPC = document.getElementById(
                            "pop_tot_ciblee_ATPC");
                        if (pop_tot_ciblee_ATPC) {
                            pop_tot_ciblee_ATPC.textContent = formatNumber(result
                                .pop_tot_ciblee_ATPC,
                                0);

                        }
                        const nb_tot_men_cible_ATPC = document.getElementById(
                            "nb_tot_men_cible_ATPC");
                        if (nb_tot_men_cible_ATPC) {
                            nb_tot_men_cible_ATPC.textContent = formatNumber(result
                                .nb_tot_men_cible_ATPC,
                                0);

                        }


                        const nb_loc_declench_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_cod_ANSADE");
                        if (nb_loc_declench_cod_ANSADE) {
                            nb_loc_declench_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE,
                                0);
                        }

                        const taux_loc_declench_cod_ANADE = document.getElementById(
                            "taux_loc_declench_cod_ANADE");
                        if (taux_loc_declench_cod_ANADE) {
                            taux_loc_declench_cod_ANADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE * 100 / result
                                    .nb_loc_cod_ANSADE,
                                1) + "%";


                        }
                        const nb_loc_certif_FDAL = document.getElementById(
                            "nb_loc_certif_FDAL");
                        if (nb_loc_certif_FDAL) {
                            nb_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL,
                                0);
                        }

                        const taux_loc_certif_FDAL = document.getElementById(
                            "taux_loc_certif_FDAL");
                        if (taux_loc_certif_FDAL) {
                            taux_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL * 100 / (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_non_cod_ANSADE),
                                1) + "%";
                        }


                        const invest_tot_men_latr = document.getElementById(
                            "invest_tot_men_latr");
                        if (invest_tot_men_latr) {
                            invest_tot_men_latr.textContent = formatNumber(result
                                .invest_tot_men_latr,
                                0);

                        }

                        const nb_loc_declench_non_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_non_cod_ANSADE");
                        if (nb_loc_declench_non_cod_ANSADE) {
                            nb_loc_declench_non_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE,
                                0);

                        }
                        const total_loc_declench = document.getElementById(
                            "total_loc_declench");
                        if (total_loc_declench) {
                            total_loc_declench.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE + result
                                    .nb_loc_declench_cod_ANSADE,
                                0);
                        }
                        const nb_tot_loc_redeclench = document.getElementById(
                            "nb_tot_loc_redeclench");
                        if (nb_tot_loc_redeclench) {
                            nb_tot_loc_redeclench.textContent = formatNumber(result
                                .nb_loc_redeclench_cod_ANSADE,
                                0);

                        }

                        const taux_loc_redeclench = document.getElementById(
                            "taux_loc_redeclench");
                        if (taux_loc_redeclench) {
                            taux_loc_redeclench.textContent = formatNumber((result
                                .nb_loc_redeclench_cod_ANSADE + result
                                    .nb_loc_redeclench_non_cod_ANSADE) * 100 / (result
                                        .nb_loc_declench_cod_ANSADE + result
                                            .nb_loc_declench_non_cod_ANSADE),
                                1) + "%";

                        }
                        const nb_loc_jamais_declench = document.getElementById(
                            "nb_loc_jamais_declench");
                        if (nb_loc_jamais_declench) {
                            nb_loc_jamais_declench.textContent = formatNumber(result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE), 0);
                        }

                        const taux_loc_jamais_declench = document.getElementById(
                            "taux_loc_jamais_declench");
                        if (taux_loc_jamais_declench) {
                            taux_loc_jamais_declench.textContent = formatNumber((result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE)) * 100 / result
                                    .nb_tot_loc, 1) + "%";
                        }
                        const nb_nouv_latr_construit = document.getElementById(
                            "nb_nouv_latr_construit");
                        if (nb_nouv_latr_construit) {
                            nb_nouv_latr_construit.textContent = formatNumber(result
                                .nb_nouv_latr_construit, 0);
                        }
                        const couvertures_dernier = document.getElementById(
                            "couvertures_dernier");
                        if (couvertures_dernier) {
                            couvertures_dernier.textContent = formatNumber(result
                                .couvertures_dernier, 0);
                        }
                        const couvert_actuelle_latr_fam = document.getElementById(
                            "couvert_actuelle_latr_fam");
                        if (couvert_actuelle_latr_fam) {
                            couvert_actuelle_latr_fam.textContent = formatNumber(result
                                .couvertures_dernier * 100 / result.menages_dernier, 1) +
                                "%";
                        }



                        const couvertures_EDL = document.getElementById("couvertures_EDL");
                        if (couvertures_EDL) {
                            couvertures_EDL.textContent = formatNumber(result
                                .couvertures_EDL, 0);
                        }
                        const couvert_initial_latr_fam = document.getElementById(
                            "couvert_initial_latr_fam");
                        if (couvert_initial_latr_fam) {
                            couvert_initial_latr_fam.textContent = formatNumber(result
                                .couvertures_EDL * 100 / result.menages_EDL, 1) + "%";
                        }




                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf, 0);

                        }
                        const taux_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_satisf");
                        if (taux_ecole_besoin_latr_satisf) {
                            taux_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf *
                                100 / result.nb_ecole_fond, 1
                            ) + '%';
                        }


                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_sans_latr, 0);
                        }
                        const taux_ecole_sans_latr = document.getElementById(
                            "taux_ecole_sans_latr");
                        if (taux_ecole_sans_latr) {
                            taux_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_sans_latr * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }
                        const nbr_ecole_cabine_a_const = document.getElementById(
                            "nbr_ecole_cabine_a_const");
                        if (nbr_ecole_cabine_a_const) {
                            nbr_ecole_cabine_a_const.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }

                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf, 0);
                        }

                        const taux_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_non_satisf");
                        if (taux_ecole_besoin_latr_non_satisf) {
                            taux_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }




                        //});
                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf, 0);
                        }
                        const taux_sante_besoin_latr_satisf = document.getElementById(
                            "taux_sante_besoin_latr_satisf");
                        if (taux_sante_besoin_latr_satisf) {
                            taux_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf *
                                100 /
                                result.nb_struct_sante, 1) + '%';
                        }

                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr, 0);
                        }

                        const taux_sante_sans_latr = document.getElementById(
                            "taux_sante_sans_latr");
                        if (taux_sante_sans_latr) {
                            taux_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr * 100 /
                                result.nb_struct_sante, 1) + "%";
                        }
                        const nbr_sante_cabine_a_const = document.getElementById(
                            "nbr_sante_cabine_a_const");
                        if (nbr_sante_cabine_a_const) {
                            nbr_sante_cabine_a_const.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }

                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_non_satisf
                            );
                        }

                        const taux_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_sante_besoin_latr_non_satisf");
                        if (taux_sante_besoin_latr_non_satisf) {
                            taux_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf
                                .nb_sante_besoin_latr_non_satisf * 100 / result
                                    .nb_struct_sante, 1) + "%";
                        }





                        //});


                    }

                });
                $.ajax({

                    url: "<?php echo e(url('admin/api/fetch-communes')); ?>",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (res) {

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function (key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                    }

                });

            });
            $('#commune').on('change', function () {
                $('#type').val(null).trigger('change');
                var idCommune = this.value;
                var idWilaya = $("#wilaya").val();
                var idMoughata = $("#moughata").val();


                $.ajax({

                    url: "<?php echo e(url('admin/fetch_general_wilaya')); ?>",

                    type: "POST",

                    data: {
                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (result) {

                        var chart3_wilaya = result.access_hygiene;

                        var chart3Labels_wilaya = [<?php echo json_encode(__('tb.nbr_menages_dlm_complet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_dlm_incomplet'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.nbr_menages_sans_dlm'), 15, 512) ?>
                        ];
                        var menages_dernier = parseFloat(chart3.nbr_menages_dlm_complet) + parseFloat(chart3.nbr_menages_dlm_incomplet) + parseFloat(chart3.nbr_menages_sans_dlm);
                        var nbr_menages_dlm_complet = (chart3_wilaya.nbr_menages_dlm_complet *
                            100 / menages_dernier).toFixed(1);
                        var nbr_menages_dlm_incomplet = (chart3_wilaya
                            .nbr_menages_dlm_incomplet * 100 / menages_dernier)
                            .toFixed(1);
                        var nbr_menages_sans_dlm = (chart3_wilaya.nbr_menages_sans_dlm * 100 /
                            menages_dernier).toFixed(1);

                        var chart3Data_wilaya = [nbr_menages_dlm_complet,
                            nbr_menages_dlm_complet, nbr_menages_sans_dlm
                        ];

                        var chart3ChartCanvas_wilaya = $('#chart3').get(0).getContext('2d')
                        var chart3_wilaya = {
                            labels: chart3Labels_wilaya,
                            datasets: [{
                                data: chart3Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart3Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        if (chart_3) {
                            chart_3.destroy();
                        }

                        chart_3 = new Chart(chart3ChartCanvas_wilaya, {
                            type: 'doughnut',
                            data: chart3_wilaya,
                            options: chart3Options_wilaya
                        })



                        var securise = (securise * 100 / c_menages_EDL_max).toFixed(1);
                        var limite = (limite * 100 / c_menages_EDL_max).toFixed(
                            1);

                        var non_ameliores = (non_ameliores * 100 / c_menages_EDL_max).toFixed(1);
                        var men_dal = (men_dal * 100 / c_menages_EDL_max).toFixed(1);

                        var base = (100 - securise - limite - men_dal - non_ameliores).toFixed(1);
                        var chart2Labels_wilaya = [<?php echo json_encode(__('tb.securise'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.base'), 15, 512) ?>,
                            <?php echo json_encode(__('tb.limite'), 15, 512) ?>,

                            <?php echo json_encode(__('tb.non_ameliores'), 15, 512) ?>, <?php echo json_encode(__('tb.men_dal'), 15, 512) ?>
                        ];
                        var chart2Data_wilaya = [securise, limite, base, non_ameliores,
                            men_dal
                        ];

                        var chart2ChartCanvas_wilaya = $('#chart2').get(0).getContext('2d')
                        var chart2_wilaya = {
                            labels: chart2Labels_wilaya,
                            datasets: [{
                                data: chart2Data_wilaya,
                                backgroundColor: ['#f56954', '#00a65a', '#f39c12',
                                    '#00c0ef', '#3c8dbc', '#d2d6de'
                                ],
                            }]
                        }
                        var chart2Options_wilaya = {
                            maintainAspectRatio: false,
                            responsive: true,
                        }
                        if (chart_2) {
                            chart_2.destroy();
                        }
                        chart_2 = new Chart(chart2ChartCanvas_wilaya, {
                            type: 'doughnut',
                            data: chart2_wilaya,
                            options: chart2Options_wilaya
                        })

                        var chart1_wilaya = result.nb_loc;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function (key, value) {
                            labels.push(value.year);
                            data1.push(value.localites_1);
                            data2.push(value.localites_3);
                        })
                        var chart1_wilaya = {
                            labels: labels,
                            datasets: [{
                                label: <?php echo json_encode(__('tb.op_declechement'), 15, 512) ?>,
                                backgroundColor: 'rgba(60,141,188,0.9)',
                                borderColor: 'rgba(60,141,188,0.8)',
                                pointRadius: false,
                                pointColor: '#3b8bba',
                                pointStrokeColor: 'rgba(60,141,188,1)',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(60,141,188,1)',
                                data: data1
                            },
                            {
                                label: <?php echo json_encode(__('tb.op_fdal'), 15, 512) ?>,
                                backgroundColor: 'rgba(210, 214, 222, 1)',
                                borderColor: 'rgba(210, 214, 222, 1)',
                                pointRadius: false,
                                pointColor: 'rgba(210, 214, 222, 1)',
                                pointStrokeColor: '#c1c7d1',
                                pointHighlightFill: '#fff',
                                pointHighlightStroke: 'rgba(220,220,220,1)',
                                data: data2
                            },
                            ]
                        }

                        var chart1Canvas_wilaya = $('#chart1').get(0).getContext('2d')
                        var chart1Data_wilaya = $.extend(true, {}, chart1_wilaya)
                        var temp0 = chart1_wilaya.datasets[0]
                        var temp1 = chart1_wilaya.datasets[1]
                        chart1Data_wilaya.datasets[0] = temp1
                        chart1Data_wilaya.datasets[1] = temp0

                        var chart1Options_wilaya = {
                            responsive: true,
                            maintainAspectRatio: false,
                            datasetFill: false
                        }

                        if (chart_1) {
                            chart_1.destroy();
                        }
                        chart_1 = new Chart(chart1Canvas_wilaya, {
                            type: 'bar',
                            data: chart1Data_wilaya,
                            options: chart1Options_wilaya
                        })

                        const nb_wilaya = document.getElementById("nb_wilaya");
                        if (nb_wilaya) {
                            nb_wilaya.textContent = result.nb_wilaya;
                        }
                        const nb_moughataa = document.getElementById("nb_moughataa");
                        if (nb_moughataa) {
                            nb_moughataa.textContent = result.nb_moughataa;
                        }
                        const nb_commune = document.getElementById("nb_commune");
                        if (nb_commune) {
                            nb_commune.textContent = result.nb_commune;
                        }
                        const nb_loc_cod_ANSADE = document.getElementById("nb_loc_cod_ANSADE");
                        if (nb_loc_cod_ANSADE) {
                            nb_loc_cod_ANSADE.textContent = result.nb_loc_cod_ANSADE;
                        }
                        const nb_loc_non_cod_ANSADE = document.getElementById(
                            "nb_loc_non_cod_ANSADE");
                        if (nb_loc_non_cod_ANSADE) {
                            nb_loc_non_cod_ANSADE.textContent = result.nb_loc_non_cod_ANSADE;
                        }
                        const nb_tot_loc = document.getElementById("nb_tot_loc");
                        if (nb_tot_loc) {
                            nb_tot_loc.textContent = result.nb_tot_loc;
                        }
                        const nb_pop_rgph = document.getElementById("nb_pop_rgph");
                        if (nb_pop_rgph) {
                            nb_pop_rgph.textContent = result.nb_pop_rgph;
                        }
                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = result.nb_eleves;
                        }
                        const nb_ecole_fond = document.getElementById("nb_ecole_fond");
                        if (nb_ecole_fond) {
                            nb_ecole_fond.textContent = result.nb_ecole_fond;
                        }
                        const nb_struct_sante = document.getElementById("nb_struct_sante");
                        if (nb_struct_sante) {
                            nb_struct_sante.textContent = result.nb_struct_sante;
                        }
                        //});

                        const pop_tot_ciblee_ATPC = document.getElementById(
                            "pop_tot_ciblee_ATPC");
                        if (pop_tot_ciblee_ATPC) {
                            pop_tot_ciblee_ATPC.textContent = formatNumber(result
                                .pop_tot_ciblee_ATPC,
                                0);

                        }
                        const nb_tot_men_cible_ATPC = document.getElementById(
                            "nb_tot_men_cible_ATPC");
                        if (nb_tot_men_cible_ATPC) {
                            nb_tot_men_cible_ATPC.textContent = formatNumber(result
                                .nb_tot_men_cible_ATPC,
                                0);

                        }


                        const nb_loc_declench_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_cod_ANSADE");
                        if (nb_loc_declench_cod_ANSADE) {
                            nb_loc_declench_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE,
                                0);
                        }

                        const taux_loc_declench_cod_ANADE = document.getElementById(
                            "taux_loc_declench_cod_ANADE");
                        if (taux_loc_declench_cod_ANADE) {
                            taux_loc_declench_cod_ANADE.textContent = formatNumber(result
                                .nb_loc_declench_cod_ANSADE * 100 / result
                                    .nb_loc_cod_ANSADE,
                                1) + "%";


                        }
                        const nb_loc_certif_FDAL = document.getElementById(
                            "nb_loc_certif_FDAL");
                        if (nb_loc_certif_FDAL) {
                            nb_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL,
                                0);
                        }

                        const taux_loc_certif_FDAL = document.getElementById(
                            "taux_loc_certif_FDAL");
                        if (taux_loc_certif_FDAL) {
                            taux_loc_certif_FDAL.textContent = formatNumber(result
                                .nb_loc_certif_FDAL * 100 / (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_non_cod_ANSADE),
                                1) + "%";
                        }


                        const invest_tot_men_latr = document.getElementById(
                            "invest_tot_men_latr");
                        if (invest_tot_men_latr) {
                            invest_tot_men_latr.textContent = formatNumber(result
                                .invest_tot_men_latr,
                                0);

                        }

                        const nb_loc_declench_non_cod_ANSADE = document.getElementById(
                            "nb_loc_declench_non_cod_ANSADE");
                        if (nb_loc_declench_non_cod_ANSADE) {
                            nb_loc_declench_non_cod_ANSADE.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE,
                                0);

                        }
                        const total_loc_declench = document.getElementById(
                            "total_loc_declench");
                        if (total_loc_declench) {
                            total_loc_declench.textContent = formatNumber(result
                                .nb_loc_declench_non_cod_ANSADE + result
                                    .nb_loc_declench_cod_ANSADE,
                                0);
                        }
                        const nb_tot_loc_redeclench = document.getElementById(
                            "nb_tot_loc_redeclench");
                        if (nb_tot_loc_redeclench) {
                            nb_tot_loc_redeclench.textContent = formatNumber(result
                                .nb_loc_redeclench_cod_ANSADE,
                                0);

                        }
                        const taux_loc_redeclench = document.getElementById(
                            "taux_loc_redeclench");
                        if (taux_loc_redeclench) {
                            taux_loc_redeclench.textContent = formatNumber((result
                                .nb_loc_redeclench_cod_ANSADE + result
                                    .nb_loc_redeclench_non_cod_ANSADE) * 100 / (result
                                        .nb_loc_declench_cod_ANSADE + result
                                            .nb_loc_declench_non_cod_ANSADE),
                                1) + "%";

                        }
                        const nb_loc_jamais_declench = document.getElementById(
                            "nb_loc_jamais_declench");
                        if (nb_loc_jamais_declench) {
                            nb_loc_jamais_declench.textContent = formatNumber(result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE), 0);
                        }

                        const taux_loc_jamais_declench = document.getElementById(
                            "taux_loc_jamais_declench");
                        if (taux_loc_jamais_declench) {
                            taux_loc_jamais_declench.textContent = formatNumber((result
                                .nb_tot_loc - (result
                                    .nb_loc_declench_cod_ANSADE + result
                                        .nb_loc_declench_non_cod_ANSADE)) * 100 / result
                                    .nb_tot_loc, 1) + "%";
                        }
                        const nb_nouv_latr_construit = document.getElementById(
                            "nb_nouv_latr_construit");
                        if (nb_nouv_latr_construit) {
                            nb_nouv_latr_construit.textContent = formatNumber(result
                                .nb_nouv_latr_construit, 0);
                        }
                        const couvertures_dernier = document.getElementById(
                            "couvertures_dernier");
                        if (couvertures_dernier) {
                            couvertures_dernier.textContent = formatNumber(result
                                .couvertures_dernier, 0);
                        }
                        const couvert_actuelle_latr_fam = document.getElementById(
                            "couvert_actuelle_latr_fam");
                        if (couvert_actuelle_latr_fam) {
                            couvert_actuelle_latr_fam.textContent = formatNumber(result
                                .couvertures_dernier * 100 / result.menages_dernier, 1) +
                                "%";
                        }



                        const couvertures_EDL = document.getElementById("couvertures_EDL");
                        if (couvertures_EDL) {
                            couvertures_EDL.textContent = formatNumber(result
                                .couvertures_EDL, 0);
                        }
                        const couvert_initial_latr_fam = document.getElementById(
                            "couvert_initial_latr_fam");
                        if (couvert_initial_latr_fam) {
                            couvert_initial_latr_fam.textContent = formatNumber(result
                                .couvertures_EDL * 100 / result.menages_EDL, 1) + "%";
                        }




                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf, 0);

                        }
                        const taux_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_satisf");
                        if (taux_ecole_besoin_latr_satisf) {
                            taux_ecole_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_besoin_latr_satisf *
                                100 / result.nb_ecole_fond, 1
                            ) + '%';
                        }


                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_sans_latr, 0);
                        }
                        const taux_ecole_sans_latr = document.getElementById(
                            "taux_ecole_sans_latr");
                        if (taux_ecole_sans_latr) {
                            taux_ecole_sans_latr.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nb_ecole_sans_latr * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }
                        const nbr_ecole_cabine_a_const = document.getElementById(
                            "nbr_ecole_cabine_a_const");
                        if (nbr_ecole_cabine_a_const) {
                            nbr_ecole_cabine_a_const.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }

                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf, 0);
                        }

                        const taux_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_ecole_besoin_latr_non_satisf");
                        if (taux_ecole_besoin_latr_non_satisf) {
                            taux_ecole_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_ecole_besoin_latr_satisf
                                .nb_ecole_besoin_latr_non_satisf * 100 /
                                result.nb_ecole_fond, 1
                            ) + "%";
                        }




                        //});
                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf, 0);
                        }
                        const taux_sante_besoin_latr_satisf = document.getElementById(
                            "taux_sante_besoin_latr_satisf");
                        if (taux_sante_besoin_latr_satisf) {
                            taux_sante_besoin_latr_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_satisf *
                                100 /
                                result.nb_struct_sante, 1) + '%';
                        }

                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr, 0);
                        }

                        const taux_sante_sans_latr = document.getElementById(
                            "taux_sante_sans_latr");
                        if (taux_sante_sans_latr) {
                            taux_sante_sans_latr.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_sans_latr * 100 /
                                result.nb_struct_sante, 1) + "%";
                        }
                        const nbr_sante_cabine_a_const = document.getElementById(
                            "nbr_sante_cabine_a_const");
                        if (nbr_sante_cabine_a_const) {
                            nbr_sante_cabine_a_const.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nbr_cabine_a_const, 0);
                        }

                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf.nb_sante_besoin_latr_non_satisf
                            );
                        }

                        const taux_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_sante_besoin_latr_non_satisf");
                        if (taux_sante_besoin_latr_non_satisf) {
                            taux_sante_besoin_latr_non_satisf.textContent = formatNumber(result
                                .nb_sante_besoin_latr_satisf
                                .nb_sante_besoin_latr_non_satisf * 100 / result
                                    .nb_struct_sante, 1) + "%";
                        }


                    }

                });


            });

        });
    </script>
    <script>
        $(function () {



        });
    </script>
    <script>
        var latLngGeom = null;


        $.ajax({
            'async': false,
            'global': false,
            'url': "/assets/admin/geojson/mrt.json",
            'dataType': "json",
            'success': function (data) {
                latLngGeom = data;

            }
        });

        var map = L.map('map')
            .setView([19.353, -11.448], 8),
            osmUrl =
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
                'Map data &copy; 2012 OpenStreetMap contributors';
        var map2 = L.map('map2')
            .setView([19.353, -11.448], 8),
            osmUrl =
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
                'Map data &copy; 2012 OpenStreetMap contributors';
        var map3 = L.map('map3')
            .setView([19.353, -11.448], 8),
            osmUrl =
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
                'Map data &copy; 2012 OpenStreetMap contributors';
        var osm = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map);
        var osm2 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map2);
        var osm3 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map3);
        $(document).ready(function () {



            //Initialize Select2 Elements
            $('.wilaya').select2();
            $('.moughata').select2();
            $('.commune').select2();
            $('.localite').select2();
            var blueIcon = new L.Icon({

                iconUrl: '/assets/admin/dist/img/marker-icon-2x-blue.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });
            var orangeIcon = new L.Icon({

                iconUrl: '/assets/admin/dist/img/marker-icon-2x-orange.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });
            var goldIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-gold.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });

            var violetIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-violet.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });

            var greyIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-grey.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });

            var greenIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-green.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });
            var redIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-red.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });
            var yellowIcon = new L.Icon({
                iconUrl: '/assets/admin/dist/img/marker-icon-2x-yellow.png',
                shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20, 20]
            });

            $.ajax({

                url: "<?php echo e(url('admin/maps/fetch-maps')); ?>",

                type: "POST",

                data: {
                    _token: '<?php echo e(csrf_token()); ?>'

                },

                dataType: 'json',

                success: function (res) {
                    //
                    map.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });
                    map2.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map2.removeLayer(layer);
                        }
                    });
                    map3.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map3.removeLayer(layer);
                        }
                    });

                    $.each(res.santes_besoin_latr_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: goldIcon
                        })
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: violetIcon
                        })
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.santes_sans_latr, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ])
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: goldIcon
                        })
                            .addTo(map2);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: violetIcon
                        })
                            .addTo(map2);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.ecoles_sans_latr, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: greyIcon
                        })
                            .addTo(map2);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.declenches_fdal, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: blueIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.declenches_non_fdal, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: orangeIcon
                        })
                            .addTo(map);


                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });




                    });

                    $.each(res.non_declenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: redIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.redeclenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: greenIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });
                    $.each(res.declenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: yellowIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule).openPopup();
                            });
                    });

                }

            });

            $('#wilaya').on('change', function () {

                var idWilaya = this.value;
                $("#moughata").html('');



                $.ajax({

                    url: "<?php echo e(url('admin/maps/fetch-maps')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (res) {
                        //
                        map.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map2.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        map3.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map3.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 8);
                        map2.setView([res.view.lat, res.view.lon], 8);
                        map3.setView([res.view.lat, res.view.lon], 8);
                        $.each(res.santes_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.santes_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ])
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map2);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map2);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.ecoles_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map2);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.declenches_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.declenches_non_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                                .addTo(map);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });




                        });

                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });

                    }

                });

                $.ajax({

                    url: "<?php echo e(url('admin/api/fetch-moughatas')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function (key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });


                    }

                });

            });
            $('#moughata').on('change', function () {

                var idMoughata = this.value;
                var idWilaya = $("#wilaya").val();


                $("#commune").html('');

                $.ajax({

                    url: "<?php echo e(url('admin/maps/fetch-maps')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (res) {
                        //
                        map.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 8);
                        $('#map').show();
                        $.each(res.declenches_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });




                        });

                        $.each(res.declenches_non_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                                .addTo(map);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });




                        });
                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                    }

                });
                $.ajax({

                    url: "<?php echo e(url('admin/api/fetch-communes')); ?>",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,

                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (res) {

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function (key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });


                    }

                });

            });


            $('#commune').on('change', function () {


                //map.invalidateSize();
                var idMoughata = $("#moughata").val();
                var idWilaya = $("#wilaya").val();
                var idCommune = this.value;


                $.ajax({

                    url: "<?php echo e(url('admin/maps/fetch-maps')); ?>",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        _token: '<?php echo e(csrf_token()); ?>'

                    },

                    dataType: 'json',

                    success: function (res) {
                        //
                        map.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 10);
                        $('#map').show();
                        $.each(res.declenches_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });




                        });
                        $.each(res.declenches_non_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                                .addTo(map);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });




                        });
                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule).openPopup();
                                });
                        });
                    }

                });


            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>