@extends('layouts.admin')
@section('title')
@lang('sidebar.reporting')
@endsection
@section('contentdeader')
@lang('sidebar.reporting')
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/js/Leaflet.BigImage.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/css/leaflet.extra-markers.min.css') }}">
<style>
    .text-container {
        border: 2px solid #000000;
        padding: 15px;
        margin: 10px auto;
        border-radius: 8px;
        width: 100%;
        /* Assure que l'encart occupe toute la largeur */
        font-size: 18px;
        /* Taille de police harmonisée */
        line-height: 1.4;
        text-align: justify;
    }

    h5 {
        background-color: #00A95C;
        text-align: center;
        padding: 10px;
        margin: 15px 0;
        font-size: 22px;
        font-weight: bold;
        color: white;
    }

    h6 {
        background: #FFD700;
        text-align: center;
        padding: 8px;
        margin: 12px 0;
        font-size: 18px;
        font-weight: bold;
    }

    .table-element {
        border-color: #000000;
        width: 100%;
        font-size: 20px;
        /* Taille de police harmonisée pour les tableaux */
        border-collapse: collapse;
    }

    /* Mise en gras pour les en-têtes de tableau */
    .table-element th {
        font-weight: bold;
        background-color: #f8f9fa;
        text-align: center;
        padding: 10px 8px;
        border: 2px solid #000000;
    }

    /* Alignement des chiffres à droite */
    .table-element td:not(:first-child) {
        text-align: right;
        padding: 8px 12px;
        border: 2px solid #000000;
    }

    /* Alignement à gauche pour la première colonne (texte) */
    .table-element td:first-child {
        text-align: left;
        padding: 8px 12px;
        border: 2px solid #000000;
    }

    /* Style pour l'impression */
    @media print {
        .no-print {
            display: none !important;
        }

        .text-container,
        .table-element {
            font-size: 16px;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }

        h5,
        h6 {
            page-break-inside: avoid;
        }

        .table-element {
            page-break-inside: avoid;
        }
    }

    /* Style pour les boutons */
    .btn-print {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }

    .btn-print:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>
 <style>
/* Style normal (avant le défilement) */
#rapport {
    text-align: center;
    padding: 12px 0;
    font-weight: bold;
    background-color: white;
    transition: all 0.3s ease;
}

/* Style quand il devient sticky après défilement */
#rapport.sticky {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 1000;
    border-bottom: 2px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}


</style>
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
@endpush
@section('content')
<div class="card">
    <div class="card-header">
        <p class="card-title card_title_center">@lang('general.filter')</p>
    </div>

    <div class="card-body">
        <div class="row">

            <!-- 🟦 Sélection du projet -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="projet" class="form-label fw-bold">Projet</label>
                    <select name="projet" id="projet" class="form-control projet">
                        @foreach ($projets as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule_resume }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🟩 Sélection de l'opérateur -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="operateur" class="form-label fw-bold">Opérateur</label>
                    <select name="operateur" id="operateur" class="form-control operateur">
                    </select>
                </div>
            </div>

            <!-- 🟧 Sélection du type d'assainissement -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="type" class="form-label fw-bold">Type d'assainissement</label>
                    <select name="type" id="type" class="form-control type">
                        <option value="deux">Assainissement familial et Assainissement public</option>
                        <option value="familial">Assainissement familial</option>
                        <option value="public">Assainissement public</option>
                    </select>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 d-flex justify-content-end">

        <button class="btn btn-success no-print" id="print-report">
            <i class="fas fa-print"></i> Imprimer et Exporter PDF
        </button>
    </div>
</div>
<div id="content" class="card">
    <div class="row">
        <div class="col-12">
            <h4 class="card_title_center" id="rapport"></h4>
        </div>
    </div>
    <div id="familial">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <h5>DONNEES GENERALES</h5>
                        <table class="mb-4 table-element" border="3">

                            <tbody>

                                <tr>
                                    <td>Intitulé du projet</td>
                                    <td id="info_projet"></td>
                                </tr>
                                <tr>
                                    <td>Période d'intervention</td>
                                    <td id="info_periode"></td>
                                </tr>
                                <tr>
                                    <td>Zone d'intervention ATPC + suivi post-FDAL</td>
                                    <td id="info_zone"></td>
                                </tr>
                                <tr>
                                    <td>Opérateurs ATPC + suivi post-FDAL</td>
                                    <td id="info_operateurs"></td>
                                </tr>
                                <tr>
                                    <td>Zone d'intervention Latrines Publiques</td>
                                    <td id="info_zone_pub"></td>
                                </tr>
                                <tr>
                                    <td>Opérateurs Latrines Publiques</td>
                                    <td id="info_operateurs_pub"></td>
                                </tr>
                                <tr>
                                    <td>Date de la requête</td>
                                    <td> {{ date('d/m/Y') }}</td>
                                </tr>


                            </tbody>
                        </table>




                        <h6>@lang('tb.chart4')</h6>
                        <canvas id="chart4"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>

                        <h5>MISE EN ŒUVRE DE L'ATPC</h5>
                        <div class="text-container">

                            L'Assainissement Total Piloté par la Communauté (ATPC) est l'approche adoptée par la
                            Mauritanie depuis

                            2011 pour promouvoir l'assainissement en milieu rural. L'ATPC consiste à encourager
                            la communauté à

                            analyser sa propre situation en matière d'hygiène et d'assainissement et notamment
                            ses pratiques en
                            matière de défécation avec leurs conséquences. Elle vise à susciter une action
                            collective destinée à
                            atteindre et maintenir un état de Fin de la Défécation à l'Air Libre (FDAL). Cet état
                            est atteint, à priori, par la
                            construction spontanée de latrines familiales sans qu'il soit nécessaire de prévoir
                            une subvention
                            extérieure aux ménages concernés
                        </div>
                        <h6>Cibles de la mise en œuvre de l'ATPC</h6>
                        <table class="mb-4 table-element" border="3">
                            <tr>
                                <td>Localités déclenchées une première fois</td>
                                <td id="villages_declenches"></td>


                            </tr>
                            <tr>
                                <td>Localités redéclenchées</td>
                                <td id="villages_redeclenches"></td>


                            </tr>
                            <tr>
                                <td>Population ciblée</td>
                                <td id="pop1"></td>


                            </tr>
                            <tr>
                                <td>Ménages ciblés</td>
                                <td id="men1"></td>


                            </tr>

                        </table>


                        <h6>Certification FDAL </h6>
                        <div class="text-container">

                            La certification FDAL (Fin de la Défécation à l'Air Libre) d'une localité est réalisée par
                            le
                            Comité Régional

                            de Suivi (CRS) selon les critères définis par la Direction de l'Assainissement, à savoir :

                            <ul>
                                <li>Les sites de défécation à l'air libre ont totalement disparu </li>
                                <li>80 % des ménages disposent de latrines </li>
                                <li>Un plan d'action existe pour équiper les 20 % de ménages restant dans les 6 mois
                                </li>
                                <li>100 % des ménages utilisent des latrines </li>
                                <li>60 % des latrines sont associées à un dispositif de lave-mains fonctionnel </li>
                            </ul>

                        </div>
                        <table class="mb-4 table-element" border="3">
                            <tr>
                                <th>Année</th>
                                <th>Nombre de localités déclenchées</th>
                                <th>Nombre de localités certifiées FDAL </th>
                                <th>Taux de certification (%) </th>
                                <th>Nombre de localités déclenchées et non certifiés FDAL </th>



                            </tr>
                            <tbody id="fdal">

                            </tbody>
                        </table>
                        <h6>@lang('tb.chart1')</h6>
                        <canvas id="chart1"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-orange.png">@lang('map.title4') | <img
                            src="/assets/admin/dist/img/marker-icon-blue.png">@lang('map.title5')</h6>
                        <div class="leaflet-container" id="map2"></div>
                        <h6>Dynamique du projet</h6>
                        <table class="mb-4 table-element" border="3">
                            <thead>
                                <tr>
                                    <th>Indicateur</th>
                                    <th>Nombre ou taux</th>
                                    <th>Nombre localités prises en compte </th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nombre de nouvelles latrines construites</td>
                                    <td id="construite"></td>
                                    <td id="localite_construite"></td>
                                </tr>
                                <tr>
                                    <td>Taux de latrines auto-construites</td>
                                    <td id="auto_const"></td>
                                    <td id="localite_auto_const"></td>
                                </tr>
                                <tr>
                                    <td>Taux de latrines réalisées avec l'aide d'un maçon</td>
                                    <td id="re_macon"></td>
                                    <td id="localite_macon"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines familiales en construction en fin de projet</td>
                                    <td id="en_const"></td>
                                    <td id="localite_en_const"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes
                                    </td>
                                    <td id="phm"></td>
                                    <td id="localite_phm"></td>
                                </tr>
                            </tbody>
                        </table>
                        <h6>Financement des latrines familiales</h6>


                        <table class="mb-4 table-element" border="3">
                            <thead>
                                <tr>
                                    <th>Indicateur</th>
                                    <th>Nombre ou taux</th>
                                    <th>Nombre localités prises en compte </th>

                                </tr>
                            </thead>
                            <tbody id="financement">


                            </tbody>
                        </table>
                        <h6>Evolution de l'accès des ménages à l'hygiène et l'assainissement</h6>
                        <table class="mb-4 table-element" border="3">
                            <thead>
                                <tr>
                                    <th>Indicateurs</th>
                                    <th>Situation en début de projet</th>
                                    <th>Situation à la fin de projet</th>
                                    <th>Progression</th>


                                </tr>
                            </thead>



                            <tbody>
                                <tr>
                                    <th>Assainissement</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>Nombre total de latrines</td>
                                    <td id="nt_latr"></td>
                                    <td id="nt_latr2"></td>
                                    <td id="nt_latr3"></td>
                                </tr>
                                <tr>
                                    <td>Taux de couverture en latrines</td>
                                    <td id="ct_latr"></td>
                                    <td id="ct_latr2"></td>
                                    <td id="ct_latr3"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines améliorées </td>
                                    <td id="n_amel"></td>
                                    <td id="n_amel2"></td>
                                    <td id="n_amel3"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de ménages déféquant à l'air libre</td>
                                    <td id="n_dal"></td>
                                    <td id="n_dal2"></td>
                                    <td id="n_dal3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement sécurisé</td>
                                    <td id="securise"></td>
                                    <td id="securise2"></td>
                                    <td id="securise3"></td>
                                </tr>
                                <tr>

                                    <td>Accès à un service d'assainissement basique (latrines améliorées)</td>
                                    <td id="basique"></td>
                                    <td id="basique2"></td>
                                    <td id="basique3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement limité (latrines partagées)</td>
                                    <td id="limite"></td>
                                    <td id="limite2"></td>
                                    <td id="limite3"></td>


                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement non amélioré</td>
                                    <td id="non_amelior"></td>
                                    <td id="non_amelior2"></td>
                                    <td id="non_amelior3"></td>
                                </tr>
                                <tr>
                                    <td>Taux de ménages déféquant à l'air libre</td>
                                    <td id="dal"></td>
                                    <td id="dal2"></td>
                                    <td id="dal3"></td>
                                </tr>

                                <tr>
                                    <th>Hygiène</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>Nombre de latrines familiales associées à un dispositif de lave-mains
                                        fonctionnel
                                    </td>
                                    <td id="hygiene_DLM"></td>
                                    <td id="hygiene_DLM2"></td>
                                    <td id="hygiene_DLM3"></td>


                                </tr>
                                <tr>
                                    <td>Taux de latrines associées à un dispositif de lave-mains fonctionnel</td>
                                    <td id="taux_hygiene_DLM"></td>
                                    <td id="taux_hygiene_DLM2"></td>
                                    <td id="taux_hygiene_DLM3"></td>


                                </tr>
                                <tr>

                                    <td>Accès à un service d'hygiène de base (eau + savon)</td>
                                    <td id="hygiene_base"></td>
                                    <td id="hygiene_base2"></td>
                                    <td id="hygiene_base3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'hygiène limité (eau ou savon)</td>
                                    <td id="hygiene_limite"></td>
                                    <td id="hygiene_limite2"></td>
                                    <td id="hygiene_limite3"></td>


                                </tr>
                                <tr>
                                    <td>Accès à aucun service</td>
                                    <td id="hygiene_aucun_service"></td>
                                    <td id="hygiene_aucun_service2"></td>
                                    <td id="hygiene_aucun_service3"></td>
                                </tr>
                            </tbody>
                        </table>

                        <h6>@lang('tb.chart2')</h6>
                        <canvas id="chart2"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        <h6>@lang('tb.chart3')</h6>
                        <canvas id="chart3"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>

                        <h5>Mise en œuvre du suivi post-FDAL</h5>
                        <div class="text-container">

                            Le suivi post-FDAL consiste à effectuer un état des lieux quelques années après la
                            certification FDAL d'une

                            localité et à mener des activités de redynamisation dans la mesure où certains ménages
                            seraient retombés
                            dans la défécation à l'air libre.
                        </div>
                        <h6>Cibles de la mise en œuvre du suivi post-FDAL</h6>
                        <table class="mb-4 table-element" border="3">

                            <tr>
                                <td>Localités bénéficiaires d'un appui-suivi</td>
                                <td id="nb_loc_benefic_appui"></td>


                            </tr>
                            <tr>
                                <td>Population ciblée</td>
                                <td id="pop2"></td>


                            </tr>
                            <tr>
                                <td>Ménages ciblés</td>
                                <td id="men2"></td>


                            </tr>

                        </table>
                        <h6>Dynamique du projet</h6>
                        <table class="mb-4 table-element" border="3">
                            <thead>
                                <tr>
                                    <th>Indicateur</th>
                                    <th>Nombre ou taux</th>
                                    <th>Nombre localités prises en compte </th>

                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>Nombre de nouvelles latrines construites</td>
                                    <td id="construite2"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Taux de latrines auto-construites</td>
                                    <td id="auto_const2"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Taux de latrines réalisées avec l'aide d'un maçon</td>
                                    <td id="re_macon2"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines familiales en construction en fin de projet</td>
                                    <td id="en_const2"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes
                                    </td>
                                    <td id="phm2"></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <h6>Evolution de l'accès des ménages à l'hygiène et l'assainissement</h6>
                        <table class="mb-4 table-element" border="3">
                            <thead>
                                <tr>
                                    <th>Indicateurs</th>
                                    <th>Situation lors de l'état des lieux</th>
                                    <th>Situation à la fin de l'appui-suivi</th>
                                    <th>Progression</th>


                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>Assainissement</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>Nombre total de latrines</td>
                                    <td id="post_nt_latr"></td>
                                    <td id="post_nt_latr2"></td>
                                    <td id="post_nt_latr3"></td>
                                </tr>
                                <tr>
                                    <td>Taux de couverture en latrines</td>
                                    <td id="post_ct_latr"></td>
                                    <td id="post_ct_latr2"></td>
                                    <td id="post_ct_latr3"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de latrines améliorées </td>
                                    <td id="post_n_amel"></td>
                                    <td id="post_n_amel2"></td>
                                    <td id="post_n_amel3"></td>
                                </tr>
                                <tr>
                                    <td>Nombre de ménages déféquant à l'air libre</td>
                                    <td id="post_n_dal"></td>
                                    <td id="post_n_dal2"></td>
                                    <td id="post_n_dal3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement sécurisé</td>
                                    <td id="post_securise"></td>
                                    <td id="post_securise2"></td>
                                    <td id="post_securise3"></td>
                                </tr>
                                <tr>

                                    <td>Accès à un service d'assainissement basique (latrines améliorées)</td>
                                    <td id="post_basique"></td>
                                    <td id="post_basique2"></td>
                                    <td id="post_basique3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement limité (latrines partagées)</td>
                                    <td id="post_limite"></td>
                                    <td id="post_limite2"></td>
                                    <td id="post_limite3"></td>


                                </tr>
                                <tr>
                                    <td>Accès à un service d'assainissement non amélioré</td>
                                    <td id="post_non_amelior"></td>
                                    <td id="post_non_amelior2"></td>
                                    <td id="post_non_amelior3"></td>
                                </tr>
                                <tr>
                                    <td>Taux de ménages déféquant à l'air libre</td>
                                    <td id="post_dal"></td>
                                    <td id="post_dal2"></td>
                                    <td id="post_dal3"></td>
                                </tr>

                                <tr>
                                    <th>Hygiène</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>


                                </tr>
                                <tr>
                                    <td>Latrines associées à un DLM fonctionnel</td>
                                    <td id="post_hygiene_DLM"></td>
                                    <td id="post_hygiene_DLM2"></td>
                                    <td id="post_hygiene_DLM3"></td>


                                </tr>
                                <tr>
                                    <td>Latrines associées à un DLM fonctionnel</td>
                                    <td id="post_taux_hygiene_DLM"></td>
                                    <td id="post_taux_hygiene_DLM2"></td>
                                    <td id="post_taux_hygiene_DLM3"></td>


                                </tr>
                                <tr>

                                    <td>Accès à un service d'hygiène de base (eau + savon)</td>
                                    <td id="post_hygiene_base"></td>
                                    <td id="post_hygiene_base2"></td>
                                    <td id="post_hygiene_base3"></td>
                                </tr>
                                <tr>
                                    <td>Accès à un service d'hygiène limité (eau ou savon)</td>
                                    <td id="post_hygiene_limite"></td>
                                    <td id="post_hygiene_limite2"></td>
                                    <td id="post_hygiene_limite3"></td>


                                </tr>
                                <tr>
                                    <td>Accès à aucun service</td>
                                    <td id="post_hygiene_aucun_service"></td>
                                    <td id="post_hygiene_aucun_service2"></td>
                                    <td id="post_hygiene_aucun_service3"></td>
                                </tr>
                            </tbody>

                        </table>
                        <br><br>



                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </div>
    <div id="public">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">
                        <h5>CONSTRUCTION DE LATRINES PUBLIQUES </h5>
                        <table class="mb-4 table-element" border="3">
                            <tr>
                                <th>Type de site </th>


                                <th>Nombre de sites ciblés</th>


                                <th>Nombre de bénéficiaires</th>
                                <th>Nombre de blocs réalisés</th>

                                <th>Nombre de cabines réalisées</th>
                                <th>Nombre de cabines réhabilitées</th>
                            </tr>
                            <tbody id="projets"></tbody>
                        </table>
                        <h6>@lang('tb.chart5')</h6>
                        <canvas id="chart5"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        <h6>@lang('tb.chart6')</h6>
                        <canvas id="chart6"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    <h6>@lang('tb.chart7')</h6>
                    <canvas id="chart7"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                            <h6>@lang('tb.chart8')</h6>
                    <canvas id="chart8"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                            <h6>@lang('tb.chart9')</h6>
                    <canvas id="chart9"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                                                <h6>@lang('tb.chart10')</h6>
                    <canvas id="chart10"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title12')</h6>
                    <div class="leaflet-container" id="map5"></div>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title13')</h6>
                    <div class="leaflet-container" id="map6"></div>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title14')</h6>
                    <div class="leaflet-container" id="map7"></div>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title15')</h6>
                    <div class="leaflet-container" id="map8"></div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    </div>

</div>
@endsection
@push('script')
<script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="{{ asset('assets/admin/dist/js/html2pdf.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/admin/js/BoundaryCanvas.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/leaflet.extra-markers.min.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/Leaflet.BigImage.min.js') }}"></script>


@endpush
@push('custom')

<script>
    window.safeNumber = function(value) {
        const number = parseFloat(value);
        return Number.isFinite(number) ? number : 0;
    };

    window.safePercentValue = function(numerator, denominator, decimals = 1) {
        denominator = window.safeNumber(denominator);

        if (denominator === 0) {
            return Number(0).toFixed(decimals);
        }

        return ((window.safeNumber(numerator) * 100) / denominator).toFixed(decimals);
    };

    function formatTaux(number, decimals = 1) {
        // Validate inputs
        if (isNaN(number) || number == null || !isFinite(number)) {
            number = 0;
        }

        // Parse the number to ensure it's treated as a number
        number = parseFloat(number);
        // Parse the int to ensure it's treated as a int
        decimals = parseInt(decimals);




        // Use Intl.NumberFormat for advanced formatting options
        const formatter = new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });

        // Format the number with the specified decimals
        const formattedNumber = formatter.format(number);

        return formattedNumber + '%';
    }

    function formatNumberReport(number, decimals = 0) {
        // Validate inputs
        if (isNaN(number) || number == null || !isFinite(number)) {
            number = 0;
        }

        // Parse the number to ensure it's treated as a number
        number = parseFloat(number);
        // Parse the int to ensure it's treated as a int
        decimals = parseInt(decimals);





        // Use Intl.NumberFormat for advanced formatting options
        const formatter = new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });

        // Format the number with the specified decimals
        const formattedNumber = formatter.format(number);

        return formattedNumber;
    }
    $(document).ready(function () {
        Chart.register(ChartDataLabels);
        $('#content').hide();
        //Initialize Select2 Elements
        $('.projet').select2()
        $('.operateur').select2()
        $('.type').select2()

    })
    $(document).ready(function () {
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
        $('#projet').on('change', function () {
            $('#type').val(null).trigger('change');
            var idProjet = this.value;
            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_projet_api') }}",

                type: "POST",

                data: {

                    projet_id: idProjet,

                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (result) {

                    $('#content').show();
                    var chart1 = result.fdal;

                    var labels = [];
                    var data1 = [];
                    var data2 = [];
                    $.each(chart1, function (key, value) {
                        labels.push(value.year);
                        data1.push(value.nbr_localites_declenchees);
                        data2.push(value.nbr_localites_certifiees);
                    })
                    if (typeof chart_1 !== 'undefined' && chart_1 !== null) {
                        chart_1.destroy();
                    }
                    var chart1 = {
                        labels: labels,
                        datasets: [{
                            label: @json(__('tb.op_declechement')),
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
                            label: @json(__('tb.op_fdal')),
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
                    var chart1Canvas = $('#chart1').get(0).getContext('2d')


                    chart_1 = new Chart(chart1Canvas, {
                        type: 'bar',
                        data: chart1,
                        options: {
                            plugins: {
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    color: '#f39c12',
                                    display: function (context) {
                                        return context.dataset.data[context
                                            .dataIndex] > 0;
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

                    const rapport = document.getElementById(
                        "rapport");
                    if (rapport) {
                        rapport.textContent = 'Rapport du projet ' + result
                            .g.projet;
                    }
                    const info_projet = document.getElementById(
                        "info_projet");
                    if (info_projet) {
                        info_projet.textContent = result
                            .g.projet;
                    }
                    const info_periode = document.getElementById(
                        "info_periode");
                    if (info_periode) {
                        info_periode.textContent = (result
                            .g.min_year + '-' + result
                                .g.max_year);
                    }
                    $('#info_zone').html('');
                    $.each(result.g2, function (key,
                        value) {

                        $('#info_zone').append(`<ul><li>` +
                            value.wilaya + `</li>     
                                </ul>`);
                    });
                    $('#info_operateurs').html('');
                    $.each(result.g3, function (key,
                        value) {
                        $('#info_operateurs').append(`<ul><li>` +
                            value.operateur + `</li>     
                                </ul>`);
                    });

                    $('#info_zone_pub').html('');
                    $.each(result.g2_infrastructures, function (key,
                        value) {
                        $('#info_zone_pub').append(`<ul><li>` +
                            value.wilaya + `</li>     
                                </ul>`);
                    });

                    $('#info_operateurs_pub').html('');
                    $.each(result.g3_infrastructures, function (key,
                        value) {
                        $('#info_operateurs_pub').append(`<ul><li>` +
                            value.operateur + `</li>     
                                </ul>`);
                    });
                    const villages_declenches = document.getElementById(
                        "villages_declenches");
                    if (villages_declenches) {
                        villages_declenches.textContent = formatNumberReport(result
                            .villages_declenches);
                    }
                    const villages_redeclenches = document.getElementById(
                        "villages_redeclenches");
                    if (villages_redeclenches) {
                        villages_redeclenches.textContent = formatNumberReport(result
                            .villages_redeclenches);
                    }
                    const pop1 = document.getElementById(
                        "pop1");
                    if (pop1) {
                        pop1.textContent = formatNumberReport(result
                            .population);
                    }
                    const men1 = document.getElementById(
                        "men1");
                    if (men1) {
                        men1.textContent = formatNumberReport(result
                            .menages);
                    }
                    var cumul_nbr_localites_declenchees = 0;
                    var cumul_nbr_localites_certifiees = 0;

                    $('#fdal').html('');
                    $.each(result.fdal, function (key,
                        value) {
                        cumul_nbr_localites_declenchees =
                            cumul_nbr_localites_declenchees + parseFloat(value
                                .nbr_localites_declenchees);
                        cumul_nbr_localites_certifiees =
                            cumul_nbr_localites_certifiees + parseFloat(value
                                .nbr_localites_certifiees);

                        $('#fdal').append(

                            `<tr>
                                        
                                        <td>` + value.year + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_declenchees) + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_certifiees) + `</td>
                                        <td bgcolor="gray"></td>
                                        <td bgcolor="gray"></td>
                                        
                                    </tr>`);
                    });
                    $('#fdal').append(`<tr><td>Cumul</td><td> ` +
                        formatNumberReport(cumul_nbr_localites_declenchees) + ` </td> 
                            <td> ` + formatNumberReport(cumul_nbr_localites_certifiees) + ` </td><td > ` +
                        formatTaux(
                            cumul_nbr_localites_certifiees * 100 /
                            cumul_nbr_localites_declenchees) + ` </td> <td > ` +
                        formatNumberReport(cumul_nbr_localites_declenchees -
                            cumul_nbr_localites_certifiees) + ` </td></tr>`);



                    const construite = document.getElementById(
                        "construite");
                    if (construite) {
                        construite.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise);
                    }
                    const localite_construite = document.getElementById(
                        "localite_construite");
                    if (localite_construite) {
                        localite_construite.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise);
                    }
                    const auto_const = document.getElementById(
                        "auto_const");
                    if (auto_const) {
                        auto_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_autoconst);
                    }
                    const localite_auto_const = document.getElementById(
                        "localite_auto_const");
                    if (localite_auto_const) {
                        localite_auto_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autoconst);
                    }

                    const re_macon = document.getElementById(
                        "re_macon");
                    if (re_macon) {
                        re_macon.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise_macon);
                    }
                    const localite_macon = document.getElementById(
                        "localite_macon");
                    if (localite_macon) {
                        localite_macon.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise_macon);
                    }
                    const en_const = document.getElementById(
                        "en_const");
                    if (en_const) {
                        en_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_en_constr);
                    }
                    const localite_en_const = document.getElementById(
                        "localite_en_const");
                    if (localite_en_const) {
                        localite_en_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_en_constr);
                    }
                    const phm = document.getElementById(
                        "phm");
                    if (phm) {
                        phm.textContent = formatNumberReport(result.r
                            .nbr_menages_ltr_end_phm);
                    }
                    const localite_phm = document.getElementById(
                        "localite_phm");
                    if (localite_phm) {
                        localite_phm.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_menages_ltr_end_phm);
                    }



                    $('#financement').html('');
                                        $('#financement').append(
                        `<tr><td>Nombre de latrines auto-financées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_autofin) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autofin) + `</td></tr>
                            <tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(result.r
                                .taux_latrine_autofin) +
                        `</td><td></td></tr>
                            <tr><td>Nombre de latrines réalisées pour lesquelles les données d'investissement ont été recueillies</td><td>` +
                        formatNumberReport(result.r
                            .nb_latr_invest_men_dispo) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nb_latr_invest_men_dispo) +
                        `</td></tr>
                            <tr><td>Montant total investi par les ménages dans leurs latrines (MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_invest_men) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_mont_invest_men) +
                        `</td></tr>
                            <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_moyen_invest_men) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines financées par entraide communautaire</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_entraid) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_entraid) + `</td></tr>
                            <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_entraid) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines subventionnées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_subv) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_subv) + `</td></tr>
                            <tr><td>Taux de latrines subventionnées</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_subv) +
                        `</td><td></td></tr>`
                    );


                    ///////////////

                    const post_n_dal2 = document.getElementById(
                        "post_n_dal2");
                    var post_n_da2 = result.r4.max
                        .men_dal;

                    if (post_n_dal2) {
                        post_n_dal2.textContent = formatNumberReport(post_n_da2);
                    }
                    const post_n_dal = document.getElementById(
                        "post_n_dal");
                    var post_n_da = result.r4.min
                        .men_dal;
                    if (post_n_dal) {
                        post_n_dal.textContent = formatNumberReport(post_n_da);
                    }
                    const post_n_dal3 = document.getElementById(
                        "post_n_dal3");
                    if (post_n_dal3) {
                        post_n_dal3.textContent = formatNumberReport(parseFloat(
                            post_n_da2) -
                            parseFloat(
                                post_n_da));
                    }

                    const post_n_amel2 = document.getElementById(
                        "post_n_amel2");
                    var post_n_ame2 = result.r4.max
                        .nb_ameliores;
                    if (post_n_amel2) {
                        post_n_amel2.textContent = formatNumberReport(post_n_ame2);
                    }
                    const post_n_amel = document.getElementById(
                        "post_n_amel");
                    var post_n_ame = result.r4.min
                        .nb_ameliores;
                    if (post_n_amel) {
                        post_n_amel.textContent = formatNumberReport(post_n_ame);
                    }
                    const post_n_amel3 = document.getElementById(
                        "post_n_amel3");
                    if (post_n_amel3) {
                        post_n_amel3.textContent = formatNumberReport(parseFloat(
                            post_n_ame2) - parseFloat(
                                post_n_ame));
                    }

                    const post_nt_latr = document.getElementById(
                        "post_nt_latr");
                    if (post_nt_latr) {
                        post_nt_latr.textContent = formatNumberReport(result.r4.min
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr2 = document.getElementById(
                        "post_nt_latr2");
                    if (post_nt_latr2) {
                        post_nt_latr2.textContent = formatNumberReport(result.r4.max
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr3 = document.getElementById(
                        "post_nt_latr3");
                    if (post_nt_latr3) {
                        post_nt_latr3.textContent = formatNumberReport(parseFloat(result.r4
                            .max
                            .nb_tot_latr_fam) - parseFloat(result.r4.min
                                .nb_tot_latr_fam));
                    }

                    const post_ct_latr = document.getElementById(
                        "post_ct_latr");
                    var post_ct = formatTaux(result.r4.min
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr) {
                        post_ct_latr.textContent = post_ct;
                    }

                    const post_ct_latr2 = document.getElementById(
                        "post_ct_latr2");
                    var post_ct2 = formatTaux(result.r4.max
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr2) {
                        post_ct_latr2.textContent =
                            post_ct2;
                    }

                    const post_ct_latr3 = document.getElementById(
                        "post_ct_latr3");
                    if (post_ct_latr3) {
                        post_ct_latr3.textContent = formatTaux(parseFloat(post_ct2) -
                            parseFloat(post_ct));
                    }

                    const post_securise = document.getElementById(
                        "post_securise");
                    var post_sec = formatTaux(result.r4.min
                        .securise * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_securise) {
                        post_securise.textContent = post_sec;
                    }
                    const post_securise2 = document.getElementById(
                        "post_securise2");
                    var post_sec2 = formatTaux(result.r4.max
                        .securise * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_securise2) {
                        post_securise2.textContent = post_sec2;
                    }

                    const post_securise3 = document.getElementById(
                        "post_securise3");
                    if (post_securise3) {
                        post_securise3.textContent = formatNumberReport(parseFloat(
                            post_sec2) - parseFloat(
                                post_sec));
                    }
                    const post_basique = document.getElementById(
                        "post_basique");
                    var post_bas = formatTaux(result.r4.min
                        .basique * 100 / result.total_menages);

                    if (post_basique) {
                        post_basique.textContent = post_bas;
                    }

                    const post_basique2 = document.getElementById(
                        "post_basique2");
                    var post_bas2 = formatTaux(result.r4.max
                        .basique * 100 / result.total_menages);
                    if (post_basique2) {
                        post_basique2.textContent = post_bas2;
                    }

                    const post_basique3 = document.getElementById(
                        "post_basique3");
                    if (post_basique3) {
                        post_basique3.textContent = formatTaux(parseFloat(post_bas2) -
                            parseFloat(
                                post_bas));
                    }


                    const post_non_amelior = document.getElementById(
                        "post_non_amelior");
                    var post_non_am = formatTaux(result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior) {
                        post_non_amelior.textContent = post_non_am;
                    }

                    const post_non_amelior2 = document.getElementById(
                        "post_non_amelior2");
                    var post_non_am2 = formatTaux(result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior2) {
                        post_non_amelior2.textContent = post_non_am2;
                    }
                    const post_non_amelior3 = document.getElementById(
                        "post_non_amelior3");
                    if (post_non_amelior3) {
                        post_non_amelior3.textContent = formatTaux(parseFloat(
                            post_non_am2) - parseFloat(
                                post_non_am));
                    }
                    const post_dal = document.getElementById(
                        "post_dal");
                    var post_da1 = formatTaux(result.r4.min
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal) {
                        post_dal.textContent = post_da1;
                    }
                    const post_dal2 = document.getElementById(
                        "post_dal2");
                    var post_da2 = formatTaux(result.r4.max
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal2) {
                        post_dal2.textContent = post_da2;
                    }

                    const post_dal3 = document.getElementById(
                        "post_dal3");
                    if (post_dal3) {
                        post_dal3.textContent = formatTaux(parseFloat(post_da2) -
                            parseFloat(
                                post_da1));
                    }

                    const post_limite = document.getElementById(
                        "post_limite");
                    var post_lim = formatTaux(100 - (result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.min
                                .basique * 100 / result
                                    .total_menages) - (result.r4.min
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite) {
                        post_limite.textContent = post_lim;
                    }
                    const post_limite2 = document.getElementById(
                        "post_limite2");

                    var post_lim2 = formatTaux(100 - (result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.max
                                .basique * 100 / result
                                    .total_menages) - (result.r4.max
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite2) {
                        post_limite2.textContent = post_lim2;
                    }

                    const post_limite3 = document.getElementById(
                        "post_limite3");
                    if (post_limite3) {
                        post_limite3.textContent = formatTaux(parseFloat(
                            post_lim2) -
                            parseFloat(post_lim));
                    }
                    const post_taux_hygiene_DLM = document.getElementById(
                        "post_taux_hygiene_DLM");
                    var post_hd = formatTaux(result.r4.min
                        .nbr_menages_dlm * 100 / result.r4.min
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM) {
                        post_taux_hygiene_DLM.textContent = post_hd;
                    }
                    const post_hygiene_DLM = document.getElementById(
                        "post_hygiene_DLM");

                    if (post_hygiene_DLM) {
                        post_hygiene_DLM.textContent = formatNumberReport(result.r4.min
                            .nbr_menages_dlm);
                    }

                    const post_taux_hygiene_DLM2 = document.getElementById(
                        "post_taux_hygiene_DLM2");
                    var post_hd2 = formatTaux(result.r4.max
                        .nbr_menages_dlm * 100 / result.r4.max
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM2) {
                        post_taux_hygiene_DLM2.textContent = post_hd2;
                    }

                    const post_taux_hygiene_DLM3 = document.getElementById(
                        "post_taux_hygiene_DLM3");
                    if (post_taux_hygiene_DLM3) {
                        post_taux_hygiene_DLM3.textContent = formatTaux(parseFloat(
                            post_hd2) -
                            parseFloat(
                                post_hd));
                    }
                    const post_hygiene_DLM2 = document.getElementById(
                        "post_hygiene_DLM2");

                    if (post_hygiene_DLM2) {
                        post_hygiene_DLM2.textContent = formatNumberReport(result.r4.max
                            .nbr_menages_dlm);
                    }
                    const post_hygiene_DLM3 = document.getElementById(
                        "post_hygiene_DLM3");
                    if (post_hygiene_DLM3) {
                        post_hygiene_DLM3.textContent = formatNumberReport(parseFloat(result
                            .r4.max
                            .nbr_menages_dlm) - parseFloat(result.r4.min
                                .nbr_menages_dlm));
                    }

                    const post_hygiene_base = document.getElementById(
                        "post_hygiene_base");
                    var post_hb = formatTaux(result.r4.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_base) {
                        post_hygiene_base.textContent = post_hb;
                    }
                    const post_hygiene_base2 = document.getElementById(
                        "post_hygiene_base2");
                    var post_hb2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_base2) {

                        post_hygiene_base2.textContent = post_hb2;
                    }

                    const post_hygiene_base3 = document.getElementById(
                        "post_hygiene_base3");
                    if (post_hygiene_base3) {
                        post_hygiene_base3.textContent = formatTaux(parseFloat(post_hb2) -
                            parseFloat(
                                post_hb));
                    }


                    const post_hygiene_limite = document.getElementById(
                        "post_hygiene_limite");
                    var post_hl = formatTaux(result.r4.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_limite) {
                        post_hygiene_limite.textContent = post_hl;
                    }
                    const post_hygiene_limite2 = document.getElementById(
                        "post_hygiene_limite2");
                    var post_hl2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_limite2) {
                        post_hygiene_limite2.textContent = post_hl2;
                    }

                    const post_hygiene_limite3 = document.getElementById(
                        "post_hygiene_limite3");
                    if (post_hygiene_limite3) {
                        post_hygiene_limite3.textContent = formatTaux(parseFloat(post_hl2) -
                            parseFloat(
                                post_hl));
                    }

                    const post_hygiene_aucun_service = document.getElementById(
                        "post_hygiene_aucun_service");
                    var post_ha = formatTaux(result.r4.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service) {
                        post_hygiene_aucun_service.textContent = post_ha;
                    }
                    const post_hygiene_aucun_service2 = document.getElementById(
                        "post_hygiene_aucun_service2");
                    var post_ha2 = formatTaux(result.r4.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service2) {
                        post_hygiene_aucun_service2.textContent = post_ha2;
                    }


                    const post_hygiene_aucun_service3 = document.getElementById(
                        "post_hygiene_aucun_service3");
                    if (post_hygiene_aucun_service3) {
                        post_hygiene_aucun_service3.textContent = formatTaux(parseFloat(
                            post_ha2) -
                            parseFloat(post_ha));
                    }
                    ////////////////
                    const nt_latr = document.getElementById(
                        "nt_latr");
                    if (nt_latr) {
                        nt_latr.textContent = formatNumberReport(result.r2.min
                            .nb_tot_latr_fam);
                    }
                    const nt_latr2 = document.getElementById(
                        "nt_latr2");
                    if (nt_latr2) {
                        nt_latr2.textContent = formatNumberReport(result.r2.max
                            .nb_tot_latr_fam);
                    }
                    const nt_latr3 = document.getElementById(
                        "nt_latr3");
                    if (nt_latr3) {
                        nt_latr3.textContent = formatNumberReport(parseInt(result.r2.max
                            .nb_tot_latr_fam) - parseInt(result.r2.min
                                .nb_tot_latr_fam));
                    }

                    const ct_latr = document.getElementById(
                        "ct_latr");
                    var ct = formatTaux(result.r2.min
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr) {
                        ct_latr.textContent = ct;
                    }

                    const ct_latr2 = document.getElementById(
                        "ct_latr2");
                    var ct2 = formatTaux(result.r2.max
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr2) {
                        ct_latr2.textContent =
                            ct2;
                    }

                    const ct_latr3 = document.getElementById(
                        "ct_latr3");
                    if (ct_latr3) {
                        ct_latr3.textContent = formatTaux(parseFloat(ct2) - parseFloat(ct));
                    }

                    const securise = document.getElementById(
                        "securise");
                    var sec = (result.r2.min
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise) {
                        securise.textContent = formatTaux(sec);
                    }
                    const securise2 = document.getElementById(
                        "securise2");
                    var sec2 = (result.r2.max
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise2) {
                        securise2.textContent = formatTaux(sec2);
                    }

                    const securise3 = document.getElementById(
                        "securise3");
                    if (securise3) {
                        securise3.textContent = formatTaux(parseFloat(sec2) - parseFloat(
                            sec));
                    }



                    const basique = document.getElementById(
                        "basique");
                    var bas = (result.r2.min
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);

                    if (basique) {
                        basique.textContent = formatTaux(bas);
                    }

                    const basique2 = document.getElementById(
                        "basique2");
                    var bas2 = (result.r2.max
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (basique2) {
                        basique2.textContent = formatTaux(bas2);
                    }

                    const basique3 = document.getElementById(
                        "basique3");
                    if (basique3) {
                        basique3.textContent = formatTaux(parseFloat(bas2) - parseFloat(
                            bas));
                    }



                    const non_amelior = document.getElementById(
                        "non_amelior");
                    var non_am = (result.r2.min
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior) {
                        non_amelior.textContent = formatTaux(non_am);
                    }
                    const non_amelior2 = document.getElementById(
                        "non_amelior2");
                    var non_am2 = (result.r2.max
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior2) {
                        non_amelior2.textContent = formatTaux(non_am2);
                    }
                    const non_amelior3 = document.getElementById(
                        "non_amelior3");
                    if (non_amelior3) {
                        non_amelior3.textContent = formatTaux(parseFloat(non_am2) -
                            parseFloat(
                                non_am));
                    }
                    const dal = document.getElementById(
                        "dal");
                    var da1 = result.r2.min
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises;
                    if (dal) {
                        dal.textContent = formatTaux(da1);
                    }
                    const dal2 = document.getElementById(
                        "dal2");
                    var da2 = (result.r2.max
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (dal2) {
                        dal2.textContent = formatTaux(da2);
                    }
                    
                    

                    const dal3 = document.getElementById(
                        "dal3");
                    if (dal3) {
                        dal3.textContent = formatTaux(parseFloat(da2) - parseFloat(da1));
                    }
                    


                    const n_dal2 = document.getElementById(
                        "n_dal2");
                    var n_da2 = result.r2.max
                        .men_dal;
                    if (n_dal2) {
                        n_dal2.textContent =formatNumberReport(n_da2);
                    }
                    const n_dal = document.getElementById(
                        "n_dal");
                    var n_da = result.r2.min
                        .men_dal;
                    if (n_dal) {
                        n_dal.textContent = formatNumberReport(n_da);
                    }
                    const n_dal3 = document.getElementById(
                        "n_dal3");
                    if (n_dal3) {
                        n_dal3.textContent = formatNumberReport(parseFloat(n_da2) -
                            parseFloat(
                                n_da));
                    }

                    const n_amel2 = document.getElementById(
                        "n_amel2");
                    var n_ame2 = result.r2.max
                        .nb_ameliores;
                    if (n_amel2) {
                        n_amel2.textContent = formatNumberReport(n_ame2);
                    }
                    const n_amel = document.getElementById(
                        "n_amel");
                    var n_ame = result.r2.min
                        .nb_ameliores;

                    if (n_amel) {
                        n_amel.textContent = formatNumberReport(n_ame);
                    }
                    const n_amel3 = document.getElementById(
                        "n_amel3");
                    if (n_amel3) {
                        n_amel3.textContent = formatNumberReport(parseFloat(n_ame2) -
                            parseFloat(n_ame));
                    }
                    
                    
                    const limite = document.getElementById(
                        "limite");
                    var lim = (100 - bas - sec - non_am - da1);
                    if (limite) {
                        limite.textContent = formatTaux(lim);
                    }
                    const limite2 = document.getElementById(
                        "limite2");
                    var lim2 = (100 - bas2 - sec2 - non_am2 - da2);
                    if (limite2) {
                        limite2.textContent = formatTaux(lim2);
                    }

                    const limite3 = document.getElementById(
                        "limite3");
                    if (limite3) {
                        limite3.textContent = formatTaux(parseFloat(lim2) - parseFloat(
                            lim));
                    }
                    const taux_hygiene_DLM = document.getElementById(
                        "taux_hygiene_DLM");
                    var hd = formatTaux(result.r2.min
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM) {
                        taux_hygiene_DLM.textContent = hd;
                    }
                    const hygiene_DLM = document.getElementById(
                        "hygiene_DLM");

                    if (hygiene_DLM) {
                        hygiene_DLM.textContent = formatNumberReport(result.r2.min
                            .nbr_menages_dlm);
                    }

                    const taux_hygiene_DLM2 = document.getElementById(
                        "taux_hygiene_DLM2");
                    var hd2 = formatTaux(result.r2.max
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM2) {
                        taux_hygiene_DLM2.textContent = hd2;
                    }

                    const taux_hygiene_DLM3 = document.getElementById(
                        "taux_hygiene_DLM3");
                    if (taux_hygiene_DLM3) {
                        taux_hygiene_DLM3.textContent = formatTaux(parseFloat(hd2) -
                            parseFloat(
                                hd));
                    }
                    const hygiene_DLM2 = document.getElementById(
                        "hygiene_DLM2");

                    if (hygiene_DLM2) {
                        hygiene_DLM2.textContent = formatNumberReport(result.r2.max
                            .nbr_menages_dlm);
                    }
                    const hygiene_DLM3 = document.getElementById(
                        "hygiene_DLM3");
                    if (hygiene_DLM3) {
                        hygiene_DLM3.textContent = formatNumberReport(parseFloat(result.r2
                            .max
                            .nbr_menages_dlm) - parseFloat(result.r2.min
                                .nbr_menages_dlm));
                    }

                    const hygiene_base = document.getElementById(
                        "hygiene_base");
                    var hb = formatTaux(result.r2.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_base) {
                        hygiene_base.textContent = hb;
                    }
                    const hygiene_base2 = document.getElementById(
                        "hygiene_base2");
                    var hb2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_base2) {

                        hygiene_base2.textContent = hb2;
                    }

                    const hygiene_base3 = document.getElementById(
                        "hygiene_base3");
                    if (hygiene_base3) {
                        hygiene_base3.textContent = formatNumberReport(parseFloat(hb2) -
                            parseFloat(hb));
                    }


                    const hygiene_limite = document.getElementById(
                        "hygiene_limite");
                    var hl = formatTaux(result.r2.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_limite) {
                        hygiene_limite.textContent = hl;
                    }
                    const hygiene_limite2 = document.getElementById(
                        "hygiene_limite2");
                    var hl2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_limite2) {
                        hygiene_limite2.textContent = hl2;
                    }

                    const hygiene_limite3 = document.getElementById(
                        "hygiene_limite3");
                    if (hygiene_limite3) {
                        hygiene_limite3.textContent = formatTaux(parseFloat(hl2) -
                            parseFloat(
                                hl));
                    }

                    const hygiene_aucun_service = document.getElementById(
                        "hygiene_aucun_service");
                    var ha = formatTaux(result.r2.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_aucun_service) {
                        hygiene_aucun_service.textContent = ha;
                    }
                    const hygiene_aucun_service2 = document.getElementById(
                        "hygiene_aucun_service2");
                    var ha2 = formatTaux(result.r2.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_aucun_service2) {
                        hygiene_aucun_service2.textContent = ha2;
                    }


                    const hygiene_aucun_service3 = document.getElementById(
                        "hygiene_aucun_service3");
                    if (hygiene_aucun_service3) {
                        hygiene_aucun_service3.textContent = formatTaux(parseFloat(ha2) -
                            parseFloat(ha));
                    }




                    const nb_loc_benefic_appui = document.getElementById(
                        "nb_loc_benefic_appui");
                    if (nb_loc_benefic_appui) {
                        nb_loc_benefic_appui.textContent = formatNumberReport(result
                            .nb_loc_benefic_appui);
                    }

                    const pop2 = document.getElementById(
                        "pop2");
                    if (pop2) {
                        pop2.textContent = formatNumberReport(result.nb_tot_pop);
                    }
                    const men2 = document.getElementById(
                        "men2");
                    if (men2) {
                        men2.textContent = formatNumberReport(result
                            .nb_menages_comptabilises);
                    }


                    const construite2 = document.getElementById(
                        "construite2");
                    if (construite2) {
                        construite2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise);
                    }
                    const auto_const2 = document.getElementById(
                        "auto_const2");
                    if (auto_const2) {
                        auto_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_autoconst);
                    }

                    const re_macon2 = document.getElementById(
                        "re_macon2");
                    if (re_macon2) {
                        re_macon2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise_macon);
                    }
                    const en_const2 = document.getElementById(
                        "en_const2");
                    if (en_const2) {
                        en_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_en_constr);
                    }
                    const phm2 = document.getElementById(
                        "phm2");
                    if (phm2) {
                        phm2.textContent = formatNumberReport(result.r3
                            .nbr_menages_ltr_end_phm);
                    }


                    $('#projets').html('');
                    $.each(result.projets, function (key,
                        value) {
                        $('#projets').append(`
                        
                                    <tr>
                                        
                                        <td>` + value.infra_publ + `</td>
                                   
                                        
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.effectif) + `</td>
                                        <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                        <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    </tr>`);



                    });

                }

            });

            $.ajax({

                url: "{{ url('admin/api/fetch-operateurs2') }}",

                type: "POST",

                data: {
                    projet_id: idProjet,
                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (res) {

                    $('#operateur').html(
                        '<option value="all">TOUS LES OPÉRATEURS</option>');

                    $.each(res.operateurs, function (key, value) {

                        $("#operateur").append('<option value="' + value

                            .id + '">' + value.intitule + '</option>');

                    });

                }

            });
        });
    });
    $('#operateur').on('change', function () {
$('#type').val(null).trigger('change');
        var idOperateur = this.value;
        var idProjet = $('#projet').val();
        if (idOperateur === 'all') {
            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_projet_api') }}",

                type: "POST",

                data: {

                    projet_id: idProjet,

                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (result) {

                    $('#content').show();
                    var chart1 = result.fdal;

                    var labels = [];
                    var data1 = [];
                    var data2 = [];
                    $.each(chart1, function (key, value) {
                        labels.push(value.year);
                        data1.push(value.nbr_localites_declenchees);
                        data2.push(value.nbr_localites_certifiees);
                    })
                    if (typeof chart_1 !== 'undefined' && chart_1 !== null) {
                        chart_1.destroy();
                    }
                    var chart1 = {
                        labels: labels,
                        datasets: [{
                            label: @json(__('tb.op_declechement')),
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
                            label: @json(__('tb.op_fdal')),
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
                    var chart1Canvas = $('#chart1').get(0).getContext('2d')


                    chart_1 = new Chart(chart1Canvas, {
                        type: 'bar',
                        data: chart1,
                        options: {
                            plugins: {
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    color: '#f39c12',
                                    display: function (context) {
                                        return context.dataset.data[context
                                            .dataIndex] > 0;
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

                    const rapport = document.getElementById(
                        "rapport");
                    if (rapport) {
                        rapport.textContent = 'Rapport du projet ' + result
                            .g.projet;
                    }
                    const info_projet = document.getElementById(
                        "info_projet");
                    if (info_projet) {
                        info_projet.textContent = result
                            .g.projet;
                    }
                    const info_periode = document.getElementById(
                        "info_periode");
                    if (info_periode) {
                        info_periode.textContent = (result
                            .g.min_year + '-' + result
                                .g.max_year);
                    }
                    $('#info_zone').html('');
                    $.each(result.g2, function (key,
                        value) {

                        $('#info_zone').append(`<ul><li>` +
                            value.wilaya + `</li>     
                                </ul>`);
                    });
                    $('#info_operateurs').html('');
                    $.each(result.g3, function (key,
                        value) {
                        $('#info_operateurs').append(`<ul><li>` +
                            value.operateur + `</li>     
                                </ul>`);
                    });

                    $('#info_zone_pub').html('');
                    $.each(result.g2_infrastructures, function (key,
                        value) {
                        $('#info_zone_pub').append(`<ul><li>` +
                            value.wilaya + `</li>     
                                </ul>`);
                    });

                    $('#info_operateurs_pub').html('');
                    $.each(result.g3_infrastructures, function (key,
                        value) {
                        $('#info_operateurs_pub').append(`<ul><li>` +
                            value.operateur + `</li>     
                                </ul>`);
                    });
                    const villages_declenches = document.getElementById(
                        "villages_declenches");
                    if (villages_declenches) {
                        villages_declenches.textContent = formatNumberReport(result
                            .villages_declenches);
                    }
                    const villages_redeclenches = document.getElementById(
                        "villages_redeclenches");
                    if (villages_redeclenches) {
                        villages_redeclenches.textContent = formatNumberReport(result
                            .villages_redeclenches);
                    }
                    const pop1 = document.getElementById(
                        "pop1");
                    if (pop1) {
                        pop1.textContent = formatNumberReport(result
                            .population);
                    }
                    const men1 = document.getElementById(
                        "men1");
                    if (men1) {
                        men1.textContent = formatNumberReport(result
                            .menages);
                    }
                    var cumul_nbr_localites_declenchees = 0;
                    var cumul_nbr_localites_certifiees = 0;

                    $('#fdal').html('');
                    $.each(result.fdal, function (key,
                        value) {
                        cumul_nbr_localites_declenchees =
                            cumul_nbr_localites_declenchees + parseFloat(value
                                .nbr_localites_declenchees);
                        cumul_nbr_localites_certifiees =
                            cumul_nbr_localites_certifiees + parseFloat(value
                                .nbr_localites_certifiees);

                        $('#fdal').append(

                            `<tr>
                                        
                                        <td>` + value.year + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_declenchees) + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_certifiees) + `</td>
                                        <td bgcolor="gray"></td>
                                        <td bgcolor="gray"></td>
                                        
                                    </tr>`);
                    });
                    $('#fdal').append(`<tr><td>Cumul</td><td> ` +
                        formatNumberReport(cumul_nbr_localites_declenchees) + ` </td> 
                            <td> ` + formatNumberReport(cumul_nbr_localites_certifiees) + ` </td><td > ` +
                        formatTaux(
                            cumul_nbr_localites_certifiees * 100 /
                            cumul_nbr_localites_declenchees) + ` </td> <td > ` +
                        formatNumberReport(cumul_nbr_localites_declenchees -
                            cumul_nbr_localites_certifiees) + ` </td></tr>`);



                    const construite = document.getElementById(
                        "construite");
                    if (construite) {
                        construite.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise);
                    }
                    const localite_construite = document.getElementById(
                        "localite_construite");
                    if (localite_construite) {
                        localite_construite.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise);
                    }
                    const auto_const = document.getElementById(
                        "auto_const");
                    if (auto_const) {
                        auto_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_autoconst);
                    }
                    const localite_auto_const = document.getElementById(
                        "localite_auto_const");
                    if (localite_auto_const) {
                        localite_auto_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autoconst);
                    }

                    const re_macon = document.getElementById(
                        "re_macon");
                    if (re_macon) {
                        re_macon.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise_macon);
                    }
                    const localite_macon = document.getElementById(
                        "localite_macon");
                    if (localite_macon) {
                        localite_macon.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise_macon);
                    }
                    const en_const = document.getElementById(
                        "en_const");
                    if (en_const) {
                        en_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_en_constr);
                    }
                    const localite_en_const = document.getElementById(
                        "localite_en_const");
                    if (localite_en_const) {
                        localite_en_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_en_constr);
                    }
                    const phm = document.getElementById(
                        "phm");
                    if (phm) {
                        phm.textContent = formatNumberReport(result.r
                            .nbr_menages_ltr_end_phm);
                    }
                    const localite_phm = document.getElementById(
                        "localite_phm");
                    if (localite_phm) {
                        localite_phm.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_menages_ltr_end_phm);
                    }



                    $('#financement').html('');
                                        $('#financement').append(
                        `<tr><td>Nombre de latrines auto-financées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_autofin) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autofin) + `</td></tr>
                            <tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(100 - result.r
                                .taux_latrine_subv * 100)  +
                        `</td><td></td></tr>
                            <tr><td>Nombre de latrines réalisées pour lesquelles les données d'investissement ont été recueillies</td><td>` +
                        formatNumberReport(result.r
                            .nb_latr_invest_men_dispo) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nb_latr_invest_men_dispo) +
                        `</td></tr>
                            <tr><td>Montant total investi par les ménages dans leurs latrines (MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_invest_men) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_mont_invest_men) +
                        `</td></tr>
                            <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_moyen_invest_men) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines financées par entraide communautaire</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_entraid) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_entraid) + `</td></tr>
                            <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_entraid) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines subventionnées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_subv) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_subv) + `</td></tr>
                            <tr><td>Taux de latrines subventionnées</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_subv) +
                        `</td><td></td></tr>`
                    );


                    
                    ///////////////

                    const post_n_dal2 = document.getElementById(
                        "post_n_dal2");
                    var post_n_da2 = result.r4.max
                        .men_dal;

                    if (post_n_dal2) {
                        post_n_dal2.textContent = formatNumberReport(post_n_da2);
                    }
                    const post_n_dal = document.getElementById(
                        "post_n_dal");
                    var post_n_da = result.r4.min
                        .men_dal;
                    if (post_n_dal) {
                        post_n_dal.textContent = formatNumberReport(post_n_da);
                    }
                    const post_n_dal3 = document.getElementById(
                        "post_n_dal3");
                    if (post_n_dal3) {
                        post_n_dal3.textContent = formatNumberReport(parseFloat(
                            post_n_da2) -
                            parseFloat(
                                post_n_da));
                    }

                    const post_n_amel2 = document.getElementById(
                        "post_n_amel2");
                    var post_n_ame2 = result.r4.max
                        .nb_ameliores;
                    if (post_n_amel2) {
                        post_n_amel2.textContent = formatNumberReport(post_n_ame2);
                    }
                    const post_n_amel = document.getElementById(
                        "post_n_amel");
                    var post_n_ame = result.r4.min
                        .nb_ameliores;
                    if (post_n_amel) {
                        post_n_amel.textContent = formatNumberReport(post_n_ame);
                    }
                    const post_n_amel3 = document.getElementById(
                        "post_n_amel3");
                    if (post_n_amel3) {
                        post_n_amel3.textContent = formatNumberReport(parseFloat(
                            post_n_ame2) - parseFloat(
                                post_n_ame));
                    }

                    const post_nt_latr = document.getElementById(
                        "post_nt_latr");
                    if (post_nt_latr) {
                        post_nt_latr.textContent = formatNumberReport(result.r4.min
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr2 = document.getElementById(
                        "post_nt_latr2");
                    if (post_nt_latr2) {
                        post_nt_latr2.textContent = formatNumberReport(result.r4.max
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr3 = document.getElementById(
                        "post_nt_latr3");
                    if (post_nt_latr3) {
                        post_nt_latr3.textContent = formatNumberReport(parseFloat(result.r4
                            .max
                            .nb_tot_latr_fam) - parseFloat(result.r4.min
                                .nb_tot_latr_fam));
                    }

                    const post_ct_latr = document.getElementById(
                        "post_ct_latr");
                    var post_ct = formatTaux(result.r4.min
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr) {
                        post_ct_latr.textContent = post_ct;
                    }

                    const post_ct_latr2 = document.getElementById(
                        "post_ct_latr2");
                    var post_ct2 = formatTaux(result.r4.max
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr2) {
                        post_ct_latr2.textContent =
                            post_ct2;
                    }

                    const post_ct_latr3 = document.getElementById(
                        "post_ct_latr3");
                    if (post_ct_latr3) {
                        post_ct_latr3.textContent = formatTaux(parseFloat(post_ct2) -
                            parseFloat(post_ct));
                    }

                    const post_securise = document.getElementById(
                        "post_securise");
                    var post_sec = formatTaux(result.r4.min
                        .securise * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_securise) {
                        post_securise.textContent = post_sec;
                    }
                    const post_securise2 = document.getElementById(
                        "post_securise2");
                    var post_sec2 = formatTaux(result.r4.max
                        .securise * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_securise2) {
                        post_securise2.textContent = post_sec2;
                    }

                    const post_securise3 = document.getElementById(
                        "post_securise3");
                    if (post_securise3) {
                        post_securise3.textContent = formatNumberReport(parseFloat(
                            post_sec2) - parseFloat(
                                post_sec));
                    }
                    const post_basique = document.getElementById(
                        "post_basique");
                    var post_bas = formatTaux(result.r4.min
                        .basique * 100 / result.total_menages);

                    if (post_basique) {
                        post_basique.textContent = post_bas;
                    }

                    const post_basique2 = document.getElementById(
                        "post_basique2");
                    var post_bas2 = formatTaux(result.r4.max
                        .basique * 100 / result.total_menages);
                    if (post_basique2) {
                        post_basique2.textContent = post_bas2;
                    }

                    const post_basique3 = document.getElementById(
                        "post_basique3");
                    if (post_basique3) {
                        post_basique3.textContent = formatTaux(parseFloat(post_bas2) -
                            parseFloat(
                                post_bas));
                    }


                    const post_non_amelior = document.getElementById(
                        "post_non_amelior");
                    var post_non_am = formatTaux(result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior) {
                        post_non_amelior.textContent = post_non_am;
                    }

                    const post_non_amelior2 = document.getElementById(
                        "post_non_amelior2");
                    var post_non_am2 = formatTaux(result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior2) {
                        post_non_amelior2.textContent = post_non_am2;
                    }
                    const post_non_amelior3 = document.getElementById(
                        "post_non_amelior3");
                    if (post_non_amelior3) {
                        post_non_amelior3.textContent = formatTaux(parseFloat(
                            post_non_am2) - parseFloat(
                                post_non_am));
                    }
                    const post_dal = document.getElementById(
                        "post_dal");
                    var post_da1 = formatTaux(result.r4.min
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal) {
                        post_dal.textContent = post_da1;
                    }
                    const post_dal2 = document.getElementById(
                        "post_dal2");
                    var post_da2 = formatTaux(result.r4.max
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal2) {
                        post_dal2.textContent = post_da2;
                    }

                    const post_dal3 = document.getElementById(
                        "post_dal3");
                    if (post_dal3) {
                        post_dal3.textContent = formatTaux(parseFloat(post_da2) -
                            parseFloat(
                                post_da1));
                    }

                    const post_limite = document.getElementById(
                        "post_limite");
                    var post_lim = formatTaux(100 - (result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.min
                                .basique * 100 / result
                                    .total_menages) - (result.r4.min
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite) {
                        post_limite.textContent = post_lim;
                    }
                    const post_limite2 = document.getElementById(
                        "post_limite2");

                    var post_lim2 = formatTaux(100 - (result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.max
                                .basique * 100 / result
                                    .total_menages) - (result.r4.max
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite2) {
                        post_limite2.textContent = post_lim2;
                    }

                    const post_limite3 = document.getElementById(
                        "post_limite3");
                    if (post_limite3) {
                        post_limite3.textContent = formatTaux(parseFloat(
                            post_lim2) -
                            parseFloat(post_lim));
                    }
                    const post_taux_hygiene_DLM = document.getElementById(
                        "post_taux_hygiene_DLM");
                    var post_hd = formatTaux(result.r4.min
                        .nbr_menages_dlm * 100 / result.r4.min
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM) {
                        post_taux_hygiene_DLM.textContent = post_hd;
                    }
                    const post_hygiene_DLM = document.getElementById(
                        "post_hygiene_DLM");

                    if (post_hygiene_DLM) {
                        post_hygiene_DLM.textContent = formatNumberReport(result.r4.min
                            .nbr_menages_dlm);
                    }

                    const post_taux_hygiene_DLM2 = document.getElementById(
                        "post_taux_hygiene_DLM2");
                    var post_hd2 = formatTaux(result.r4.max
                        .nbr_menages_dlm * 100 / result.r4.max
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM2) {
                        post_taux_hygiene_DLM2.textContent = post_hd2;
                    }

                    const post_taux_hygiene_DLM3 = document.getElementById(
                        "post_taux_hygiene_DLM3");
                    if (post_taux_hygiene_DLM3) {
                        post_taux_hygiene_DLM3.textContent = formatTaux(parseFloat(
                            post_hd2) -
                            parseFloat(
                                post_hd));
                    }
                    const post_hygiene_DLM2 = document.getElementById(
                        "post_hygiene_DLM2");

                    if (post_hygiene_DLM2) {
                        post_hygiene_DLM2.textContent = formatNumberReport(result.r4.max
                            .nbr_menages_dlm);
                    }
                    const post_hygiene_DLM3 = document.getElementById(
                        "post_hygiene_DLM3");
                    if (post_hygiene_DLM3) {
                        post_hygiene_DLM3.textContent = formatNumberReport(parseFloat(result
                            .r4.max
                            .nbr_menages_dlm) - parseFloat(result.r4.min
                                .nbr_menages_dlm));
                    }

                    const post_hygiene_base = document.getElementById(
                        "post_hygiene_base");
                    var post_hb = formatTaux(result.r4.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_base) {
                        post_hygiene_base.textContent = post_hb;
                    }
                    const post_hygiene_base2 = document.getElementById(
                        "post_hygiene_base2");
                    var post_hb2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_base2) {

                        post_hygiene_base2.textContent = post_hb2;
                    }

                    const post_hygiene_base3 = document.getElementById(
                        "post_hygiene_base3");
                    if (post_hygiene_base3) {
                        post_hygiene_base3.textContent = formatTaux(parseFloat(post_hb2) -
                            parseFloat(
                                post_hb));
                    }


                    const post_hygiene_limite = document.getElementById(
                        "post_hygiene_limite");
                    var post_hl = formatTaux(result.r4.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_limite) {
                        post_hygiene_limite.textContent = post_hl;
                    }
                    const post_hygiene_limite2 = document.getElementById(
                        "post_hygiene_limite2");
                    var post_hl2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_limite2) {
                        post_hygiene_limite2.textContent = post_hl2;
                    }

                    const post_hygiene_limite3 = document.getElementById(
                        "post_hygiene_limite3");
                    if (post_hygiene_limite3) {
                        post_hygiene_limite3.textContent = formatTaux(parseFloat(post_hl2) -
                            parseFloat(
                                post_hl));
                    }

                    const post_hygiene_aucun_service = document.getElementById(
                        "post_hygiene_aucun_service");
                    var post_ha = formatTaux(result.r4.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service) {
                        post_hygiene_aucun_service.textContent = post_ha;
                    }
                    const post_hygiene_aucun_service2 = document.getElementById(
                        "post_hygiene_aucun_service2");
                    var post_ha2 = formatTaux(result.r4.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service2) {
                        post_hygiene_aucun_service2.textContent = post_ha2;
                    }


                    const post_hygiene_aucun_service3 = document.getElementById(
                        "post_hygiene_aucun_service3");
                    if (post_hygiene_aucun_service3) {
                        post_hygiene_aucun_service3.textContent = formatTaux(parseFloat(
                            post_ha2) -
                            parseFloat(post_ha));
                    }
                    ////////////////
                    const nt_latr = document.getElementById(
                        "nt_latr");
                    if (nt_latr) {
                        nt_latr.textContent = formatNumberReport(result.r2.min
                            .nb_tot_latr_fam);
                    }
                    const nt_latr2 = document.getElementById(
                        "nt_latr2");
                    if (nt_latr2) {
                        nt_latr2.textContent = formatNumberReport(result.r2.max
                            .nb_tot_latr_fam);
                    }
                    const nt_latr3 = document.getElementById(
                        "nt_latr3");
                    if (nt_latr3) {
                        nt_latr3.textContent = formatNumberReport(parseInt(result.r2.max
                            .nb_tot_latr_fam) - parseInt(result.r2.min
                                .nb_tot_latr_fam));
                    }

                    const ct_latr = document.getElementById(
                        "ct_latr");
                    var ct = formatTaux(result.r2.min
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr) {
                        ct_latr.textContent = ct;
                    }

                    const ct_latr2 = document.getElementById(
                        "ct_latr2");
                    var ct2 = formatTaux(result.r2.max
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr2) {
                        ct_latr2.textContent =
                            ct2;
                    }

                    const ct_latr3 = document.getElementById(
                        "ct_latr3");
                    if (ct_latr3) {
                        ct_latr3.textContent = formatTaux(parseFloat(ct2) - parseFloat(ct));
                    }

                    const securise = document.getElementById(
                        "securise");
                    var sec = (result.r2.min
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise) {
                        securise.textContent = formatTaux(sec);
                    }
                    const securise2 = document.getElementById(
                        "securise2");
                    var sec2 = (result.r2.max
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise2) {
                        securise2.textContent = formatTaux(sec2);
                    }

                    const securise3 = document.getElementById(
                        "securise3");
                    if (securise3) {
                        securise3.textContent = formatTaux(parseFloat(sec2) - parseFloat(
                            sec));
                    }



                    const basique = document.getElementById(
                        "basique");
                    var bas = (result.r2.min
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);

                    if (basique) {
                        basique.textContent = formatTaux(bas);
                    }

                    const basique2 = document.getElementById(
                        "basique2");
                    var bas2 = (result.r2.max
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (basique2) {
                        basique2.textContent = formatTaux(bas2);
                    }

                    const basique3 = document.getElementById(
                        "basique3");
                    if (basique3) {
                        basique3.textContent = formatTaux(parseFloat(bas2) - parseFloat(
                            bas));
                    }



                    const non_amelior = document.getElementById(
                        "non_amelior");
                    var non_am = (result.r2.min
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior) {
                        non_amelior.textContent = formatTaux(non_am);
                    }
                    const non_amelior2 = document.getElementById(
                        "non_amelior2");
                    var non_am2 = (result.r2.max
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior2) {
                        non_amelior2.textContent = formatTaux(non_am2);
                    }
                    const non_amelior3 = document.getElementById(
                        "non_amelior3");
                    if (non_amelior3) {
                        non_amelior3.textContent = formatTaux(parseFloat(non_am2) -
                            parseFloat(
                                non_am));
                    }
                    const dal = document.getElementById(
                        "dal");
                    var da1 = result.r2.min
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises;
                    if (dal) {
                        dal.textContent = formatTaux(da1);
                    }
                    const dal2 = document.getElementById(
                        "dal2");
                    var da2 = (result.r2.max
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (dal2) {
                        dal2.textContent = formatTaux(da2);
                    }
                    
                    

                    const dal3 = document.getElementById(
                        "dal3");
                    if (dal3) {
                        dal3.textContent = formatTaux(parseFloat(da2) - parseFloat(da1));
                    }
                    


                    const n_dal2 = document.getElementById(
                        "n_dal2");
                    var n_da2 = result.r2.max
                        .men_dal;
                    if (n_dal2) {
                        n_dal2.textContent =formatNumberReport(n_da2);
                    }
                    const n_dal = document.getElementById(
                        "n_dal");
                    var n_da = result.r2.min
                        .men_dal;
                    if (n_dal) {
                        n_dal.textContent = formatNumberReport(n_da);
                    }
                    const n_dal3 = document.getElementById(
                        "n_dal3");
                    if (n_dal3) {
                        n_dal3.textContent = formatNumberReport(parseFloat(n_da2) -
                            parseFloat(
                                n_da));
                    }

                    const n_amel2 = document.getElementById(
                        "n_amel2");
                    var n_ame2 = result.r2.max
                        .nb_ameliores;
                    if (n_amel2) {
                        n_amel2.textContent = formatNumberReport(n_ame2);
                    }
                    const n_amel = document.getElementById(
                        "n_amel");
                    var n_ame = result.r2.min
                        .nb_ameliores;

                    if (n_amel) {
                        n_amel.textContent = formatNumberReport(n_ame);
                    }
                    const n_amel3 = document.getElementById(
                        "n_amel3");
                    if (n_amel3) {
                        n_amel3.textContent = formatNumberReport(parseFloat(n_ame2) -
                            parseFloat(n_ame));
                    }
                    
                    
                    const limite = document.getElementById(
                        "limite");
                    var lim = (100 - bas - sec - non_am - da1);
                    if (limite) {
                        limite.textContent = formatTaux(lim);
                    }
                    const limite2 = document.getElementById(
                        "limite2");
                    var lim2 = (100 - bas2 - sec2 - non_am2 - da2);
                    if (limite2) {
                        limite2.textContent = formatTaux(lim2);
                    }

                    const limite3 = document.getElementById(
                        "limite3");
                    if (limite3) {
                        limite3.textContent = formatTaux(parseFloat(lim2) - parseFloat(
                            lim));
                    }
                    const taux_hygiene_DLM = document.getElementById(
                        "taux_hygiene_DLM");
                    var hd = formatTaux(result.r2.min
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM) {
                        taux_hygiene_DLM.textContent = hd;
                    }
                    const hygiene_DLM = document.getElementById(
                        "hygiene_DLM");

                    if (hygiene_DLM) {
                        hygiene_DLM.textContent = formatNumberReport(result.r2.min
                            .nbr_menages_dlm);
                    }

                    const taux_hygiene_DLM2 = document.getElementById(
                        "taux_hygiene_DLM2");
                    var hd2 = formatTaux(result.r2.max
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM2) {
                        taux_hygiene_DLM2.textContent = hd2;
                    }

                    const taux_hygiene_DLM3 = document.getElementById(
                        "taux_hygiene_DLM3");
                    if (taux_hygiene_DLM3) {
                        taux_hygiene_DLM3.textContent = formatTaux(parseFloat(hd2) -
                            parseFloat(
                                hd));
                    }
                    const hygiene_DLM2 = document.getElementById(
                        "hygiene_DLM2");

                    if (hygiene_DLM2) {
                        hygiene_DLM2.textContent = formatNumberReport(result.r2.max
                            .nbr_menages_dlm);
                    }
                    const hygiene_DLM3 = document.getElementById(
                        "hygiene_DLM3");
                    if (hygiene_DLM3) {
                        hygiene_DLM3.textContent = formatNumberReport(parseFloat(result.r2
                            .max
                            .nbr_menages_dlm) - parseFloat(result.r2.min
                                .nbr_menages_dlm));
                    }

                    const hygiene_base = document.getElementById(
                        "hygiene_base");
                    var hb = formatTaux(result.r2.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_base) {
                        hygiene_base.textContent = hb;
                    }
                    const hygiene_base2 = document.getElementById(
                        "hygiene_base2");
                    var hb2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_base2) {

                        hygiene_base2.textContent = hb2;
                    }

                    const hygiene_base3 = document.getElementById(
                        "hygiene_base3");
                    if (hygiene_base3) {
                        hygiene_base3.textContent = formatNumberReport(parseFloat(hb2) -
                            parseFloat(hb));
                    }


                    const hygiene_limite = document.getElementById(
                        "hygiene_limite");
                    var hl = formatTaux(result.r2.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_limite) {
                        hygiene_limite.textContent = hl;
                    }
                    const hygiene_limite2 = document.getElementById(
                        "hygiene_limite2");
                    var hl2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_limite2) {
                        hygiene_limite2.textContent = hl2;
                    }

                    const hygiene_limite3 = document.getElementById(
                        "hygiene_limite3");
                    if (hygiene_limite3) {
                        hygiene_limite3.textContent = formatTaux(parseFloat(hl2) -
                            parseFloat(
                                hl));
                    }

                    const hygiene_aucun_service = document.getElementById(
                        "hygiene_aucun_service");
                    var ha = formatTaux(result.r2.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_aucun_service) {
                        hygiene_aucun_service.textContent = ha;
                    }
                    const hygiene_aucun_service2 = document.getElementById(
                        "hygiene_aucun_service2");
                    var ha2 = formatTaux(result.r2.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_aucun_service2) {
                        hygiene_aucun_service2.textContent = ha2;
                    }


                    const hygiene_aucun_service3 = document.getElementById(
                        "hygiene_aucun_service3");
                    if (hygiene_aucun_service3) {
                        hygiene_aucun_service3.textContent = formatTaux(parseFloat(ha2) -
                            parseFloat(ha));
                    }




                    const nb_loc_benefic_appui = document.getElementById(
                        "nb_loc_benefic_appui");
                    if (nb_loc_benefic_appui) {
                        nb_loc_benefic_appui.textContent = formatNumberReport(result
                            .nb_loc_benefic_appui);
                    }

                    const pop2 = document.getElementById(
                        "pop2");
                    if (pop2) {
                        pop2.textContent = formatNumberReport(result.nb_tot_pop);
                    }
                    const men2 = document.getElementById(
                        "men2");
                    if (men2) {
                        men2.textContent = formatNumberReport(result
                            .nb_menages_comptabilises);
                    }


                    const construite2 = document.getElementById(
                        "construite2");
                    if (construite2) {
                        construite2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise);
                    }
                    const auto_const2 = document.getElementById(
                        "auto_const2");
                    if (auto_const2) {
                        auto_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_autoconst);
                    }

                    const re_macon2 = document.getElementById(
                        "re_macon2");
                    if (re_macon2) {
                        re_macon2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise_macon);
                    }
                    const en_const2 = document.getElementById(
                        "en_const2");
                    if (en_const2) {
                        en_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_en_constr);
                    }
                    const phm2 = document.getElementById(
                        "phm2");
                    if (phm2) {
                        phm2.textContent = formatNumberReport(result.r3
                            .nbr_menages_ltr_end_phm);
                    }


                    $('#projets').html('');
                    $.each(result.projets, function (key,
                        value) {
                        $('#projets').append(`
                        
                                    <tr>
                                        
                                        <td>` + value.infra_publ + `</td>
                                   
                                        
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.effectif) + `</td>
                                        <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                        <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    </tr>`);

                    });


                }

            });
        } else {
            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_projet_api') }}",

                type: "POST",

                data: {

                    projet_id: idProjet,
                    operateur_id: idOperateur,
                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (result) {


                    $('#content').show();
                    var chart1 = result.fdal;

                    var labels = [];
                    var data1 = [];
                    var data2 = [];
                    $.each(chart1, function (key, value) {
                        labels.push(value.year);
                        data1.push(value.nbr_localites_declenchees);
                        data2.push(value.nbr_localites_certifiees);
                    })
                    if (typeof chart_1 !== 'undefined' && chart_1 !== null) {
                        chart_1.destroy();
                    }
                    var chart1 = {
                        labels: labels,
                        datasets: [{
                            label: @json(__('tb.op_declechement')),
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
                            label: @json(__('tb.op_fdal')),
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
                    var chart1Canvas = $('#chart1').get(0).getContext('2d')


                    chart_1 = new Chart(chart1Canvas, {
                        type: 'bar',
                        data: chart1,
                        options: {
                            plugins: {
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    color: '#f39c12',
                                    display: function (context) {
                                        return context.dataset.data[context
                                            .dataIndex] > 0;
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
                    const rapport = document.getElementById(
                        "rapport");

                    if (rapport) {
                        rapport.textContent = "Rapport de l'ONG " + result.g3.operateur +
                            " dans le cadre du projet " + result.g.projet;
                    }
                    const info_projet = document.getElementById(
                        "info_projet");
                    if (info_projet) {
                        info_projet.textContent = result
                            .g.projet;
                    }
                    const info_periode = document.getElementById(
                        "info_periode");
                    if (info_periode) {
                        info_periode.textContent = (result
                            .g.min_year + '-' + result
                                .g.max_year);
                    }
                    $('#info_zone').html('');
                    $.each(result.g2, function (key,
                        value) {

                        $('#info_zone').append(`<ul><li>` +
                            value.wilaya + `</li>     
                </ul>`);
                    });
                    $('#info_operateurs').html('');
                    $('#info_operateurs').append(`<ul><li>` +
                        result.g3.operateur + `</li>     
                </ul>`);

                    $('#info_zone_pub').html('');
                    $.each(result.g2_infrastructures, function (key,
                        value) {
                        $('#info_zone_pub').append(`<ul><li>` +
                            value.wilaya + `</li>     
        </ul>`);
                    });
                    $('#info_operateurs_pub').html('');
                    $.each(result.g3_infrastructures, function (key,
                        value) {
                        $('#info_operateurs_pub').append(`<ul><li>` +
                            value.operateur + `</li>     
        </ul>`);
                    });
                    const villages_declenches = document.getElementById(
                        "villages_declenches");
                    if (villages_declenches) {
                        villages_declenches.textContent = formatNumberReport(result
                            .villages_declenches);
                    }
                    const villages_redeclenches = document.getElementById(
                        "villages_redeclenches");
                    if (villages_redeclenches) {
                        villages_redeclenches.textContent = formatNumberReport(result
                            .villages_redeclenches);
                    }
                    const pop1 = document.getElementById(
                        "pop1");
                    if (pop1) {
                        pop1.textContent = formatNumberReport(result
                            .population);
                    }
                    const men1 = document.getElementById(
                        "men1");
                    if (men1) {
                        men1.textContent = formatNumberReport(result
                            .menages);
                    }
                    var cumul_nbr_localites_declenchees = 0;
                    var cumul_nbr_localites_certifiees = 0;

                    $('#fdal').html('');
                    $.each(result.fdal, function (key,
                        value) {
                        cumul_nbr_localites_declenchees =
                            cumul_nbr_localites_declenchees + parseFloat(value
                                .nbr_localites_declenchees);
                        cumul_nbr_localites_certifiees =
                            cumul_nbr_localites_certifiees + parseFloat(value
                                .nbr_localites_certifiees);

                        $('#fdal').append(

                            `<tr>
                                        
                                        <td>` + value.year + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_declenchees) + `</td>
                                        <td>` + formatNumberReport(value.nbr_localites_certifiees) + `</td>
                                        <td bgcolor="gray"></td>
                                        <td bgcolor="gray"></td>
                                        
                                    </tr>`);
                    });
                    $('#fdal').append(`<tr><td>Cumul</td><td> ` +
                        formatNumberReport(cumul_nbr_localites_declenchees) + ` </td> 
                            <td> ` + formatNumberReport(cumul_nbr_localites_certifiees) + ` </td><td > ` +
                        formatTaux(
                            cumul_nbr_localites_certifiees * 100 /
                            cumul_nbr_localites_declenchees) + ` </td> <td > ` +
                        formatNumberReport(cumul_nbr_localites_declenchees -
                            cumul_nbr_localites_certifiees) + ` </td></tr>`);


                    $('#dynamic').html('');


                    $('#dynamic').append(
                        `<tr><td>Nombre de nouvelles latrines construites</td><td>` +
                        result.r3
                            .nbr_latrine_realise + `</td><td>` +
                        result.r3
                            .nbr_localite_latrine_realise + `</td></tr>
                            <tr><td>Taux de latrines auto-construites</td><td>` +
                        result.r3
                            .nbr_latrine_autoconst + `</td><td>` +
                        result.r3
                            .nbr_localite_latrine_autoconst +
                        `</td></tr>
                            <tr><td>Taux de latrines réalisées avec l'aide d'un maçon</td><td>` +
                        result.r3
                            .nbr_latrine_realise_macon + `</td><td>` +
                        result.r3
                            .nbr_localite_latrine_realise_macon +
                        `</td></tr>
                            
                            <tr><td>Nombre de latrines familiales en construction en fin de projet</td><td>` +
                        result.r3
                            .nbr_latrine_en_constr + `</td><td>` +
                        result.r3
                            .nbr_localite_latrine_en_constr +
                        `</td></tr>
                            <tr><td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes</td><td>` +
                        result.r3
                            .nbr_menages_ltr_end_phm +
                        `</td><td>` +
                        result.r3
                            .nbr_localite_menages_ltr_end_phm +
                        `</td></tr>`
                    );
                    
                     const construite = document.getElementById(
                        "construite");
                    if (construite) {
                        construite.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise);
                    }
                    const localite_construite = document.getElementById(
                        "localite_construite");
                    if (localite_construite) {
                        localite_construite.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise);
                    }
                    const auto_const = document.getElementById(
                        "auto_const");
                    if (auto_const) {
                        auto_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_autoconst);
                    }
                    const localite_auto_const = document.getElementById(
                        "localite_auto_const");
                    if (localite_auto_const) {
                        localite_auto_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autoconst);
                    }

                    const re_macon = document.getElementById(
                        "re_macon");
                    if (re_macon) {
                        re_macon.textContent = formatNumberReport(result.r
                            .nbr_latrine_realise_macon);
                    }
                    const localite_macon = document.getElementById(
                        "localite_macon");
                    if (localite_macon) {
                        localite_macon.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_realise_macon);
                    }
                    const en_const = document.getElementById(
                        "en_const");
                    if (en_const) {
                        en_const.textContent = formatNumberReport(result.r
                            .nbr_latrine_en_constr);
                    }
                    const localite_en_const = document.getElementById(
                        "localite_en_const");
                    if (localite_en_const) {
                        localite_en_const.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_en_constr);
                    }
                    const phm = document.getElementById(
                        "phm");
                    if (phm) {
                        phm.textContent = formatNumberReport(result.r
                            .nbr_menages_ltr_end_phm);
                    }
                    const localite_phm = document.getElementById(
                        "localite_phm");
                    if (localite_phm) {
                        localite_phm.textContent = formatNumberReport(result.r
                            .nbr_localite_nbr_menages_ltr_end_phm);
                    }


                    $('#financement').html('');
                    $('#financement').append(
                        `<tr><td>Nombre de latrines auto-financées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_autofin) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_autofin) + `</td></tr>
                            <tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(100 - result.r
                                .taux_latrine_subv * 100)  +
                        `</td><td></td></tr>
                            <tr><td>Nombre de latrines réalisées pour lesquelles les données d'investissement ont été recueillies</td><td>` +
                        formatNumberReport(result.r
                            .nb_latr_invest_men_dispo) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nb_latr_invest_men_dispo) +
                        `</td></tr>
                            <tr><td>Montant total investi par les ménages dans leurs latrines (MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_invest_men) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_mont_invest_men) +
                        `</td></tr>
                            <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                        formatNumberReport(result.r
                            .mont_moyen_invest_men) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines financées par entraide communautaire</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_entraid) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_entraid) + `</td></tr>
                            <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_entraid) + `</td><td></td></tr>
                            <tr><td>Nombre de latrines subventionnées</td><td>` +
                        formatNumberReport(result.r
                            .nbr_latrine_subv) + `</td><td>` +
                        formatNumberReport(result.r
                            .nbr_localite_nbr_latrine_subv) + `</td></tr>
                            <tr><td>Taux de latrines subventionnées</td><td>` +
                        formatTaux(result.r
                            .taux_latrine_subv) +
                        `</td><td></td></tr>`
                    );
                    ///////////////
                   
                    ///////////////

                    const post_n_dal2 = document.getElementById(
                        "post_n_dal2");
                    var post_n_da2 = result.r4.max
                        .men_dal;

                    if (post_n_dal2) {
                        post_n_dal2.textContent = formatNumberReport(post_n_da2);
                    }
                    const post_n_dal = document.getElementById(
                        "post_n_dal");
                    var post_n_da = result.r4.min
                        .men_dal;
                    if (post_n_dal) {
                        post_n_dal.textContent = formatNumberReport(post_n_da);
                    }
                    const post_n_dal3 = document.getElementById(
                        "post_n_dal3");
                    if (post_n_dal3) {
                        post_n_dal3.textContent = formatNumberReport(parseFloat(
                            post_n_da2) -
                            parseFloat(
                                post_n_da));
                    }

                    const post_n_amel2 = document.getElementById(
                        "post_n_amel2");
                    var post_n_ame2 = result.r4.max
                        .nb_ameliores;
                    if (post_n_amel2) {
                        post_n_amel2.textContent = formatNumberReport(post_n_ame2);
                    }
                    const post_n_amel = document.getElementById(
                        "post_n_amel");
                    var post_n_ame = result.r4.min
                        .nb_ameliores;
                    if (post_n_amel) {
                        post_n_amel.textContent = formatNumberReport(post_n_ame);
                    }
                    const post_n_amel3 = document.getElementById(
                        "post_n_amel3");
                    if (post_n_amel3) {
                        post_n_amel3.textContent = formatNumberReport(parseFloat(
                            post_n_ame2) - parseFloat(
                                post_n_ame));
                    }

                    const post_nt_latr = document.getElementById(
                        "post_nt_latr");
                    if (post_nt_latr) {
                        post_nt_latr.textContent = formatNumberReport(result.r4.min
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr2 = document.getElementById(
                        "post_nt_latr2");
                    if (post_nt_latr2) {
                        post_nt_latr2.textContent = formatNumberReport(result.r4.max
                            .nb_tot_latr_fam);
                    }
                    const post_nt_latr3 = document.getElementById(
                        "post_nt_latr3");
                    if (post_nt_latr3) {
                        post_nt_latr3.textContent = formatNumberReport(parseFloat(result.r4
                            .max
                            .nb_tot_latr_fam) - parseFloat(result.r4.min
                                .nb_tot_latr_fam));
                    }

                    const post_ct_latr = document.getElementById(
                        "post_ct_latr");
                    var post_ct = formatTaux(result.r4.min
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr) {
                        post_ct_latr.textContent = post_ct;
                    }

                    const post_ct_latr2 = document.getElementById(
                        "post_ct_latr2");
                    var post_ct2 = formatTaux(result.r4.max
                        .nb_tot_latr_fam * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_ct_latr2) {
                        post_ct_latr2.textContent =
                            post_ct2;
                    }

                    const post_ct_latr3 = document.getElementById(
                        "post_ct_latr3");
                    if (post_ct_latr3) {
                        post_ct_latr3.textContent = formatTaux(parseFloat(post_ct2) -
                            parseFloat(post_ct));
                    }

                    const post_securise = document.getElementById(
                        "post_securise");
                    var post_sec = formatTaux(result.r4.min
                        .securise * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_securise) {
                        post_securise.textContent = post_sec;
                    }
                    const post_securise2 = document.getElementById(
                        "post_securise2");
                    var post_sec2 = formatTaux(result.r4.max
                        .securise * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_securise2) {
                        post_securise2.textContent = post_sec2;
                    }

                    const post_securise3 = document.getElementById(
                        "post_securise3");
                    if (post_securise3) {
                        post_securise3.textContent = formatNumberReport(parseFloat(
                            post_sec2) - parseFloat(
                                post_sec));
                    }
                    const post_basique = document.getElementById(
                        "post_basique");
                    var post_bas = formatTaux(result.r4.min
                        .basique * 100 / result.total_menages);

                    if (post_basique) {
                        post_basique.textContent = post_bas;
                    }

                    const post_basique2 = document.getElementById(
                        "post_basique2");
                    var post_bas2 = formatTaux(result.r4.max
                        .basique * 100 / result.total_menages);
                    if (post_basique2) {
                        post_basique2.textContent = post_bas2;
                    }

                    const post_basique3 = document.getElementById(
                        "post_basique3");
                    if (post_basique3) {
                        post_basique3.textContent = formatTaux(parseFloat(post_bas2) -
                            parseFloat(
                                post_bas));
                    }


                    const post_non_amelior = document.getElementById(
                        "post_non_amelior");
                    var post_non_am = formatTaux(result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior) {
                        post_non_amelior.textContent = post_non_am;
                    }

                    const post_non_amelior2 = document.getElementById(
                        "post_non_amelior2");
                    var post_non_am2 = formatTaux(result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages);
                    if (post_non_amelior2) {
                        post_non_amelior2.textContent = post_non_am2;
                    }
                    const post_non_amelior3 = document.getElementById(
                        "post_non_amelior3");
                    if (post_non_amelior3) {
                        post_non_amelior3.textContent = formatTaux(parseFloat(
                            post_non_am2) - parseFloat(
                                post_non_am));
                    }
                    const post_dal = document.getElementById(
                        "post_dal");
                    var post_da1 = formatTaux(result.r4.min
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal) {
                        post_dal.textContent = post_da1;
                    }
                    const post_dal2 = document.getElementById(
                        "post_dal2");
                    var post_da2 = formatTaux(result.r4.max
                        .men_dal * 100 / result
                            .total_menages);
                    if (post_dal2) {
                        post_dal2.textContent = post_da2;
                    }

                    const post_dal3 = document.getElementById(
                        "post_dal3");
                    if (post_dal3) {
                        post_dal3.textContent = formatTaux(parseFloat(post_da2) -
                            parseFloat(
                                post_da1));
                    }

                    const post_limite = document.getElementById(
                        "post_limite");
                    var post_lim = formatTaux(100 - (result.r4.min
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.min
                                .basique * 100 / result
                                    .total_menages) - (result.r4.min
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite) {
                        post_limite.textContent = post_lim;
                    }
                    const post_limite2 = document.getElementById(
                        "post_limite2");

                    var post_lim2 = formatTaux(100 - (result.r4.max
                        .non_ameliores * 100 / result
                            .total_menages) - (result.r4.max
                                .basique * 100 / result
                                    .total_menages) - (result.r4.max
                                        .men_dal * 100 / result
                                            .total_menages));
                    if (post_limite2) {
                        post_limite2.textContent = post_lim2;
                    }

                    const post_limite3 = document.getElementById(
                        "post_limite3");
                    if (post_limite3) {
                        post_limite3.textContent = formatTaux(parseFloat(
                            post_lim2) -
                            parseFloat(post_lim));
                    }
                    const post_taux_hygiene_DLM = document.getElementById(
                        "post_taux_hygiene_DLM");
                    var post_hd = formatTaux(result.r4.min
                        .nbr_menages_dlm * 100 / result.r4.min
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM) {
                        post_taux_hygiene_DLM.textContent = post_hd;
                    }
                    const post_hygiene_DLM = document.getElementById(
                        "post_hygiene_DLM");

                    if (post_hygiene_DLM) {
                        post_hygiene_DLM.textContent = formatNumberReport(result.r4.min
                            .nbr_menages_dlm);
                    }

                    const post_taux_hygiene_DLM2 = document.getElementById(
                        "post_taux_hygiene_DLM2");
                    var post_hd2 = formatTaux(result.r4.max
                        .nbr_menages_dlm * 100 / result.r4.max
                            .nb_tot_latr_fam);
                    if (post_taux_hygiene_DLM2) {
                        post_taux_hygiene_DLM2.textContent = post_hd2;
                    }

                    const post_taux_hygiene_DLM3 = document.getElementById(
                        "post_taux_hygiene_DLM3");
                    if (post_taux_hygiene_DLM3) {
                        post_taux_hygiene_DLM3.textContent = formatTaux(parseFloat(
                            post_hd2) -
                            parseFloat(
                                post_hd));
                    }
                    const post_hygiene_DLM2 = document.getElementById(
                        "post_hygiene_DLM2");

                    if (post_hygiene_DLM2) {
                        post_hygiene_DLM2.textContent = formatNumberReport(result.r4.max
                            .nbr_menages_dlm);
                    }
                    const post_hygiene_DLM3 = document.getElementById(
                        "post_hygiene_DLM3");
                    if (post_hygiene_DLM3) {
                        post_hygiene_DLM3.textContent = formatNumberReport(parseFloat(result
                            .r4.max
                            .nbr_menages_dlm) - parseFloat(result.r4.min
                                .nbr_menages_dlm));
                    }

                    const post_hygiene_base = document.getElementById(
                        "post_hygiene_base");
                    var post_hb = formatTaux(result.r4.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_base) {
                        post_hygiene_base.textContent = post_hb;
                    }
                    const post_hygiene_base2 = document.getElementById(
                        "post_hygiene_base2");
                    var post_hb2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_base2) {

                        post_hygiene_base2.textContent = post_hb2;
                    }

                    const post_hygiene_base3 = document.getElementById(
                        "post_hygiene_base3");
                    if (post_hygiene_base3) {
                        post_hygiene_base3.textContent = formatTaux(parseFloat(post_hb2) -
                            parseFloat(
                                post_hb));
                    }


                    const post_hygiene_limite = document.getElementById(
                        "post_hygiene_limite");
                    var post_hl = formatTaux(result.r4.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_limite) {
                        post_hygiene_limite.textContent = post_hl;
                    }
                    const post_hygiene_limite2 = document.getElementById(
                        "post_hygiene_limite2");
                    var post_hl2 = formatTaux(result.r4.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_limite2) {
                        post_hygiene_limite2.textContent = post_hl2;
                    }

                    const post_hygiene_limite3 = document.getElementById(
                        "post_hygiene_limite3");
                    if (post_hygiene_limite3) {
                        post_hygiene_limite3.textContent = formatTaux(parseFloat(post_hl2) -
                            parseFloat(
                                post_hl));
                    }

                    const post_hygiene_aucun_service = document.getElementById(
                        "post_hygiene_aucun_service");
                    var post_ha = formatTaux(result.r4.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.min.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service) {
                        post_hygiene_aucun_service.textContent = post_ha;
                    }
                    const post_hygiene_aucun_service2 = document.getElementById(
                        "post_hygiene_aucun_service2");
                    var post_ha2 = formatTaux(result.r4.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r4.max.nb_menages_comptabilises);
                    if (post_hygiene_aucun_service2) {
                        post_hygiene_aucun_service2.textContent = post_ha2;
                    }


                    const post_hygiene_aucun_service3 = document.getElementById(
                        "post_hygiene_aucun_service3");
                    if (post_hygiene_aucun_service3) {
                        post_hygiene_aucun_service3.textContent = formatTaux(parseFloat(
                            post_ha2) -
                            parseFloat(post_ha));
                    }
                    ////////////////
                    const nt_latr = document.getElementById(
                        "nt_latr");
                    if (nt_latr) {
                        nt_latr.textContent = formatNumberReport(result.r2.min
                            .nb_tot_latr_fam);
                    }
                    const nt_latr2 = document.getElementById(
                        "nt_latr2");
                    if (nt_latr2) {
                        nt_latr2.textContent = formatNumberReport(result.r2.max
                            .nb_tot_latr_fam);
                    }
                    const nt_latr3 = document.getElementById(
                        "nt_latr3");
                    if (nt_latr3) {
                        nt_latr3.textContent = formatNumberReport(parseInt(result.r2.max
                            .nb_tot_latr_fam) - parseInt(result.r2.min
                                .nb_tot_latr_fam));
                    }

                    const ct_latr = document.getElementById(
                        "ct_latr");
                    var ct = formatTaux(result.r2.min
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr) {
                        ct_latr.textContent = ct;
                    }

                    const ct_latr2 = document.getElementById(
                        "ct_latr2");
                    var ct2 = formatTaux(result.r2.max
                        .nb_tot_latr_fam * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (ct_latr2) {
                        ct_latr2.textContent =
                            ct2;
                    }

                    const ct_latr3 = document.getElementById(
                        "ct_latr3");
                    if (ct_latr3) {
                        ct_latr3.textContent = formatTaux(parseFloat(ct2) - parseFloat(ct));
                    }

                    const securise = document.getElementById(
                        "securise");
                    var sec = (result.r2.min
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise) {
                        securise.textContent = formatTaux(sec);
                    }
                    const securise2 = document.getElementById(
                        "securise2");
                    var sec2 = (result.r2.max
                        .securise * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (securise2) {
                        securise2.textContent = formatTaux(sec2);
                    }

                    const securise3 = document.getElementById(
                        "securise3");
                    if (securise3) {
                        securise3.textContent = formatTaux(parseFloat(sec2) - parseFloat(
                            sec));
                    }



                    const basique = document.getElementById(
                        "basique");
                    var bas = (result.r2.min
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);

                    if (basique) {
                        basique.textContent = formatTaux(bas);
                    }

                    const basique2 = document.getElementById(
                        "basique2");
                    var bas2 = (result.r2.max
                        .basique * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (basique2) {
                        basique2.textContent = formatTaux(bas2);
                    }

                    const basique3 = document.getElementById(
                        "basique3");
                    if (basique3) {
                        basique3.textContent = formatTaux(parseFloat(bas2) - parseFloat(
                            bas));
                    }



                    const non_amelior = document.getElementById(
                        "non_amelior");
                    var non_am = (result.r2.min
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior) {
                        non_amelior.textContent = formatTaux(non_am);
                    }
                    const non_amelior2 = document.getElementById(
                        "non_amelior2");
                    var non_am2 = (result.r2.max
                        .non_ameliores * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (non_amelior2) {
                        non_amelior2.textContent = formatTaux(non_am2);
                    }
                    const non_amelior3 = document.getElementById(
                        "non_amelior3");
                    if (non_amelior3) {
                        non_amelior3.textContent = formatTaux(parseFloat(non_am2) -
                            parseFloat(
                                non_am));
                    }
                    const dal = document.getElementById(
                        "dal");
                    var da1 = result.r2.min
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises;
                    if (dal) {
                        dal.textContent = formatTaux(da1);
                    }
                    const dal2 = document.getElementById(
                        "dal2");
                    var da2 = (result.r2.max
                        .men_dal * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (dal2) {
                        dal2.textContent = formatTaux(da2);
                    }
                    
                    

                    const dal3 = document.getElementById(
                        "dal3");
                    if (dal3) {
                        dal3.textContent = formatTaux(parseFloat(da2) - parseFloat(da1));
                    }
                    


                    const n_dal2 = document.getElementById(
                        "n_dal2");
                    var n_da2 = result.r2.max
                        .men_dal;
                    if (n_dal2) {
                        n_dal2.textContent =formatNumberReport(n_da2);
                    }
                    const n_dal = document.getElementById(
                        "n_dal");
                    var n_da = result.r2.min
                        .men_dal;
                    if (n_dal) {
                        n_dal.textContent = formatNumberReport(n_da);
                    }
                    const n_dal3 = document.getElementById(
                        "n_dal3");
                    if (n_dal3) {
                        n_dal3.textContent = formatNumberReport(parseFloat(n_da2) -
                            parseFloat(
                                n_da));
                    }

                    const n_amel2 = document.getElementById(
                        "n_amel2");
                    var n_ame2 = result.r2.max
                        .nb_ameliores;
                    if (n_amel2) {
                        n_amel2.textContent = formatNumberReport(n_ame2);
                    }
                    const n_amel = document.getElementById(
                        "n_amel");
                    var n_ame = result.r2.min
                        .nb_ameliores;

                    if (n_amel) {
                        n_amel.textContent = formatNumberReport(n_ame);
                    }
                    const n_amel3 = document.getElementById(
                        "n_amel3");
                    if (n_amel3) {
                        n_amel3.textContent = formatNumberReport(parseFloat(n_ame2) -
                            parseFloat(n_ame));
                    }
                    
                    
                    const limite = document.getElementById(
                        "limite");
                    var lim = (100 - bas - sec - non_am - da1);
                    if (limite) {
                        limite.textContent = formatTaux(lim);
                    }
                    const limite2 = document.getElementById(
                        "limite2");
                    var lim2 = (100 - bas2 - sec2 - non_am2 - da2);
                    if (limite2) {
                        limite2.textContent = formatTaux(lim2);
                    }

                    const limite3 = document.getElementById(
                        "limite3");
                    if (limite3) {
                        limite3.textContent = formatTaux(parseFloat(lim2) - parseFloat(
                            lim));
                    }
                    const taux_hygiene_DLM = document.getElementById(
                        "taux_hygiene_DLM");
                    var hd = formatTaux(result.r2.min
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM) {
                        taux_hygiene_DLM.textContent = hd;
                    }
                    const hygiene_DLM = document.getElementById(
                        "hygiene_DLM");

                    if (hygiene_DLM) {
                        hygiene_DLM.textContent = formatNumberReport(result.r2.min
                            .nbr_menages_dlm);
                    }

                    const taux_hygiene_DLM2 = document.getElementById(
                        "taux_hygiene_DLM2");
                    var hd2 = formatTaux(result.r2.max
                        .nbr_menages_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (taux_hygiene_DLM2) {
                        taux_hygiene_DLM2.textContent = hd2;
                    }

                    const taux_hygiene_DLM3 = document.getElementById(
                        "taux_hygiene_DLM3");
                    if (taux_hygiene_DLM3) {
                        taux_hygiene_DLM3.textContent = formatTaux(parseFloat(hd2) -
                            parseFloat(
                                hd));
                    }
                    const hygiene_DLM2 = document.getElementById(
                        "hygiene_DLM2");

                    if (hygiene_DLM2) {
                        hygiene_DLM2.textContent = formatNumberReport(result.r2.max
                            .nbr_menages_dlm);
                    }
                    const hygiene_DLM3 = document.getElementById(
                        "hygiene_DLM3");
                    if (hygiene_DLM3) {
                        hygiene_DLM3.textContent = formatNumberReport(parseFloat(result.r2
                            .max
                            .nbr_menages_dlm) - parseFloat(result.r2.min
                                .nbr_menages_dlm));
                    }

                    const hygiene_base = document.getElementById(
                        "hygiene_base");
                    var hb = formatTaux(result.r2.min
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_base) {
                        hygiene_base.textContent = hb;
                    }
                    const hygiene_base2 = document.getElementById(
                        "hygiene_base2");
                    var hb2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_complet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_base2) {

                        hygiene_base2.textContent = hb2;
                    }

                    const hygiene_base3 = document.getElementById(
                        "hygiene_base3");
                    if (hygiene_base3) {
                        hygiene_base3.textContent = formatNumberReport(parseFloat(hb2) -
                            parseFloat(hb));
                    }


                    const hygiene_limite = document.getElementById(
                        "hygiene_limite");
                    var hl = formatTaux(result.r2.min
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_limite) {
                        hygiene_limite.textContent = hl;
                    }
                    const hygiene_limite2 = document.getElementById(
                        "hygiene_limite2");
                    var hl2 = formatTaux(result.r2.max
                        .nbr_menages_dlm_incomplet * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_limite2) {
                        hygiene_limite2.textContent = hl2;
                    }

                    const hygiene_limite3 = document.getElementById(
                        "hygiene_limite3");
                    if (hygiene_limite3) {
                        hygiene_limite3.textContent = formatTaux(parseFloat(hl2) -
                            parseFloat(
                                hl));
                    }

                    const hygiene_aucun_service = document.getElementById(
                        "hygiene_aucun_service");
                    var ha = formatTaux(result.r2.min
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.min.nb_menages_comptabilises);
                    if (hygiene_aucun_service) {
                        hygiene_aucun_service.textContent = ha;
                    }
                    const hygiene_aucun_service2 = document.getElementById(
                        "hygiene_aucun_service2");
                    var ha2 = formatTaux(result.r2.max
                        .nbr_menages_sans_dlm * 100 / result
                            .r2.max.nb_menages_comptabilises);
                    if (hygiene_aucun_service2) {
                        hygiene_aucun_service2.textContent = ha2;
                    }


                    const hygiene_aucun_service3 = document.getElementById(
                        "hygiene_aucun_service3");
                    if (hygiene_aucun_service3) {
                        hygiene_aucun_service3.textContent = formatTaux(parseFloat(ha2) -
                            parseFloat(ha));
                    }




                    const nb_loc_benefic_appui = document.getElementById(
                        "nb_loc_benefic_appui");
                    if (nb_loc_benefic_appui) {
                        nb_loc_benefic_appui.textContent = formatNumberReport(result
                            .nb_loc_benefic_appui);
                    }

                    const pop2 = document.getElementById(
                        "pop2");
                    if (pop2) {
                        pop2.textContent = formatNumberReport(result.nb_tot_pop);
                    }
                    const men2 = document.getElementById(
                        "men2");
                    if (men2) {
                        men2.textContent = formatNumberReport(result
                            .nb_menages_comptabilises);
                    }


                    const construite2 = document.getElementById(
                        "construite2");
                    if (construite2) {
                        construite2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise);
                    }
                    const auto_const2 = document.getElementById(
                        "auto_const2");
                    if (auto_const2) {
                        auto_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_autoconst);
                    }

                    const re_macon2 = document.getElementById(
                        "re_macon2");
                    if (re_macon2) {
                        re_macon2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_realise_macon);
                    }
                    const en_const2 = document.getElementById(
                        "en_const2");
                    if (en_const2) {
                        en_const2.textContent = formatNumberReport(result.r3
                            .nbr_latrine_en_constr);
                    }
                    const phm2 = document.getElementById(
                        "phm2");
                    if (phm2) {
                        phm2.textContent = formatNumberReport(result.r3
                            .nbr_menages_ltr_end_phm);
                    }


                    $('#projets').html('');
                    $.each(result.projets, function (key,
                        value) {
                        $('#projets').append(`
                        
                                    <tr>
                                        
                                        <td>` + value.infra_publ + `</td>
                                   
                                        
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.effectif) + `</td>
                                        <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                    
                                        
                                        <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                        <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    </tr>`);



                    });

                }

            });
        }


    });
</script>
<script>
// ============================================
// FONCTION D'IMPRESSION AMÉLIORÉE - VERSION CORRIGÉE
// ============================================

function printReport() {
    // Créer un clone pour l'impression
    const content = document.getElementById('content');
    const clone = content.cloneNode(true);
    
    // Préparer le contenu pour l'impression
    prepareForPrint(clone);
    
    // Créer une fenêtre d'impression
    const printWindow = window.open('', '_blank');
    const reportTitle = document.getElementById('rapport').textContent;
    const date = document.getElementById('date')?.textContent || new Date().toLocaleDateString('fr-FR');
    const currentFilter = getCurrentFilter();
    
    // Créer le HTML pour la fenêtre d'impression
const printWindowHTML = `
<!DOCTYPE html>
<html>
<head>
    <title>${reportTitle}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ========== STYLES DE BASE ========== */
        body {
            font-family: "Arial", sans-serif;
            font-size: 18px;
            line-height: 1.4;
            color: #000;
        }
        
        .text-container {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            font-size: 18px;
            background: #f9f9f9;
        }
        
        .table-element {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table-element th {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 10px;
        }
        
        .table-element td {
            padding: 8px;
        }
        
        /* STYLES DES TITRES - BASE */
        h4 {
            text-align: center;
            margin: 25px 0;
            font-size: 24px !important;  /* Ajout !important */
            color: #333;
            font-weight: bold;
        }
        
        h5 {
            background-color: #00A95C;
            color: white;
            padding: 12px;
            margin: 30px 0 20px 0;
            text-align: center;
            font-size: 22px !important;  /* Ajout !important */
            font-weight: bold;
        }
        
        h6 {
            background-color: #FFD700;
            color: #000;
            padding: 10px;
            margin: 25px 0 15px 0;
            text-align: center;
            font-size: 20px !important;  /* Ajout !important */
            font-weight: bold;
        }
        
        /* ========== STYLES ÉCRAN ========== */
        @media screen {
            body {
                padding: 20px;
                margin: 0;
                background: #f5f5f5;
            }
            
            .content-wrapper {
                max-width: 794px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 40px;
                padding-bottom: 20px;
                border-bottom: 3px solid #00A95C;
            }
            
            .print-footer {
                text-align: center;
                margin-top: 40px;
                padding-top: 20px;
                border-top: 2px solid #ccc;
                font-size: 11px;
                color: #666;
            }
            
            .action-buttons {
                text-align: center;
                margin: 20px 0 40px 0;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 1px solid #ddd;
            }
            
            .action-buttons .btn {
                margin: 0 10px;
                padding: 10px 20px;
                font-size: 14px;
                border-radius: 4px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-weight: bold;
                transition: all 0.3s ease;
            }
            
            .action-buttons .btn i {
                margin-right: 8px;
            }
            
            .btn-print {
                background-color: #28a745;
                color: white;
                border: 1px solid #28a745;
            }
            
            .btn-print:hover {
                background-color: #218838;
                border-color: #1e7e34;
            }
            
            .btn-pdf {
                background-color: #dc3545;
                color: white;
                border: 1px solid #dc3545;
            }
            
            .btn-pdf:hover {
                background-color: #c82333;
                border-color: #bd2130;
            }
            
            .btn-close {
                background-color: #6c757d;
                color: white;
                border: 1px solid #6c757d;
            }
            
            .btn-close:hover {
                background-color: #5a6268;
                border-color: #545b62;
            }
            
            .print-notice {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
                font-size: 12px;
            }
            
            .filter-info {
                background: #e9ecef;
                padding: 10px 15px;
                margin: 15px 0;
                border-radius: 5px;
                font-size: 13px;
                border-left: 4px solid #00A95C;
            }
        }
        
        /* ========== STYLES IMPRESSION ========== */
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body {
                font-size: 18px !important;
                line-height: 1.4 !important;
                font-family: "Arial", sans-serif !important;
                color: #000 !important;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .content-wrapper {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                background: white !important;
            }
            
            .print-header {
                text-align: center !important;
                margin-bottom: 30px !important;
                padding-bottom: 15px !important;
                border-bottom: 2px solid #00A95C !important;
                page-break-after: avoid !important;
            }
            
            .print-footer {
                text-align: center !important;
                margin-top: 30px !important;
                padding-top: 15px !important;
                border-top: 1px solid #ccc !important;
                font-size: 12pt !important;
                color: #666 !important;
            }
            
            .text-container {
                border: 1px solid #000 !important;
                padding: 12px !important;
                margin: 15px 0 !important;
                font-size: 18px !important;
                page-break-inside: avoid !important;
                background: #f9f9f9 !important;
            }
            
            .table-element {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 18px !important;
                page-break-inside: avoid !important;
            }
            
            .table-element, .table-element th, .table-element td {
                border: 1px solid #000 !important;
            }
            
            .table-element th {
                background-color: #f8f9fa !important;
                font-weight: bold !important;
                padding: 8px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .table-element td {
                padding: 6px !important;
            }
            
            /* TAILLES AUGMENTÉES POUR L'IMPRESSION */
            h4 {
                text-align: center !important;
                font-size: 26px !important;  /* Augmenté de 22px à 26px */
                margin: 25px 0 15px 0 !important;
                color: #333 !important;
                font-weight: bold !important;
                page-break-after: avoid !important;
            }
            
            h5 {
                background-color: #00A95C !important;
                color: white !important;
                padding: 12px !important;
                margin: 30px 0 18px 0 !important;
                text-align: center !important;
                font-size: 24px !important;  /* Augmenté de 22px à 24px */
                page-break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
            }
            
            h6 {
                background-color: #FFD700 !important;
                color: #000 !important;
                padding: 10px !important;
                margin: 25px 0 15px 0 !important;
                text-align: center !important;
                font-size: 22px !important;  /* Augmenté de 20px à 22px */
                page-break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
            }
            
            /* Masquer les éléments */
            .leaflet-container,
            #map, #map2, #map3, #map4, #map5, #map6,
            canvas {
                display: none !important;
            }
            
            h6:has(img) {
                display: none !important;
            }
            
            .action-buttons,
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always !important;
            }
            
            .no-break {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <div class="action-buttons no-print">
            <button class="btn btn-pdf" id="print-window-pdf">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </button>
            <button class="btn btn-print" id="print-window-print">
                <i class="fas fa-print"></i> Imprimer
            </button>
            <button class="btn btn-close" id="print-window-close">
                <i class="fas fa-times"></i> Fermer
            </button>
        </div>
        
        ${currentFilter ? '<div class="filter-info no-print"><strong>Filtre appliqué :</strong> ' + currentFilter + '</div>' : ''}
        
        <div class="print-header">
            <h1 style="color: #00A95C; margin-bottom: 10px; font-size: 28px;">${reportTitle}</h1>
            <p style="font-size: 16px; color: #666;">Rapport d'assainissement - Direction de l'Assainissement</p>
            <p style="font-size: 14px; color: #888;">Date de génération : ${date}</p>
        </div>
        
        <div class="print-notice no-print">
            <strong>Note :</strong> Les cartes géographiques et graphiques interactifs ne sont pas inclus dans cette version imprimée.
            Pour une vue complète avec les visualisations, utilisez la fonction d'export PDF.
        </div>
        
        <div id="print-content">
            ${clone.innerHTML}
        </div>
        
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"><\/script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>
    <script>
        // Fonction pour obtenir le filtre actuel
        function getCurrentFilter() {
            const wilaya = $('#wilaya option:selected').text();
            const moughata = $('#moughata option:selected').text();
            const commune = $('#commune option:selected').text();
            const type = $('#type option:selected').text();
            
            let filterText = '';
            
            if (wilaya && wilaya !== 'TOUTES LES WILAYAS') {
                filterText += 'Wilaya: ' + wilaya;
            }
            if (moughata && moughata !== '-- Select Moughata --') {
                filterText += (filterText ? ', ' : '') + 'Moughata: ' + moughata;
            }
            if (commune && commune !== '-- Select Commune --') {
                filterText += (filterText ? ', ' : '') + 'Commune: ' + commune;
            }
            if (type && type !== 'Assainissement familial et Assainissement public') {
                filterText += (filterText ? ', ' : '') + 'Type: ' + type;
            }
            
            return filterText || 'Aucun filtre';
        }
        
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
        
        async function exportToPDFFromPrintWindow() {
            const btn = document.getElementById('print-window-pdf');
            const originalText = btn.innerHTML;
            
            try {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération PDF...';
                btn.disabled = true;
                
                if (typeof window.jspdf === 'undefined' || typeof html2canvas === 'undefined') {
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
                    await loadScript('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
                
                const { jsPDF } = window.jspdf;
                const content = document.querySelector('.content-wrapper');
                const reportTitle = document.querySelector('.print-header h1').textContent;
                const date = document.querySelector('.print-header p:last-child').textContent;
                const currentFilter = document.querySelector('.filter-info') ? 
                    document.querySelector('.filter-info').textContent.replace('Filtre appliqué :', '').trim() : 
                    'Aucun filtre';
                
                const tempDiv = document.createElement('div');
                tempDiv.style.position = 'absolute';
                tempDiv.style.left = '-9999px';
                tempDiv.style.top = '0';
                tempDiv.style.width = '794px';
                tempDiv.style.backgroundColor = 'white';
                tempDiv.style.padding = '20px';
                
                const pdfContent = content.cloneNode(true);
                
                const elementsToRemove = pdfContent.querySelectorAll(
                    '.action-buttons, .print-notice, .filter-info, .no-print'
                );
                elementsToRemove.forEach(el => el.remove());
                
                const mapsAndCharts = pdfContent.querySelectorAll(
                    '.leaflet-container, canvas, #map, #map2, #map3, #map4, #map5, #map6'
                );
                mapsAndCharts.forEach(el => el.remove());
                
                const mapLegends = pdfContent.querySelectorAll('h6');
                mapLegends.forEach(legend => {
                    if (legend.innerHTML.includes('img')) {
                        legend.remove();
                    }
                });
                
                // FORCER LES TAILLES DE POLICE POUR LE PDF
                const headings = pdfContent.querySelectorAll('h4, h5, h6');
                headings.forEach(heading => {
                    if (heading.tagName === 'H4') {
                        heading.style.fontSize = '26px';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H5') {
                        heading.style.fontSize = '24px';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H6') {
                        heading.style.fontSize = '22px';
                        heading.style.fontWeight = 'bold';
                    }
                });
                
                const header = document.createElement('div');
                header.style.textAlign = 'center';
                header.style.marginBottom = '30px';
                header.style.paddingBottom = '15px';
                header.style.borderBottom = '2px solid #00A95C';
                header.innerHTML = '<h2 style="color: #00A95C; margin-bottom: 10px; font-size: 28px;">' + reportTitle + '</h2>' +
                                   '<p style="font-size: 16px; color: #666; margin: 5px 0;">Rapport d\\'assainissement - Direction de l\\'Assainissement</p>' +
                                   '<p style="font-size: 14px; color: #888; margin: 5px 0;">' + date + '</p>' +
                                   (currentFilter !== 'Aucun filtre' ? '<p style="font-size: 14px; color: #555; margin: 5px 0;"><strong>Filtre :</strong> ' + currentFilter + '</p>' : '');
                
                pdfContent.insertBefore(header, pdfContent.firstChild);
                
                tempDiv.appendChild(pdfContent);
                document.body.appendChild(tempDiv);
                
                await new Promise(resolve => setTimeout(resolve, 500));
                
                const canvas = await html2canvas(tempDiv, {
                    scale: 1.5,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    allowTaint: true,
                    removeContainer: true
                });
                
                document.body.removeChild(tempDiv);
                
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                const imgWidth = pageWidth - 20;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                let position = 10;
                let remainingHeight = imgHeight;
                
                while (remainingHeight > 0) {
                    const heightOnPage = Math.min(remainingHeight, pageHeight - 20);
                    pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight, '', 'FAST');
                    
                    remainingHeight -= (pageHeight - 20);
                    position -= (pageHeight - 20);
                    
                    if (remainingHeight > 0) {
                        pdf.addPage();
                    }
                }
                
                const totalPages = pdf.internal.getNumberOfPages();
                for (let i = 1; i <= totalPages; i++) {
                    pdf.setPage(i);
                    pdf.setFontSize(8);
                    pdf.setTextColor(128, 128, 128);
                    pdf.text(
                        'Page ' + i + ' sur ' + totalPages,
                        pageWidth / 2,
                        pageHeight - 10,
                        { align: 'center' }
                    );
                }
                
                const fileName = 'rapport_assainissement_' + 
                                new Date().toISOString().slice(0, 10).replace(/-/g, '') + 
                                '_' + 
                                Math.floor(Math.random() * 1000) + 
                                '.pdf';
                
                pdf.save(fileName);
                
            } catch (error) {
                console.error('Erreur lors de la génération du PDF:', error);
                alert('Erreur lors de la génération du PDF. Veuillez réessayer ou utiliser l\\'impression standard.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
        
        function printFromPrintWindow() {
            window.print();
        }
        
        function closePrintWindow() {
            window.close();
        }
        
        function generateSimplePDF() {
            try {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();
                
                const reportTitle = document.querySelector('.print-header h1').textContent;
                const date = document.querySelector('.print-header p:last-child').textContent;
                const currentFilter = document.querySelector('.filter-info') ? 
                    document.querySelector('.filter-info').textContent.replace('Filtre appliqué :', '').trim() : 
                    'Aucun filtre';
                
                pdf.setFontSize(16);
                pdf.setTextColor(0, 169, 92);
                pdf.text(reportTitle, pageWidth / 2, 20, { align: 'center' });
                
                pdf.setFontSize(10);
                pdf.setTextColor(100, 100, 100);
                pdf.text('Rapport d\\'assainissement - Direction de l\\'Assainissement', pageWidth / 2, 30, { align: 'center' });
                pdf.text(date, pageWidth / 2, 35, { align: 'center' });
                
                if (currentFilter !== 'Aucun filtre') {
                    pdf.text('Filtre: ' + currentFilter, pageWidth / 2, 40, { align: 'center' });
                }
                
                pdf.setFontSize(12);
                pdf.setTextColor(0, 0, 0);
                pdf.text('Pour le rapport complet avec graphiques et cartes,', 20, 60);
                pdf.text('veuillez utiliser la fonction d\\'impression du navigateur.', 20, 70);
                
                pdf.setFontSize(8);
                pdf.setTextColor(128, 128, 128);
                pdf.text('Document généré par le Système PIAM', pageWidth / 2, pageHeight - 10, { align: 'center' });
                pdf.text('Direction de l\\'Assainissement - Ministère de l\\'Hydraulique et de l\\'Assainissement', pageWidth / 2, pageHeight - 5, { align: 'center' });
                
                const fileName = 'rapport_assainissement_simple_' + new Date().toISOString().slice(0, 10) + '.pdf';
                pdf.save(fileName);
            } catch (error) {
                alert('Impossible de générer le PDF simple.');
            }
        }
        
        window.onload = function() {
            const sections = document.querySelectorAll('h5');
            sections.forEach((section, index) => {
                if (index > 0) {
                    const pageBreak = document.createElement('div');
                    pageBreak.className = 'page-break';
                    pageBreak.style.height = '1px';
                    section.parentNode.insertBefore(pageBreak, section);
                }
            });
            
            // Assurer que les titres sont bien visibles
            document.querySelectorAll('h4, h5, h6').forEach(el => {
                el.style.display = 'block';
                el.style.visibility = 'visible';
            });
            
            document.getElementById('print-window-pdf').addEventListener('click', function() {
                exportToPDFFromPrintWindow().catch(function(error) {
                    console.error('Erreur PDF:', error);
                    generateSimplePDF();
                });
            });
            
            document.getElementById('print-window-print').addEventListener('click', printFromPrintWindow);
            document.getElementById('print-window-close').addEventListener('click', closePrintWindow);
        };
    <\/script>
</body>
</html>`;
    
    printWindow.document.write(printWindowHTML);
    printWindow.document.close();
}
// Fonction pour obtenir les filtres actuels
function getCurrentFilter() {
    const wilaya = $('#wilaya option:selected').text();
    const moughata = $('#moughata option:selected').text();
    const commune = $('#commune option:selected').text();
    const type = $('#type option:selected').text();
    
    let filterText = '';
    
    if (wilaya && wilaya !== 'TOUTES LES WILAYAS') {
        filterText += 'Wilaya: ' + wilaya;
    }
    if (moughata && moughata !== '-- Select Moughata --') {
        filterText += (filterText ? ', ' : '') + 'Moughata: ' + moughata;
    }
    if (commune && commune !== '-- Select Commune --') {
        filterText += (filterText ? ', ' : '') + 'Commune: ' + commune;
    }
    if (type && type !== 'Assainissement familial et Assainissement public') {
        filterText += (filterText ? ', ' : '') + 'Type: ' + type;
    }
    
    return filterText || 'Aucun filtre';
}

// Fonction pour préparer le contenu pour l'impression
function prepareForPrint(element) {
    // Supprimer les cartes et graphiques
    const maps = element.querySelectorAll('.leaflet-container, canvas');
    maps.forEach(item => item.remove());
    
    // Supprimer les titres de légende des cartes
    const mapLegends = element.querySelectorAll('h6');
    mapLegends.forEach(legend => {
        if (legend.innerHTML.includes('img')) {
            legend.remove();
        }
    });
    
    // Supprimer les boutons d'impression
    const printButtons = element.querySelectorAll('.no-print, #generate-pdf, #print-report');
    printButtons.forEach(button => button.remove());
    
    // Masquer les sections vides
    hideEmptySections(element);
    
    // Optimiser l'apparence
    optimizeTables(element);
    optimizeTitles(element);
    optimizeTextContainers(element);
}



// ============================================
// FONCTIONS UTILITAIRES
// ============================================

// Fonction pour masquer les sections vides
function hideEmptySections(element) {
    const sections = element.querySelectorAll('.row');
    sections.forEach(section => {
        const tables = section.querySelectorAll('table');
        const texts = section.querySelectorAll('.text-container');
        
        let hasContent = false;
        
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            if (rows.length > 0) {
                hasContent = true;
            }
        });
        
        texts.forEach(text => {
            if (text.textContent.trim() !== '') {
                hasContent = true;
            }
        });
        
        if (!hasContent) {
            section.style.display = 'none';
        }
    });
}

// Fonction pour optimiser les tables
function optimizeTables(element) {
    const tables = element.querySelectorAll('.table-element');
    tables.forEach(table => {
        table.classList.add('no-break');
    });
}

// Fonction pour optimiser les titres
function optimizeTitles(element) {
    const titles = element.querySelectorAll('h5, h6');
    titles.forEach(title => {
        title.classList.add('no-break');
    });
}

// Fonction pour optimiser les conteneurs de texte
function optimizeTextContainers(element) {
    const containers = element.querySelectorAll('.text-container');
    containers.forEach(container => {
        container.classList.add('no-break');
    });
}

// ============================================
// INITIALISATION
// ============================================

$(document).ready(function() {
    // Initialisation Select2
    $('.wilaya').select2();
    $('.moughata').select2();
    $('.commune').select2();
    $('.type').select2();
    
    // Gestion du changement de type
    $('#type').on('change', function() {
        const type = $(this).val();
        
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
    
// Export PDF amélioré pour la page principale
$('#generate-pdf').off('click').on('click', async function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const btn = $(this);
    const originalText = btn.html();
    
    try {
        // Afficher l'indicateur de chargement
        btn.html('<i class="fas fa-spinner fa-spin"></i> Génération PDF...');
        btn.prop('disabled', true);
        
        const { jsPDF } = window.jspdf;
        const content = document.getElementById('content');
        const reportTitle = document.getElementById('rapport').textContent;
        const date = document.getElementById('date')?.textContent || new Date().toLocaleDateString('fr-FR');
        const currentFilter = getCurrentFilter();
        
        // Créer un div temporaire pour le contenu PDF
        const tempDiv = document.createElement('div');
        tempDiv.style.position = 'absolute';
        tempDiv.style.left = '-9999px';
        tempDiv.style.top = '0';
        tempDiv.style.width = '794px'; // Largeur A4
        tempDiv.style.backgroundColor = 'white';
        tempDiv.style.padding = '20px';
        
        // Cloner le contenu
        const pdfContent = content.cloneNode(true);
        
        // Supprimer les éléments non nécessaires
        const elementsToRemove = pdfContent.querySelectorAll(
            '.leaflet-container, canvas, #map, #map2, #map3, #map4, #map5, #map6, .no-print'
        );
        elementsToRemove.forEach(el => el.remove());
        
        // Supprimer les titres de légende des cartes
        const mapLegends = pdfContent.querySelectorAll('h6');
        mapLegends.forEach(legend => {
            if (legend.innerHTML.includes('img')) {
                legend.remove();
            }
        });
        
        // Optimiser l'apparence pour le PDF
        optimizeForPDF(pdfContent);
        
        // Ajouter un en-tête
        const header = document.createElement('div');
        header.style.textAlign = 'center';
        header.style.marginBottom = '30px';
        header.style.paddingBottom = '15px';
        header.style.borderBottom = '2px solid #00A95C';
        header.innerHTML = `
            <h2 style="color: #00A95C; margin-bottom: 10px;">\${reportTitle}</h2>
            <p style="font-size: 14px; color: #666; margin: 5px 0;">Rapport d'assainissement - Direction de l'Assainissement</p>
            <p style="font-size: 12px; color: #888; margin: 5px 0;">\${date}</p>
            \${currentFilter !== 'Aucun filtre' ? \`<p style="font-size: 12px; color: #555; margin: 5px 0;"><strong>Filtre :</strong> \${currentFilter}</p>\` : ''}
        `;
        
        pdfContent.insertBefore(header, pdfContent.firstChild);
        
        // Ajouter un pied de page
        const footer = document.createElement('div');
        footer.style.textAlign = 'center';
        footer.style.marginTop = '40px';
        footer.style.paddingTop = '20px';
        footer.style.borderTop = '1px solid #ccc';
        footer.style.fontSize = '11px';
        footer.style.color = '#666';
        footer.innerHTML = `
            <p>Document généré par le Système PIAM (Plateforme d'Information pour l'Assainissement en Mauritanie)</p>
            <p>Direction de l'Assainissement - Ministère de l'Hydraulique et de l'Assainissement</p>
            <p>République Islamique de Mauritanie - \${new Date().getFullYear()}</p>
        `;
        
        pdfContent.appendChild(footer);
        
        // Ajouter le contenu au div temporaire
        tempDiv.appendChild(pdfContent);
        document.body.appendChild(tempDiv);
        
        // Attendre un peu pour le rendu
        await new Promise(resolve => setTimeout(resolve, 500));
        
        // Convertir en canvas
        const canvas = await html2canvas(tempDiv, {
            scale: 1.5,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
            allowTaint: true,
            removeContainer: true
        });
        
        // Nettoyer
        document.body.removeChild(tempDiv);
        
        const imgData = canvas.toDataURL('image/png');
        
        // Créer le PDF
        const pdf = new jsPDF('p', 'mm', 'a4');
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();
        
        const imgWidth = pageWidth - 20;
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        let position = 10;
        let remainingHeight = imgHeight;
        
        // Ajouter l'image au PDF avec pagination
        while (remainingHeight > 0) {
            const heightOnPage = Math.min(remainingHeight, pageHeight - 20);
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight, '', 'FAST');
            
            remainingHeight -= (pageHeight - 20);
            position -= (pageHeight - 20);
            
            if (remainingHeight > 0) {
                pdf.addPage();
            }
        }
        
        // Ajouter la pagination
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(8);
            pdf.setTextColor(128, 128, 128);
            pdf.text(
                'Page ' + i + ' sur ' + totalPages,
                pageWidth / 2,
                pageHeight - 10,
                { align: 'center' }
            );
        }
        
        // Télécharger le PDF
        const fileName = 'rapport_assainissement_' + 
                        new Date().toISOString().slice(0, 10).replace(/-/g, '') + 
                        '.pdf';
        
        pdf.save(fileName);
        
    } catch (error) {
        console.error('Erreur lors de la génération du PDF:', error);
        alert('Erreur lors de la génération du PDF: ' + error.message);
        
        // En cas d'échec, proposer l'impression
        if (confirm('La génération du PDF a échoué. Souhaitez-vous imprimer le document à la place ?')) {
            printReport();
        }
    } finally {
        // Restaurer le bouton
        btn.html(originalText);
        btn.prop('disabled', false);
    }
});

// Fonction pour optimiser le contenu pour le PDF
function optimizeForPDF(element) {
    // Optimiser les tables
    const tables = element.querySelectorAll('.table-element');
    tables.forEach(table => {
        table.style.fontSize = '9pt';
        table.style.margin = '10px 0';
        
        // Ajuster les cellules
        const cells = table.querySelectorAll('td, th');
        cells.forEach(cell => {
            cell.style.padding = '4px 6px';
        });
    });
    
    // Optimiser les textes
    const textContainers = element.querySelectorAll('.text-container');
    textContainers.forEach(container => {
        container.style.fontSize = '10pt';
        container.style.padding = '10px';
        container.style.margin = '15px 0';
    });
    
    // Optimiser les titres
    const h5 = element.querySelectorAll('h5');
    h5.forEach(title => {
        title.style.fontSize = '12pt';
        title.style.padding = '8px';
        title.style.margin = '20px 0 10px 0';
    });
    
    const h6 = element.querySelectorAll('h6');
    h6.forEach(title => {
        title.style.fontSize = '11pt';
        title.style.padding = '6px';
        title.style.margin = '15px 0 8px 0';
    });
    
    // Masquer les éléments vides
    const rows = element.querySelectorAll('.row');
    rows.forEach(row => {
        const hasContent = row.textContent.trim() !== '' && 
                          row.querySelectorAll('table, .text-container, h5, h6').length > 0;
        if (!hasContent) {
            row.style.display = 'none';
        }
    });
}

    
    // Impression
    $('#print-report').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        printReport();
    });
    
    // Gestion du changement de wilaya
    $('#wilaya').on('change', function() {
        if ($(this).val() === 'all') {
            window.location.href = "{{ route('admin.reporting.index') }}";
        }
    });
});

// Redéfinir les fonctions de formatage si nécessaire
window.formatTaux = function(number, decimals = 1) {
    number = parseFloat(number);
    decimals = parseInt(decimals);
    
    if (isNaN(number) || number === null || !isFinite(number)) {
        number = 0;
    }
    
    const formatter = new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    
    const formattedNumber = formatter.format(number);
    return formattedNumber + '%';
};

window.formatNumberReport = function(number, decimals = 0) {
    number = parseFloat(number);
    decimals = parseInt(decimals);
    
    if (isNaN(number) || number === null || !isFinite(number)) {
        number = 0;
    }
    
    const formatter = new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
    
    return formatter.format(number);
};

</script>
<script>
window.addEventListener("scroll", function() {
    const header = document.getElementById("rapport");
    const offset = header.offsetTop;

    if (window.scrollY > offset) {
        header.classList.add("sticky");
    } else {
        header.classList.remove("sticky");
    }
});
</script>
<script>
        var latLngGeom = null;
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

        $.ajax({
            'async': false,
            'global': false,
            'url': "/assets/admin/geojson/mrt.json",
            'dataType': "json",
            'success': function (data) {
                latLngGeom = data;

            }
        });


        var map2 = L.map('map2')
            .setView([19.353, -11.448], 6),
            osmUrl =
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
                'Map data &copy; 2012 OpenStreetMap contributors';
       

        var osm2 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map2);
        
        $(document).ready(function () {

            L.control.bigImage({
                position: 'topright'
            }).addTo(map2);
            

            

            $('#projet').on('change', function () {

                var idProjet = this.value;
                
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        projet_id: idProjet,
                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (res) {
                        //
                        map2.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        
                        //map2.setView([res.view.lat, res.view.lon], 8);
                        
                        $.each(res.declenches_non_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                                .addTo(map2);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });


                        $.each(res.declenches_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                                .addTo(map2);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });


                    }

                });
                

            });
            $('#operateur').on('change', function () {

                var idOperateur = this.value;
                var idProjet = $("#projet").val();


                
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        projet_id: idProjet,
                        operateur_id: idOperateur,
                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (res) {
                        //
                                               map2.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map2.removeLayer(layer);
                            }
                        });
                        
                        //map2.setView([res.view.lat, res.view.lon], 8);
                        
                        $.each(res.declenches_non_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: orangeIcon
                            })
                                .addTo(map2);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });


                        $.each(res.declenches_fdal, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: blueIcon
                            })
                                .addTo(map2);


                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });

                    }

                });
               

            });
        });
    </script>

@endpush
