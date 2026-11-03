
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('sidebar.maps'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('contentheader'); ?>
    <?php echo app('translator')->get('sidebar.maps'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('css'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/js/Leaflet.BigImage.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/plugins/select2/css/select2.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/admin/dist/css/leaflet.extra-markers.min.css')); ?>">
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
                <div class="col-md-4">
                    <div class="form-group">

                        <select name="wilaya" id="wilaya" class="form-control wilaya">
                            <option value="">-- Select Wilaya --</option>
                            <option value="all" selected>TOUTES LES WILAYAS</option>
                            <?php $__currentLoopData = $wilayas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->intitule); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                        <select name="moughata" id="moughata" class="form-control moughata">
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">

                        <select name="commune" id="commune" class="form-control commune">
                        </select>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-yellow.png"><?php echo app('translator')->get('map.title'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-green.png"><?php echo app('translator')->get('map.title2'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-red.png"><?php echo app('translator')->get('map.title3'); ?></h2>
    <div class="row">
        <div class="col-12">
            <div class="leaflet-container" id="map"></div>
        </div>
    </div>

    <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-orange.png"><?php echo app('translator')->get('map.title4'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-blue.png"><?php echo app('translator')->get('map.title5'); ?></h2>
    <div class="row">
        <div class="col-12">
            <div class="leaflet-container" id="map2"></div>
        </div>
    </div>
    <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png"><?php echo app('translator')->get('map.title6'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-grey.png"><?php echo app('translator')->get('map.title7'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-violet.png"><?php echo app('translator')->get('map.title8'); ?></h2>
    <div class="row">
        <div class="col-12">
            <div class="leaflet-container" id="map3"></div>
        </div>
    </div>
    <h2 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png"><?php echo app('translator')->get('map.title9'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-grey.png"><?php echo app('translator')->get('map.title10'); ?> | <img
            src="/assets/admin/dist/img/marker-icon-violet.png"><?php echo app('translator')->get('map.title11'); ?></h2>
    <div class="row">
        <div class="col-12">
            <div class="leaflet-container" id="map4"></div>
        </div>
    </div>



    <div class="modal fade" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>DONNEES GENERALES</h5>
                    <table class="table table-bordered">

                        <tbody>

                            <tr>
                                <td>Wilaya</td>
                                <td id="info_wilaya"></td>
                            </tr>
                            <tr>
                                <td>Moughata</td>
                                <td id="info_moughata"></td>
                            </tr>
                            <tr>
                                <td>Commune</td>
                                <td id="info_commune"></td>
                            </tr>
                            <tr>
                                <td>Localité</td>
                                <td id="info_localite"></td>
                            </tr>
                            <tr>
                                <td>Code ANSADE</td>
                                <td id="info_code_ANSADE"></td>
                            </tr>
                            <tr>
                                <td>Ecole</td>
                                <td id="info_ecole"></td>
                            </tr>
                            <tr>
                                <td>Structure de santé</td>
                                <td id="info_sante"></td>
                            </tr>


                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <tr>
                            <td>Source d’approvisionnement en eau de la localité</td>
                            <td id="approv_eau"></td>


                        </tr>
                    </table>
                    <h5>Projet ayant mis en œuvre l’ATPC / Suivi post-FDAL </h5>

                    <table class="table table-bordered">
                        <tr>
                            <td>Intitulé du projet</td>
                            <td>Opérateur</td>
                            <td>Période </td>

                        </tr>
                        <tbody id="projets_atcp">





                        </tbody>
                    </table>
                    <h5>Historique de la mise en œuvre de l’ATPC / Suivi post-FDAL</h5>
                    <table class="table table-bordered">
                        <tr>
                            <td>Etapes de la mise en œuvre de l’ATPC et du suivi post-FDAL</td>
                            <td>Date</td>
                            <td>Pop.</td>
                            <td>Nombre de ménages</td>
                            <td>Nombre de ménages avec latrines</td>
                            <td>Taux de couverture en latrines</td>


                        </tr>
                        <tbody id="operations">
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
<?php $__env->stopSection(); ?>
<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(asset('assets/admin/plugins/select2/js/select2.min.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/admin/js/BoundaryCanvas.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/dist/js/leaflet.extra-markers.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/admin/dist/js/Leaflet.BigImage.min.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('custom'); ?>
    <script>
        $('#wilaya').on('change', function() {
            if ($(this).val() === 'all') {
                window.location.href = "<?php echo e(route('admin.maps.index')); ?>";
            }
    });
        var latLngGeom = null;
        var blueIcon = new L.Icon({

            iconUrl: '/assets/admin/dist/img/marker-icon-2x-blue.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });
        var orangeIcon = new L.Icon({

            iconUrl: '/assets/admin/dist/img/marker-icon-2x-orange.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });

        var greenIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-green.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });
        var redIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-red.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });
        var yellowIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-yellow.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });

        var goldIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-gold.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });

        var violetIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-violet.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });

        var greyIcon = new L.Icon({
            iconUrl: '/assets/admin/dist/img/marker-icon-2x-grey.png',
            shadowUrl: '/assets/admin/dist/img/marker-shadow.png',
                iconSize: [15, 20],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [20 , 20]
        });

        $.ajax({
            'async': false,
            'global': false,
            'url': "/assets/admin/geojson/mrt.json",
            'dataType': "json",
            'success': function(data) {
                latLngGeom = data;

            }
        });

        var map = L.map('map')
            .setView([19.353, -11.448], 6),
            osmUrl =
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
            'Map data &copy; 2012 OpenStreetMap contributors';
        var map2 = L.map('map2')
            .setView([19.353, -11.448], 6),
            osmUrl =
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
            'Map data &copy; 2012 OpenStreetMap contributors';
        var map3 = L.map('map3')
            .setView([19.353, -11.448], 6),
            osmUrl =
            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
            'Map data &copy; 2012 OpenStreetMap contributors';
        var map4 = L.map('map4')
            .setView([19.353, -11.448], 6),
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
        var osm4 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                    boundary: latLngGeom,
                    attribution: osmAttribution
                }).addTo(map4);
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.wilaya').select2();
            $('.moughata').select2();
            $('.commune').select2();
            $('.localite').select2();
            L.control.bigImage({
                position: 'topright'
            }).addTo(map);
            L.control.bigImage({
                position: 'topright'
            }).addTo(map2);
            L.control.bigImage({
                position: 'topright'
            }).addTo(map3);
            L.control.bigImage({
                position: 'topright'
            }).addTo(map4);
            
            

            $.ajax({

                url: "<?php echo e(url('admin/maps/fetch-maps')); ?>",

                type: "POST",

                data: {
                    _token: '<?php echo e(csrf_token()); ?>'

                },

                dataType: 'json',

                success: function(res) {
                    //
                    map.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });
                    map2.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map2.removeLayer(layer);
                        }
                    });

                    map3.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map3.removeLayer(layer);
                        }
                    });

                    map4.eachLayer(function(layer) {
                        if (layer instanceof L.Marker) {
                            map4.removeLayer(layer);
                        }
                    });
                    $.each(res.santes_besoin_latr_satisf, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                            .addTo(map4);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.ecoles_besoin_latr_satisf, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                            .addTo(map3);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.santes_besoin_latr_non_satisf, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                            .addTo(map4);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.ecoles_besoin_latr_non_satisf, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                            .addTo(map3);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.santes_sans_latr, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                            .addTo(map4);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.ecoles_sans_latr, function(key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                            .addTo(map3);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>
            
                                                            </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.declenches_non_fdal, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                            .addTo(map2);


                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
                                        </ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

                                        </tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                                <td>` + value.nb_tot_pop + `</td>
                                                <td>` + value.nb_menages_comptabilises + `</td>
                                                <td>` + value.nb_tot_latr_fam + `</td>
                                                <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
                                            <td></td>
                                            <td</td>
                                            <td></td>
                                            <td></td>
                                            </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.declenches_fdal, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                            .addTo(map2);


                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
</ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

</tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
        <td>` + value.nb_tot_pop + `</td>
        <td>` + value.nb_menages_comptabilises + `</td>
        <td>` + value.nb_tot_latr_fam + `</td>
        <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
    <td></td>
    <td</td>
    <td></td>
    <td></td>
    </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });

                    $.each(res.declenches_fdal, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                            .addTo(map2);


                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
</ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

</tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
        <td>` + value.nb_tot_pop + `</td>
        <td>` + value.nb_menages_comptabilises + `</td>
        <td>` + value.nb_tot_latr_fam + `</td>
        <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
    <td></td>
    <td</td>
    <td></td>
    <td></td>
    </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });

                    $.each(res.non_declenches, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                            .addTo(map);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
</ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

</tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
        <td>` + value.nb_tot_pop + `</td>
        <td>` + value.nb_menages_comptabilises + `</td>
        <td>` + value.nb_tot_latr_fam + `</td>
        <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
    <td></td>
    <td</td>
    <td></td>
    <td></td>
    </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.redeclenches, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                            .addTo(map);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
</ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

</tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
        <td>` + value.nb_tot_pop + `</td>
        <td>` + value.nb_menages_comptabilises + `</td>
        <td>` + value.nb_tot_latr_fam + `</td>
        <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
    <td></td>
    <td</td>
    <td></td>
    <td></td>
    </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });
                    $.each(res.declenches, function(key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                            .addTo(map);
                        marker.on('mouseover',
                            function() {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                        marker.on('click',
                            function() {

                                var idLocalite = value.id;
                                $.ajax({

                                    url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                    type: "POST",

                                    data: {

                                        wilaya_id: idWilaya,
                                        moughata_id: idMoughata,
                                        commune_id: idCommune,
                                        localite_id: idLocalite,
                                        _token: '<?php echo e(csrf_token()); ?>'

                                    },

                                    dataType: 'json',

                                    success: function(res) {

                                        $('#info_sante').html('');
                                        $.each(res
                                            .info_infrastructure_sante,
                                            function(key,
                                                value) {

                                                $('#info_sante')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ + `</li>     
</ul>`
                                                    );
                                            })

                                        $('#info_ecole').html('');
                                        $.each(res
                                            .info_infrastructure_ecole,
                                            function(key,
                                                value) {

                                                $('#info_ecole')
                                                    .append(
                                                        `<ul><li>` +
                                                        value
                                                        .intitule_infra_publ +
                                                        `</li></ul>`
                                                    );
                                            })


                                        const info_code_ANSADE =
                                            document.getElementById(
                                                "info_code_ANSADE");
                                        if (info_code_ANSADE) {
                                            info_code_ANSADE
                                                .textContent = res
                                                .info.code_ansade;
                                        }

                                        const info_localite =
                                            document.getElementById(
                                                "info_localite");
                                        if (info_localite) {
                                            info_localite
                                                .textContent = res
                                                .info.intitule;
                                        }

                                        const info_commune =
                                            document.getElementById(
                                                "info_commune");
                                        if (info_commune) {
                                            info_commune
                                                .textContent = res
                                                .info.commune;
                                        }
                                        const info_moughata =
                                            document.getElementById(
                                                "info_moughata");
                                        if (info_moughata) {
                                            info_moughata
                                                .textContent = res
                                                .info.moughata;
                                        }
                                        const info_wilaya = document
                                            .getElementById(
                                                "info_wilaya");
                                        if (info_wilaya) {
                                            info_wilaya
                                                .textContent = res
                                                .info.wilaya;
                                        }


                                        $('#projets_atcp').html('');
                                        $.each(res.projets_atcp,
                                            function(key,
                                                value) {

                                                $('#projets_atcp')
                                                    .append(
                                                        `<tr><td>` +
                                                        value
                                                        .intitule_resume +
                                                        `</td><td>` +
                                                        value
                                                        .intitule +
                                                        `</td><td>` +
                                                        value
                                                        .year + `</td>

</tr>`
                                                    );
                                            })

                                        $('#operations').html('');
                                        var nb_menages_comptabilises =
                                            0;
                                        $.each(res.operations,
                                            function(key,
                                                value) {
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux'
                                                ) {
                                                    nb_menages_comptabilises
                                                        = value
                                                        .nb_menages_comptabilises;
                                                }
                                                if (value
                                                    .intitule ===
                                                    'Etat des lieux' ||
                                                    value
                                                    .intitule ===
                                                    'Dernier suivi'
                                                ) {
                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
        <td>` + value.nb_tot_pop + `</td>
        <td>` + value.nb_menages_comptabilises + `</td>
        <td>` + value.nb_tot_latr_fam + `</td>
        <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                    100 /
                                                                    nb_menages_comptabilises
                                                                )
                                                                .toFixed(
                                                                    1
                                                                ) :
                                                                'NR'
                                                            ) +
                                                            `</td></tr>`
                                                        );
                                                } else {

                                                    $('#operations')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .date_op + `</td>
    <td></td>
    <td</td>
    <td></td>
    <td></td>
    </tr>`
                                                        );
                                                }

                                            })
                                        const approv_eau = document
                                            .getElementById(
                                                "approv_eau");
                                        if (approv_eau) {
                                            approv_eau.textContent =
                                                res
                                                .approv;
                                        }
                                    }

                                });

                                $('#myModal').modal('show');
                                $('#close').on('click', function() {
                                    $('#myModal').modal('hide');
                                });


                            });



                    });



                }

            });

            $('#wilaya').on('change', function() {

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

                    success: function(res) {
                        //
                        map.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map2.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        map3.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map3.removeLayer(layer);
                            }
                        });
                        map4.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 8);
                        map2.setView([res.view.lat, res.view.lon], 8);
                        map3.setView([res.view.lat, res.view.lon], 8);
                        map4.setView([res.view.lat, res.view.lon], 8);
                        $.each(res.santes_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_sans_latr, function(key, value) {
                            
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_sans_latr, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches_non_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: orangeIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        /*$.each(res.declenches_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: blueIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                    </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                    </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                            <td>` + value.nb_tot_pop + `</td>
                            <td>` + value.nb_menages_comptabilises + `</td>
                            <td>` + value.nb_tot_latr_fam + `</td>
                            <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                        <td></td>
                        <td</td>
                        <td></td>
                        <td></td>
                        </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });*/

                        $.each(res.declenches_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: blueIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                    </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                    </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                            <td>` + value.nb_tot_pop + `</td>
                            <td>` + value.nb_menages_comptabilises + `</td>
                            <td>` + value.nb_tot_latr_fam + `</td>
                            <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                        <td></td>
                        <td</td>
                        <td></td>
                        <td></td>
                        </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });

                        $.each(res.non_declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: redIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                    </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                    </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                            <td>` + value.nb_tot_pop + `</td>
                            <td>` + value.nb_menages_comptabilises + `</td>
                            <td>` + value.nb_tot_latr_fam + `</td>
                            <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                        <td></td>
                        <td</td>
                        <td></td>
                        <td></td>
                        </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.redeclenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: greenIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                    </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                    </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                            <td>` + value.nb_tot_pop + `</td>
                            <td>` + value.nb_menages_comptabilises + `</td>
                            <td>` + value.nb_tot_latr_fam + `</td>
                            <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                        <td></td>
                        <td</td>
                        <td></td>
                        <td></td>
                        </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: yellowIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                    </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                    </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                            <td>` + value.nb_tot_pop + `</td>
                            <td>` + value.nb_menages_comptabilises + `</td>
                            <td>` + value.nb_tot_latr_fam + `</td>
                            <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                        <td></td>
                        <td</td>
                        <td></td>
                        <td></td>
                        </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


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

                    success: function(result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function(key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule + '</option>');

                        });


                    }

                });

            });
            $('#moughata').on('change', function() {

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

                    success: function(res) {
                        //
                        map.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map2.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        map3.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map3.removeLayer(layer);
                            }
                        });
                        map4.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 10);
                        map2.setView([res.view.lat, res.view.lon], 10);
                        map3.setView([res.view.lat, res.view.lon], 10);
                        map4.setView([res.view.lat, res.view.lon], 10);
                        $.each(res.santes_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_sans_latr, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_sans_latr, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches_non_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: orangeIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: blueIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                        </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                                        </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                <td>` + value.nb_tot_pop + `</td>
                                                <td>` + value.nb_menages_comptabilises + `</td>
                                                <td>` + value.nb_tot_latr_fam + `</td>
                                                <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                            <td></td>
                                            <td</td>
                                            <td></td>
                                            <td></td>
                                            </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });

                        $.each(res.non_declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: redIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                        </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                                        </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                <td>` + value.nb_tot_pop + `</td>
                                                <td>` + value.nb_menages_comptabilises + `</td>
                                                <td>` + value.nb_tot_latr_fam + `</td>
                                                <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                            <td></td>
                                            <td</td>
                                            <td></td>
                                            <td></td>
                                            </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.redeclenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: greenIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                        </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                                        </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                <td>` + value.nb_tot_pop + `</td>
                                                <td>` + value.nb_menages_comptabilises + `</td>
                                                <td>` + value.nb_tot_latr_fam + `</td>
                                                <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                            <td></td>
                                            <td</td>
                                            <td></td>
                                            <td></td>
                                            </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: yellowIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                        </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>

                                        </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                <td>` + value.nb_tot_pop + `</td>
                                                <td>` + value.nb_menages_comptabilises + `</td>
                                                <td>` + value.nb_tot_latr_fam + `</td>
                                                <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                            <td></td>
                                            <td</td>
                                            <td></td>
                                            <td></td>
                                            </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


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

                    success: function(res) {

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function(key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule +  '</option>');

                        });


                    }

                });

            });


            $('#commune').on('change', function() {


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

                    success: function(res) {
                        //
                        map.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map.removeLayer(layer);
                            }
                        });
                        map2.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        map3.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map3.removeLayer(layer);
                            }
                        });
                        map4.eachLayer(function(layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 8);
                        map2.setView([res.view.lat, res.view.lon], 8);
                        map3.setView([res.view.lat, res.view.lon], 8);
                        map4.setView([res.view.lat, res.view.lon], 8);
                        $.each(res.santes_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: goldIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: violetIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.santes_sans_latr, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map4);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.ecoles_sans_latr, function(key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                    .long_infra_publ
                                ], {
                                    icon: greyIcon
                                })
                                .addTo(map3);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches_non_fdal, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: orangeIcon
                                })
                                .addTo(map2);


                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });

                        $.each(res.non_declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: redIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.redeclenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: greenIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });
                        $.each(res.declenches, function(key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                    icon: yellowIcon
                                })
                                .addTo(map);
                            marker.on('mouseover',
                                function() {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                            marker.on('click',
                                function() {

                                    var idLocalite = value.id;
                                    $.ajax({

                                        url: "<?php echo e(url('admin/reporting/fetch_reporting_localite_api')); ?>",

                                        type: "POST",

                                        data: {

                                            wilaya_id: idWilaya,
                                            moughata_id: idMoughata,
                                            commune_id: idCommune,
                                            localite_id: idLocalite,
                                            _token: '<?php echo e(csrf_token()); ?>'

                                        },

                                        dataType: 'json',

                                        success: function(res) {

                                            $('#info_sante').html('');
                                            $.each(res
                                                .info_infrastructure_sante,
                                                function(key,
                                                    value) {

                                                    $('#info_sante')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ + `</li>     
                                                            </ul>`
                                                        );
                                                })

                                            $('#info_ecole').html('');
                                            $.each(res
                                                .info_infrastructure_ecole,
                                                function(key,
                                                    value) {

                                                    $('#info_ecole')
                                                        .append(
                                                            `<ul><li>` +
                                                            value
                                                            .intitule_infra_publ +
                                                            `</li></ul>`
                                                        );
                                                })


                                            const info_code_ANSADE =
                                                document.getElementById(
                                                    "info_code_ANSADE");
                                            if (info_code_ANSADE) {
                                                info_code_ANSADE
                                                    .textContent = res
                                                    .info.code_ansade;
                                            }

                                            const info_localite =
                                                document.getElementById(
                                                    "info_localite");
                                            if (info_localite) {
                                                info_localite
                                                    .textContent = res
                                                    .info.intitule;
                                            }

                                            const info_commune =
                                                document.getElementById(
                                                    "info_commune");
                                            if (info_commune) {
                                                info_commune
                                                    .textContent = res
                                                    .info.commune;
                                            }
                                            const info_moughata =
                                                document.getElementById(
                                                    "info_moughata");
                                            if (info_moughata) {
                                                info_moughata
                                                    .textContent = res
                                                    .info.moughata;
                                            }
                                            const info_wilaya = document
                                                .getElementById(
                                                    "info_wilaya");
                                            if (info_wilaya) {
                                                info_wilaya
                                                    .textContent = res
                                                    .info.wilaya;
                                            }


                                            $('#projets_atcp').html('');
                                            $.each(res.projets_atcp,
                                                function(key,
                                                    value) {

                                                    $('#projets_atcp')
                                                        .append(
                                                            `<tr><td>` +
                                                            value
                                                            .intitule_resume +
                                                            `</td><td>` +
                                                            value
                                                            .intitule +
                                                            `</td><td>` +
                                                            value
                                                            .year + `</td>
            
                                                            </tr>`
                                                        );
                                                })

                                            $('#operations').html('');
                                            var nb_menages_comptabilises =
                                                0;
                                            $.each(res.operations,
                                                function(key,
                                                    value) {
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux'
                                                    ) {
                                                        nb_menages_comptabilises
                                                            = value
                                                            .nb_menages_comptabilises;
                                                    }
                                                    if (value
                                                        .intitule ===
                                                        'Etat des lieux' ||
                                                        value
                                                        .intitule ===
                                                        'Dernier suivi'
                                                    ) {
                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                    <td>` + value.nb_tot_pop + `</td>
                                                                    <td>` + value.nb_menages_comptabilises + `</td>
                                                                    <td>` + value.nb_tot_latr_fam + `</td>
                                                                    <td>` + (nb_menages_comptabilises ? (value.nb_tot_latr_fam *
                                                                        100 /
                                                                        nb_menages_comptabilises
                                                                    )
                                                                    .toFixed(
                                                                        1
                                                                    ) :
                                                                    'NR'
                                                                ) +
                                                                `</td></tr>`
                                                            );
                                                    } else {

                                                        $('#operations')
                                                            .append(
                                                                `<tr><td>` +
                                                                value
                                                                .intitule +
                                                                `</td><td>` +
                                                                value
                                                                .date_op + `</td>
                                                                <td></td>
                                                                <td</td>
                                                                <td></td>
                                                                <td></td>
                                                                </tr>`
                                                            );
                                                    }

                                                })
                                            const approv_eau = document
                                                .getElementById(
                                                    "approv_eau");
                                            if (approv_eau) {
                                                approv_eau.textContent =
                                                    res
                                                    .approv;
                                            }
                                        }

                                    });

                                    $('#myModal').modal('show');
                                    $('#close').on('click', function() {
                                        $('#myModal').modal('hide');
                                    });


                                });



                        });


                    }

                });


            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\lapto\piam_projet\piam\resources\views/admin/maps/index.blade.php ENDPATH**/ ?>