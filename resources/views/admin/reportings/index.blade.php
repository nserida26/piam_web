@extends('layouts.admin')
@section('title')
    @lang('sidebar.reporting')
@endsection
@section('contentheader')
    @lang('sidebar.reporting')
@endsection
@php
    $safeTaux = function ($numerator, $denominator, $decimals = 1) {
        $denominator = (float) ($denominator ?? 0);

        if ($denominator == 0.0) {
            return formatTaux(0, $decimals);
        }

        return formatTaux(((float) ($numerator ?? 0) * 100) / $denominator, $decimals);
    };

    $safeTauxValue = function ($numerator, $denominator) {
        $denominator = (float) ($denominator ?? 0);

        if ($denominator == 0.0) {
            return 0;
        }

        return ((float) ($numerator ?? 0) * 100) / $denominator;
    };

    $safeNumber = function ($numerator, $denominator, $decimals = 0) {
        $denominator = (float) ($denominator ?? 0);

        if ($denominator == 0.0) {
            return formatNumberReport(0, $decimals);
        }

        return formatNumberReport(((float) ($numerator ?? 0)) / $denominator, $decimals);
    };
@endphp

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
            line-height: 1.6;
            width: 100%;
            font-size: 20px;
            text-align: justify;
        }
    
        .text-container p {
            margin-bottom: 8px;
            line-height: 1.4;
        }
    
        .text-container li {
            line-height: 1.4;
            margin-bottom: 5px;
        }
    
        h5 {
            background-color: #00A95C;
            text-align: center;
            padding: 10px;
            color: white;
            margin: 20px 0 10px 0;
            width: 100%;
            box-sizing: border-box;
            font-weight: bold !important; /* AJOUTÉ - TEXTE EN GRAS */
            font-size: 22px; /* Taille légèrement augmentée */
        }
    
        h6 {
            background: #FFD700;
            text-align: center;
            padding: 8px;
            margin: 15px 0 10px 0;
            width: 100%;
            box-sizing: border-box;
            font-weight: bold !important; /* AJOUTÉ - TEXTE EN GRAS */
            font-size: 20px; /* Taille légèrement augmentée */
        }
    
        .table-element {
            border-color: #000000;
            width: 100%;
            border-collapse: collapse;
            
            
        }
    
        .table-element thead th,
        .table-element th[scope="row"] {
            font-weight: bold;
            background-color: #f8f9fa;
            font-size: 18px;
        }
    
        .table-element td {
            text-align: right;
            padding: 8px 12px;
            font-size: 18px;
        }
    
        .table-element td:first-child,
        .table-element th:first-child {
            text-align: left;
            font-size: 18px;
        }
    


        /* Styles pour l'impression */
        @media print {
            body * {
                visibility: hidden;
            }
            
            #content, #content * {
                visibility: visible;
            }
            
            #content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            
            .no-print, .no-print * {
                display: none !important;
            }
            
            .text-container {
                border: 1px solid #000;
                font-size: 18px;
                page-break-inside: avoid;
            }
            
            .table-element {
                font-size: 18px;
                page-break-inside: avoid;
            }
            
            h5, h6 {
                page-break-inside: avoid;
                page-break-after: avoid;
            }
            
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            
            /* Éviter les coupures dans les tableaux */
            table { 
                page-break-inside: auto;
            }
            tr { 
                page-break-inside: avoid; 
                page-break-after: auto;
            }
        }
    
        .btn-print {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
        }
    
        .btn-print:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    
        #content {
            background: white;
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
        /* Styles pour les indicateurs de chargement */
.spinner-border {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}

.visually-hidden {
    position: absolute !important;
    width: 1px !important;
    height: 1px !important;
    padding: 0 !important;
    margin: -1px !important;
    overflow: hidden !important;
    clip: rect(0,0,0,0) !important;
    white-space: nowrap !important;
    border: 0 !important;
}

/* Amélioration de l'impression */
@media print {
    @page {
        size: A4;
        margin: 1.5cm;
    }
    
    body {
        font-size: 18px !important;
        line-height: 1.4 !important;
    }
    
    .text-container {
        font-size: 18px !important;
        border: 1px solid #000 !important;
        padding: 10px !important;
        margin: 10px 0 !important;
    }
    
    .table-element {
        font-size: 18px !important;
        max-width: 100% !important;
        page-break-inside: avoid !important;
    }
    
    h5 {
        font-size: 22px !important;
        background-color: #00A95C !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    h6 {
        font-size: 20px !important;
        background-color: #FFD700 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    canvas {
        max-height: 200px !important;
        page-break-inside: avoid !important;
    }
    
    .leaflet-container {
        max-height: 300px !important;
        page-break-inside: avoid !important;
    }
    
    .no-print,
    .no-print * {
        display: none !important;
    }
    
    /* Empêcher les coupures malheureuses */
    .keep-together {
        page-break-inside: avoid !important;
    }
    
    /* Forcer un saut de page avant */
    .page-break-before {
        page-break-before: always !important;
    }
    
    /* Forcer un saut de page après */
    .page-break-after {
        page-break-after: always !important;
    }
}

/* Amélioration de l'expérience utilisateur */
#generate-pdf, #print-report {
    position: relative;
    min-width: 150px;
}

#generate-pdf .loading,
#print-report .loading {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}

/* Style pour le header fixe en impression */
.pdf-header {
    display: none;
}

@media print {
    .pdf-header {
        display: block;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: white;
        padding: 10px;
        border-bottom: 2px solid #00A95C;
        text-align: center;
        font-weight: bold;
        z-index: 1000;
    }
    
    body {
        padding-top: 80px !important;
    }
}
/* Styles supplémentaires pour la gestion des sauts de page */
@media print {
    /* Empêcher les coupures dans les tableaux */
    table {
        page-break-inside: auto !important;
    }
    
    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    
    thead {
        display: table-header-group !important;
    }
    
    tfoot {
        display: table-footer-group !important;
    }
    
    /* Classes pour la gestion des sauts de page */
    .keep-together {
        page-break-inside: avoid !important;
    }
    
    .page-break-before {
        page-break-before: always !important;
    }
    
    .page-break-after {
        page-break-after: always !important;
    }
    
    .keep-with-next {
        page-break-after: avoid !important;
    }
    
    .keep-with-previous {
        page-break-before: avoid !important;
    }
    
    /* S'assurer que les titres restent avec leur contenu */
    h6 + table,
    h6 + .text-container {
        page-break-before: avoid !important;
    }
    
    /* Forcer un saut de page avant les grandes sections */
    h5:not(:first-of-type) {
        page-break-before: always !important;
        margin-top: 30px !important;
    }
    
    /* Éviter les coupures dans les conteneurs de texte */
    .text-container {
        page-break-inside: avoid !important;
    }
    
    /* Ajuster les marges pour l'impression */
    .table-element {
        margin: 15px 0 !important;
    }
}
    </style>
@endpush
@section('content')
    <div class="card">
    <div class="card-header">
        <p class="card-title card_title_center">@lang('general.filter')</p>
    </div>
    <!-- /.card-header -->

    <div class="card-body">
        <div class="row">

            <!-- 🟦 Wilaya -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="wilaya" class="form-label fw-bold">Wilaya</label>
                    <select name="wilaya" id="wilaya" class="form-control wilaya">
                        <option value="all" selected>TOUTES LES WILAYAS</option>
                        @foreach ($wilayas as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🟩 Moughataa -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="moughata" class="form-label fw-bold">Moughataa</label>
                    <select name="moughata" id="moughata" class="form-control moughata">
                    </select>
                </div>
            </div>

            <!-- 🟧 Commune -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="commune" class="form-label fw-bold">Commune</label>
                    <select name="commune" id="commune" class="form-control commune">
                    </select>
                </div>
            </div>

            <!-- 🟨 Type d'assainissement -->
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
                <h4 class="card_title_center" id="rapport">Rapport du pays</h4>
            </div>
        </div>

        <div id="familial">
            <div class="row">
                <div class="col-12">
                    <h5>DONNEES GENERALES</h5>
                    <table class="mb-4 table-element" border="3">

                        <tbody>

                            <tr>
                                <th id="territoire">Pays</th>
                                <td id="intitule">Mauritanie</td>
                            </tr>
                            <tr>
                                <th>Date</th>
                                <td id="date">{{ date('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Nb de wilayas</th>
                                <td id="nb_wilayas">{{ formatNumberReport($report['donnees_generales']['nb_wilayas']) }}</td>
                            </tr>
                            <tr>
                                <th>Nb de moughataas</th>
                                <td id="nb_moughatas">{{ formatNumberReport($report['donnees_generales']['nb_moughatas']) }}</td>
                            </tr>
                            <tr>
                                <th>Nb de communes</th>
                                <td id="nb_communes">{{ $report['donnees_generales']['nb_communes'] }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de localités codifiées ANSADE</th>
                                <td id="nb_localites_codifiees">
                                    {{ formatNumberReport($report['donnees_generales']['nb_localites_codifiees']) }}</td>
                            </tr>
                            <tr>
                                <th>Nombre de localités non codifiées ANSADE</th>
                                <td id="nb_localites_non_codifiees">
                                    {{ $report['donnees_generales']['nb_localites_non_codifiees'] }}</td>
                            </tr>
                            <tr>
                                <th>Nb de localites</th>
                                <td id="nb_localites">{{ formatNumberReport($report['donnees_generales']['nb_localites']) }}</td>
                            </tr>
                            <tr>
                                <th>Population estimée</th>
                                <td id="population">{{ formatNumberReport($report['donnees_generales']['population']) }}</td>
                            </tr>
                            <tr>
                                <th>Nombre d'écoles fondamentales</th>
                                <td id="nb_ecoles">{{ formatNumberReport($report['donnees_generales']['nb_ecoles']) }}</td>
                            </tr>
                            <tr>
                                <th>Nombre total d'élèves</th>
                                <td id="nb_eleves">{{ formatNumberReport($report['donnees_generales']['nb_eleves']) }}</td>
                            </tr>
                            <tr>
                                <th>Nb de formations sanitaires</th>
                                <td id="nb_santes">{{ formatNumberReport($report['donnees_generales']['nb_santes']) }}</td>
                            </tr>


                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row">
                <div class="col-12">

                    <h5>ACCES A L'EAU, L'ASSAINISSEMENT ET L'HYGIENE DES MENAGES
                    </h5>
                    <div class="text-container">

                        Objectif de la Mauritanie : Chaque ménage dispose de latrines améliorées et d'un dispositif de
                        
                        lave-mains fonctionnel à l'horizon 2030.

                    </div>
                    <div class="text-container">

                        L'Assainissement Total Piloté par la Communauté (ATPC) est l'approche adoptée par la Mauritanie
                            depuis 
                        2011 pour promouvoir l'assainissement en milieu rural. L'ATPC consiste à encourager la communauté
                            à 
                        analyser sa propre situation en matière d'hygiène et d'assainissement et notamment ses pratiques
                            en
                        matière de défécation avec leurs conséquences. Elle vise à susciter une action collective
                            destinée à
                        atteindre et maintenir un état de Fin de la Défécation à l'Air Libre (FDAL). Cet état est
                            atteint, à priori, par la 
                        construction spontanée de latrines familiales sans qu'il soit nécessaire de prévoir une
                            subvention 
                        extérieure aux ménages concernés.
                    </div>
                    <h6>Projets ayant mis en œuvre l'ATPC
                    </h6>

                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Projet ayant mis en œuvre l'ATPC</th>
                                <th>Opérateur</th>
                                <th>Période</th>
                                <th>Nb de localités ciblées</th>

                            </tr>
                        </thead>
                        <tbody id="projets">
                            @foreach ($report['access_assainisement_familial']['projets'] as $projet)
                                <tr>
                                    <td>{{ $projet->intitule_resume }}</td>
                                    <td>{{ $projet->intitule }}</td>
                                    <td> @isset($projet->year)
                                            {{ $projet->year }}
                                        @endisset </td>
                                    <td>{{ $projet->localites }}</td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h6> Cibles de la mise en œuvre de l'ATPC
                    </h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Indicateur</th>
                                <th>Nombre</th>
                                <th>Taux</th>


                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <th>Villages déclenchés une première fois (codifiés ANSADE)</th>
                                <td id="villages_declenches_cod">
                                    {{ formatNumberReport($report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod']) }}
                                </td>
                                <td id="taux_villages_declenches_cod">
                                    {{ $safeTaux($report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'], $report['donnees_generales']['nb_localites_codifiees']) }}
                                    

                                </td>
                            </tr>
                            <tr>
                                <th>Villages déclenchés une première fois (non codifiés ANSADE)</th>
                                <td id="villages_declenches_noncod">
                                    {{ formatNumberReport($report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod']) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Villages déclenchés une première fois</th>
                                <td id="villages_declenches">
                                    {{ formatNumberReport($report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] + $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod']) }}
                                </td>
                                <td id="taux_villages_declenches">
                                    {{ $safeTaux($report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod'] + $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'], $report['donnees_generales']['nb_localites_codifiees'], 1) }}
                                    

                                </td>
                            </tr>
                            <tr>
                                <th>Villages redéclenchés (codifiés ANSADE)</th>
                                <td id="villages_redeclenches_cod">
                                    {{ formatNumberReport($report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod']) }}
                                </td>
                                <td id="taux_villages_redeclenches_cod">
                                    {{ $safeTaux($report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'], $report['donnees_generales']['nb_localites_codifiees']) }}
                                    
                                </td>
                            </tr>
                            <tr>
                                <th>Villages redéclenchés (non codifiés ANSADE)</th>
                                <td id="villages_redeclenches_noncod">
                                    {{ formatNumberReport($report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod']) }}
                                </td>
                            </tr>
                            <tr>
                                <th>Villages bénéficiaires d'un appui-suivi</th>
                                <td id="villages_appui_suivi">
                                    {{ $report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'] }}
                                </td>
                                <td id="taux_villages_appui_suivi">
                                    {{ $safeTaux($report['access_assainisement_familial']['mise_atcp']['villages_appui_suivi'], $report['donnees_generales']['nb_localites_codifiees']) }}
                                

                                </td>
                            </tr>
                            <tr>
                                <th>Population totale touchée par l'ATPC</th>
                                <td id="pm_population">
                                    {{ formatNumberReport($report['access_assainisement_familial']['pm']['population']) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Nombre de ménages touchés par l'ATPC</th>
                                <td id="pm_menages">{{ formatNumberReport($report['access_assainisement_familial']['pm']['menages']) }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Villages jamais déclenchés</th>
                                <td id="villages_jamais_declenches">
                                    {{ formatNumberReport($report['donnees_generales']['nb_localites_codifiees'] - $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod']) }}
                                </td>
                                <td id="taux_villages_jamais_declenches">
                                    {{ $safeTaux($report['donnees_generales']['nb_localites_codifiees'] - $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'], $report['donnees_generales']['nb_localites_codifiees']) }}
                                    

                                </td>
                            </tr>


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart0')</h6>
                    <canvas id="chart0"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-yellow.png">@lang('map.title') | <img
                            src="/assets/admin/dist/img/marker-icon-green.png">@lang('map.title2') | <img
                            src="/assets/admin/dist/img/marker-icon-red.png">@lang('map.title3')</h6>
                    <div class="leaflet-container" id="map"></div>
                    @php
                        $menages = $report['access_assainisement_familial']['pm']['menages'];
                        $menages_EDL_max = $report['menages_EDL_max'];
                        $limite = 100 - (
                            $safeTauxValue($report['access_assainisement_familial']['base'], $menages_EDL_max)
                            + $safeTauxValue($report['access_assainisement_familial']['non_ameliores'], $menages_EDL_max)
                            + $safeTauxValue($report['access_assainisement_familial']['men_dal'], $menages)
                        );
                        
                    @endphp

                    <h6>Accès à l'eau des localités ciblées par l'ATPC</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Source d'approvisionnement</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody id="approv_eau">

                            @foreach ($report['access_assainisement_familial']['approv_eau'] as $approv_eau)
                                <tr>
                                    <td>{{ isset($approv_eau->approv_eau) ? $approv_eau->approv_eau : 'NR' }}</td>
                                    <td>{{ $approv_eau->localites }}</td>
                                    <td>{{ $safeTaux($approv_eau->localites, $report['donnees_generales']['nb_localites']) }}
                                    
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>

                    <h6>@lang('tb.chart4')</h6>
                    <canvas id="chart4"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>

                    <h6>Certification FDAL </h6>
                    <div class="text-container">

                        La certification FDAL (Fin de la Défécation à l'Air Libre) d'une localité est réalisée par le
                            Comité Régional 

                        de Suivi (CRS) selon les critères définis par la Direction de l'Assainissement, à savoir :
                        
                        <ul>
                            <li>Les sites de défécation à l'air libre ont totalement disparu </li>
                            <li>80 % des ménages disposent de latrines </li>
                            <li>Un plan d'action existe pour équiper les 20 % de ménages restant dans les 6 mois </li>
                            <li>100 % des ménages utilisent des latrines </li>
                            <li>60 % des latrines sont associées à un dispositif de lave-mains fonctionnel </li>
                        </ul>

                    </div>
                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Année</th>
                            <th>Nombre de localités déclenchées</th>
                            <th>Nombre de localités certifiées FDAL </th>

                        </tr>
                        <tbody id="fdal">
                            @php
                                $cumul_decle = 0;
                                $cumul_certif = 0;
                            @endphp
                            @foreach ($report['access_assainisement_familial']['fdal'] as $fdal)
                                @php
                                    if (isset($fdal->localites_1)) {
                                        $cumul_decle += $fdal->localites_1;
                                    } else {
                                        $cumul_decle += 0;
                                    }
                                    if (isset($fdal->localites_3)) {
                                        $cumul_certif += $fdal->localites_3;
                                    } else {
                                        $cumul_certif += 0;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $fdal->year }}</td>
                                    <td>{{ isset($fdal->localites_1) ? $fdal->localites_1 : 0 }}</td>
                                    <td>{{ isset($fdal->localites_3) ? $fdal->localites_3 : 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    

                    <table class="mb-4 table-element" border="3">

                        <tbody>


                            <tr>
                                <th>Taux de certification</th>
                                <td id="certification_taux">
                                    @if (isset($cumul_decle) && isset($cumul_certif) && $cumul_decle != 0)
                                        {{ $safeTaux($cumul_certif, $cumul_decle) }} 
                                    @endif
                                </td>


                            </tr>
                            <tr>
                                <th>Nb de localités déclenchées et non certifiées FDAL</th>
                                <td id="certification_nbr">
                                    @if (isset($cumul_decle) && isset($cumul_certif) && $cumul_decle >= $cumul_certif)
                                        {{ $cumul_decle - $cumul_certif }}
                                    @endif
                                </td>

                            </tr>



                        </tbody>
                    </table>
                    <h6>@lang('tb.chart1')</h6>
                    <canvas id="chart1"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-orange.png">@lang('map.title4') | <img
                            src="/assets/admin/dist/img/marker-icon-blue.png">@lang('map.title5')</h6>
                        <div class="leaflet-container" id="map2"></div>
                    <h6>Résultats de la mise en œuvre de l'ATPC</h6>

                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Indicateur</th>
                                <th>Nombre ou taux</th>
                                <th>Nombre localités prises en compte </th>

                            </tr>
                        </thead>
                        <tbody id="resultas">

                            <tr>
                                <td>Nombre de latrines familiales avant mise en œuvre de l'ATPC</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['couvertures_EDL']) }}</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localites_couvertures_EDL']) }}
                                </td>

                            </tr>
                            <tr>
                                <td>Taux de couverture initial en latrines familiales avant mise en œuvre de l'ATPC</td>
                                <td>
                                    {{ $safeTaux($report['access_assainisement_familial']['couvertures_EDL'], $report['access_assainisement_familial']['menages_EDL1']) }}


                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines familiales lors du dernier suivi</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['couvertures_dernier']) }}</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localites_couvertures_dernier']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de couverture final en latrines familiales</td>

                                <td>
                                    {{ $safeTaux($report['access_assainisement_familial']['couvertures_dernier'], $report['menages_EDL_max']) }}

                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Nombre de nouvelles latrines construites</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nouv_latr_const']) }}
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localites_nouv_latr_const']) }}
                                </td>

                            </tr>
                            <tr>
                                <td>Taux de latrines auto-construites</td>

                                <td>

                                    {{ $safeTaux($report['access_assainisement_familial']['nbr_latrine_autoconst'], $report['access_assainisement_familial']['nouv_latr_const']) }}

                                </td>
                                <td>
                                    {{ formatNumberReport2($report['access_assainisement_familial']['nbr_localites_latrine_autoconst']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines réalisées avec l'aide d'un maçon</td>

                                <td>

                                    {{ $safeTaux($report['access_assainisement_familial']['nbr_latrine_realise_macon'], $report['access_assainisement_familial']['nouv_latr_const']) }}
                                </td>
                                <td>
                                    {{ formatNumberReport2($report['access_assainisement_familial']['nbr_localites_latrine_realise_macon']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines familiales en construction lors du dernier suivi</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_latrine_en_constr']) }}
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_localites_latrine_en_constr']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_menages_ltr_end_phm']) }}
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_localites_menages_ltr_end_phm']) }}
                                </td>
                            </tr>

                            <tr>
                                <td>Nb de latrines familiales associées à un DLM fonctionnel</td>

                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_menages_dlm']->nbr_dlm) }}
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nbr_menages_dlm']->nbr_localites) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines associées à un DLM fonctionnel</td>
                                
                                <td>
                                    {{ $safeTaux($report['access_assainisement_familial']['nbr_menages_dlm']->nbr_dlm, $menages) }}
                                    
                                </td>
                                <td></td>
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

                            <tr>
                                <td>Taux de latrines auto-financées</td>
                                <td>{{ formatTaux($report['access_assainisement_familial']['auto_finance']) }}
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['localites_latrine_autofin']) }}
                                </td>

                            </tr>


                            <tr>
                                <td>Nombre de ménages pour lesquels les données d'investissement ont été recueillies
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_menages_inv']) }}</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['localites_latr_invest_men_dispo']) }}
                                </td>
                            </tr>


                            <tr>
                                <td>Montant total investi par les ménages dans leurs latrines

                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['investi']) }}</td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['localites_mont_invest_men']) }}
                                </td>
                            </tr>


                            <tr>
                                <td>Montant moyen investi par ménage dans une latrine
                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['investi_menages']) }}
                                </td>
                                <td></td>
                            </tr>


                            <tr>
                                <td>Taux de latrines financées par entraide communautaire</td>
                                <td>
                                    {{ formatTaux($report['access_assainisement_familial']['latrines_entrainde']) }}

                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['localites_entrainde']) }}
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines subventionnées</td>
                                <td>
                                    {{ formatTaux($report['access_assainisement_familial']['latrines_subv']) }}

                                </td>
                                <td>
                                    {{ formatNumberReport($report['access_assainisement_familial']['localites_subv']) }}
                                </td>

                            </tr>


                        </tbody>
                    </table>
                    <h6>Accès des ménages à l'assainissement</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Classification selon les Objectifs de Développement Durable</th>


                                <th>Taux</th>
                                <th>Nombre localités prises en compte</th>

                            </tr>
                        </thead>
                        <tbody id="access_assainisement_familial">



                            <tr>
                                <td>Accès à un service d'assainissement sécurisé</td>
                                <td ID="securise">
                                    {{ $safeTaux($report['access_assainisement_familial']['securise'], $menages_EDL_max) }} 
                                </td>
                                <td id="nb_localite_securise">
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localite_securise']) }}

                                </td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'assainissement basique (latrines améliorées)
                                </td>

                                <td ID="base">
                                    {{ $safeTaux($report['access_assainisement_familial']['base'], $menages_EDL_max) }}
                                    
                                </td>
                                <td id="nb_localite_base">
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localite_base']) }}

                                </td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'assainissement limité (latrines partagées)

                                </td>

                                <td id="limite">
                                    {{ formatTaux($limite) }}
                                    
                                </td>
                                <td id="nb_localite_limite">
                                </td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'assainissement non amélioré
                                </td>
                                <td id="non_ameliores">
                                    {{ $safeTaux($report['access_assainisement_familial']['non_ameliores'], $menages_EDL_max) }}
                                
                                </td>
                                <td id="nb_localite_non_ameliores">
                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localite_non_ameliores']) }}
                                </td>
                            </tr>


                            <tr>
                                <td>Taux de ménages déféquant à l'air libre</td>
                                <td id="men_dal">
                                    {{ $safeTaux($report['access_assainisement_familial']['men_dal'], $menages) }}
                                    
                                </td>
                                <td id="nb_localite_men_dal">

                                    {{ formatNumberReport($report['access_assainisement_familial']['nb_localite_men_dal']) }}
                                </td>
                            </tr>




                        </tbody>
                    </table>
                    <h6>@lang('tb.chart2')</h6>
                    <canvas id="chart2"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>

                    <!-- /.card -->

                    <h6>Accès des ménages à l'hygiène</h6>

                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Classification selon les Objectifs de Développement Durable</th>
                                <th>Taux</th>
                                <th>Nb localités prises en compte</th>
                            </tr>
                        </thead>
                        <tbody id="access_hygiene">



                            @php
                                $m = $report['access_hygiene'];
                            @endphp



                            <tr>
                                <td>Accès à un service d'hygiène de base (eau + savon)</td>
                                <td id="nbr_menages_dlm_complet">{{ formatNumberReport($m->nbr_menages_dlm_complet) }}</td>
                                <td id="nbr_localites_complet">{{ formatNumberReport($m->nbr_localites_complet) }}</td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'hygiène limité (eau ou savon) </td>
                                <td id="nbr_menages_dlm_incomplet">{{ formatNumberReport($m->nbr_menages_dlm_incomplet) }}</td>
                                <td id="nbr_localites_incomplet">{{ formatNumberReport($m->nbr_localites_incomplet) }}</td>
                            </tr>


                            <tr>
                                <td>Aucun service</td>
                                <td id="nbr_menages_sans_dlm">{{ formatNumberReport($m->nbr_menages_sans_dlm) }}</td>
                                <td id="nbr_sans_dlm">{{ formatNumberReport($m->nbr_sans_dlm) }}</td>
                            </tr>


                        </tbody>
                    </table>

                    <h6>@lang('tb.chart3')</h6>
                    <canvas id="chart3"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
                <!-- /.col -->
            </div>
        </div>


        <div id="public">
            <div class="row" id="ecoles">
                <div class="col-12">

                    <h5>ACCES A L'ASSAINISSEMENT ET L'HYGIENE DES ECOLES
                        FONDAMENTALES</h5>
                    <div class="text-container">

                        Objectif de la Mauritanie : Permettre aux enseignants (es) et aux élèves de l'ensemble des écoles
                        
                        l'accès aux toilettes et à un dispositif de lave-mains fonctionnels à l'horizon 2030, avec une
                        
                        attention particulière pour les personnes à mobilité réduite et la gestion de l'hygiène
                    </div>
                    <h6>Accès à l'eau des écoles</h6>
                    
                    <h6>Rappel des normes actuelles relatives à l'équipement en latrines des écoles</h6>

                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Norme</th>
                            <th>Idéal</th>
                            <th>Acceptable</th>
                        </tr>
                        <tr>
                            <td>Cabines filles</td>
                            <td>1 cabine par tranche de 40 filles, dans un bloc séparé</td>
                            <td>1 cabine par tranche de 50 filles</td>
                        </tr>
                        <tr>
                            <td>Cabines garçons</td>
                            <td>1 cabine par tranche de 50 garçons, dans un bloc séparé</td>
                            <td>1 cabine par tranche de 70 garçons, jusqu'à 100 si urinoirs</td>
                        </tr>
                        <tr>
                            <td>Cabines enseignants</td>
                            <td>1 bloc réservé aux enseignants avec une cabine latrines et une cabine douche</td>
                            <td>1 cabine réservée aux enseignants, dans le bloc garçons</td>
                        </tr>
                        <tr>
                            <td>Cabines accessibles aux Personnes à Mobilité Réduite (PMR)</td>
                            <td>1 cabine spéciale (porte large, siège, barres d'appui) réservée dans le bloc enseignants
                            </td>
                            <td>Rampe pour accessibilité des blocs élèves</td>
                        </tr>
                    </table>

                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Source d'approvisionnement</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody id="approv_eau_ecoles">

                            @foreach ($report['ecoles']['approv_eau'] as $approv_eau)
                                <tr>

                                    <td>{{ isset($approv_eau->approv_eau) ? $approv_eau->approv_eau : 'NR' }}</td>
                                    <td>{{ formatNumberReport($approv_eau->infrastructures) }}</td>
                                    <td>{{ formatNumberReport($approv_eau->infrastructures) }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart5')</h6>
                    <canvas id="chart5"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>

                    <h6 class="mt-3">Accès à l'assainissement des écoles</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Indicateur</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $acces_assainissement = $report['ecoles']['acces_assainissement'];
                            @endphp

                            <tr>

                                <td>Couverture des écoles en latrines </td>
                                <td id="couvert_ecole_latr">{{ $acces_assainissement->couvert_ecole_latr }}</td>
                                <td id="taux_couvert_ecole_latr">
                                    {{ $safeTaux($acces_assainissement->couvert_ecole_latr, $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>Elèves ayant accès à l'assainissement</td>
                                <td id="effectif">{{ $acces_assainissement->effectif }}</td>
                                <td id="taux_effectif">
                                    {{ $safeTaux($acces_assainissement->effectif, $report['donnees_generales']['nb_eleves']) }}
                                    
                                </td>

                            </tr>
                            <tr>
                                <td>Ratio actuel (nb d'élèves par cabine)</td>
                                <td id="ratio">
                                    {{ $safeNumber($acces_assainissement->effectif, $acces_assainissement->nbr_cabine_fonct_sum) }}
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Ecoles dont les cabines filles et garçons sont bien distinctes</td>
                                <td id="dist_cab_hom_fem">{{ formatNumberReport($acces_assainissement->dist_cab_hom_fem) }}</td>
                                <td id="taux_dist_cab_hom_fem">
                                    {{ $safeTaux($acces_assainissement->dist_cab_hom_fem, $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>

                            </tr>
                            <tr>
                                <td>Ecoles dont les latrines permettent une bonne gestion de l'hygiène menstruelle
                                </td>
                                <td id="nbr_cabine_fem_hyg">{{ formatNumberReport($acces_assainissement->nbr_cabine_fem_hyg) }}</td>
                                <td id="taux_nbr_cabine_fem_hyg">
                                    {{ $safeTaux($acces_assainissement->nbr_cabine_fem_hyg, $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>


                            </tr>
                            <tr>
                                <td>Ecoles dont les latrines sont accessibles aux personnes à mobilité réduite</td>
                                <td id="nbr_cabine_pmr">{{ formatNumberReport($acces_assainissement->nbr_cabine_pmr) }}</td>
                                <td id="taux_nbr_cabine_pmr">
                                    {{ $safeTaux($acces_assainissement->nbr_cabine_pmr, $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>
                            </tr>

                            <tr>
                                <td>Ecoles dont les latrines sont associées à un dispositif de lave-mains
                                    fonctionnel
                                </td>
                                <td id="nb_dlm_instal">{{ formatNumberReport($acces_assainissement->nb_dlm_instal) }}</td>
                                <td id="taux_nb_dlm_instal">
                                    {{ $safeTaux($acces_assainissement->nb_dlm_instal, $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>
                            </tr>




                        </tbody>
                    </table>
                    


                    <h6>Besoins en assainissement des écoles</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Indicateur</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        @php
                            $besoin_assainissement = $report['ecoles']['besoin_assainissement'];
                        @endphp
                        <tbody id="besoin_assainissement">


                            <tr>
                                <td>Ecoles dont les besoins en latrines sont satisfaits </td>
                                <td id="nb_ecole_besoin_latr_satisf">
                                    {{ formatNumberReport($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf']) }}</td>
                                <td id="taux_nb_ecole_besoin_latr_satisf">
                                    {{ $safeTaux($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf'], $report['donnees_generales']['nb_ecoles']) }}
                                
                                </td>
                            </tr>
                            <tr>
                                <td>Ecoles dont les besoins en latrines ne sont pas satisfaits</td>
                                <td id="nb_ecole_besoin_latr_non_satisf">
                                    {{ formatNumberReport($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf']) }}
                                </td>
                                <td id="taux_nb_ecole_besoin_latr_non_satisf">
                                    {{ $safeTaux($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf'], $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>

                            </tr>
                            <tr>
                                <td>Ecoles ne disposant d'aucunes latrines</td>
                                <td id="nb_ecole_sans_latr">
                                    {{ formatNumberReport($report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr']) }}
                                </td>
                                <td id="taux_nb_ecole_sans_latr">
                                    {{ $safeTaux($report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr'], $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>Nb estimatif de cabines de latrines à construire dans les écoles</td>
                                <td id="nbr_cabine_a_const">
                                    {{ formatNumberReport($report['ecoles']['besoin_assainissement']['nbr_cabine_a_const']) }}</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Ecoles dont les besoins sont pris en compte par un projet</td>
                                <td id="projet_besoins">{{ formatNumberReport($report['ecoles']['projet_besoins']) }}</td>
                                <td id="taux_projet_besoins">
                                    {{ $safeTaux($report['ecoles']['projet_besoins'], $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>


                            </tr>

                            <tr>
                                <td>Ecoles dont les besoins ne sont pas pris en compte par un projet
                                </td>
                                <td id="projet_sans_besoins">{{ formatNumberReport($report['ecoles']['projet_sans_besoins']) }}</td>
                                <td id="taux_projet_sans_besoins">
                                    {{ $safeTaux($report['ecoles']['projet_sans_besoins'], $report['donnees_generales']['nb_ecoles']) }}
                                    
                                </td>
                            </tr>



                        </tbody>
                    </table>
                    <h6>@lang('tb.chart6')</h6>
                    <canvas id="chart6"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title6') | <img
                                src="/assets/admin/dist/img/marker-icon-grey.png">@lang('map.title7') | <img
                                src="/assets/admin/dist/img/marker-icon-violet.png">@lang('map.title8')</h6>
                    <div class="leaflet-container" id="map3"></div>
                    <h6>Projets passés de réalisation de latrines</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Intitulé du projet </th>
                                <th>Opérateur</th>
                                <th>Année de réception</th>
                                <th>Nombre d'écoles ciblées</th>
                                <th>Nombre de cabines réalisées</th>
                                <th>Nombre de cabines réhabilitées</th>
                            </tr>
                        </thead>
                        <tbody id="projets_passes_ecoles">

                            @foreach ($report['ecoles']['projets_passees'] as $projets_passees)
                                <tr>

                                    <td>{{ $projets_passees->intitule_resume }}</td>
                                    <td>{{ $projets_passees->intitule }}</td>
                                    <td>{{ $projets_passees->an_recept }}</td>
                                    <td>{{ $projets_passees->infrastructures }}</td>
                                    <td>{{ $projets_passees->nbr_cabine_const }}</td>
                                    <td>{{ $projets_passees->nbr_cabine_rehab }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>

                    <h6>Projets en perspective de réalisation de latrines</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Intitulé du projet </th>
                                <th>Opérateur</th>
                                <th>Année de réception</th>
                                <th>Nombre d'écoles ciblées</th>
                                <th>Nombre de cabines à réaliser</th>
                                <th>Nombre de cabines à réhabiliter</th>
                            </tr>
                        </thead>
                        @php
                            $reh_ecoles = 0;
                            $const_ecoles = 0;
                            $ecoles = 0;
                        @endphp
                        <tbody id="projets_perspectives_ecoles">

                            @foreach ($report['ecoles']['projets_perspectives'] as $projets_perspectives)
                                @php
                                    $reh_ecoles = $projets_perspectives->nbr_cabine_a_rehab + $reh_ecoles;
                                    $const_ecoles = $projets_perspectives->nbr_cabine_a_const + $reh_ecoles;
                                    $ecoles = $projets_perspectives->infrastructures + $reh_ecoles;
                                @endphp
                                <tr>

                                    <td>{{ $projets_perspectives->intitule_resume }}</td>
                                    <td>{{ $projets_perspectives->intitule }}</td>
                                    <td>{{ $projets_perspectives->an_program }}</td>
                                    <td>{{ $projets_perspectives->infrastructures }}</td>
                                    <td>{{ $projets_perspectives->nbr_cabine_a_const }}</td>
                                    <td>{{ $projets_perspectives->nbr_cabine_a_rehab }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title12')</h6>
                    <div class="leaflet-container" id="map5"></div>
                    <h6>Accès à l'hygiène des écoles</h6>
                    <table class="mb-4 table-element" border="3">
                        <tbody>
                            <tr>
                                <th>Classification selon les Objectifs de Développement Durable</th>
                                <th>Taux</th>
                                <th>Nombre d'écoles prises en compte</th>
                            </tr>


                            @php
                                $m = $report['ecoles']['access_hygiene'];
                            @endphp



                            <tr>
                                <td>Accès à un service d'hygiène de base</td>
                                <td id="nbr_ecoles_dlm_complet">{{ formatNumberReport($m->nb_dlm_complet) }}</td>
                                <td id="nbr_ecoles_complet">{{ $m->nbr_inf_complet }}</td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'hygiène limité</td>
                                <td id="nb_ecoles_dlm_incomplet">{{ $m->nb_dlm_incomplet }}</td>
                                <td id="nbr_ecoles_incomplet">{{ $m->nbr_inf_incomplet }}</td>
                            </tr>


                            <tr>
                                <td>Aucun service</td>
                                <td id="nb_ecoles_sans_dlm">{{ $m->nb_sans_dlm }}</td>
                                <td id="nbr_ecoles_sans">{{ $m->nbr_inf_sans }}</td>
                            </tr>


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart7')</h6>
                    <canvas id="chart7"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
                <!-- /.col -->
            </div>

            <div class="row" id="santes">
                <div class="col-12">


                    <h5>ACCES A L'EAU, L'ASSAINISSEMENT ET L'HYGIENE DES
                        STRUCTURES DE SANTE</h5>
                    <div class="text-container">

                        Objectif de la Mauritanie : Permettre au personnel de santé, aux patients et aux visiteurs de
                        
                        l'ensemble de formations sanitaires, l'accès aux toilettes et à un dispositif de lave-mains 
                        fonctionnels à l'horizon 2030, avec une attention particulière pour les personnes à mobilité 
                        réduite et la gestion de l'hygiène menstruelle</p>
                    </div>
                    <h5>Accès à l'assainissement des formations sanitaires</h5>

                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Source d'approvisionnement</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        <tbody id="approv_eau_santes">

                            @foreach ($report['santes']['approv_eau'] as $approv_eau)
                                <tr>

                                    <td>{{ isset($approv_eau->approv_eau) ? $approv_eau->approv_eau : 'NR' }}</td>
                                    <td>{{ $approv_eau->infrastructures }}</td>
                                    <td>{{ $approv_eau->infrastructures }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart8')</h6>
                    <canvas id="chart8"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    <h6>Accès à l'assainissement des formations sanitaires</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Indicateur</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        @php
                            $acces_assainissement = $report['santes']['acces_assainissement'];
                        @endphp
                        <tbody>


                            <tr>

                                <td>Couverture des formations sanitaires en latrines </td>
                                <td id="couvert_sante_latr">{{ $acces_assainissement->couvert_sante_latr }}</td>
                                <td id="taux_couvert_sante_latr">
                                    {{ $safeTaux($acces_assainissement->couvert_sante_latr, $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>
                            <tr>

                                <td>Formations sanitaires dont les cabines femmes et hommes sont bien distinctes </td>
                                <td id="dist_cab_hom_fem_sante">{{ $acces_assainissement->dist_cab_hom_fem }}
                                </td>
                                <td id="taux_dist_cab_hom_fem_sante">
                                    {{ $safeTaux($acces_assainissement->dist_cab_hom_fem, $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>


                            <tr>
                                <td>Formations sanitaires dont les latrines permettent une bonne gestion de l'hygiène
                                    menstruelle
                                </td>
                                <td id="nbr_cabine_fem_hyg_sante">{{ $acces_assainissement->nbr_cabine_fem_hyg }}
                                </td>
                                <td id="taux_nbr_cabine_fem_hyg_sante">
                                    {{ $safeTaux($acces_assainissement->nbr_cabine_fem_hyg, $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>


                            </tr>
                            <tr>
                                <td>Formations sanitaires dont les latrines sont accessibles aux personnes à mobilité
                                    réduite</td>
                                <td id="nbr_cabine_pmr_sante">{{ $acces_assainissement->nbr_cabine_pmr }}</td>
                                <td id="taux_nbr_cabine_pmr_sante">
                                    {{ $safeTaux($acces_assainissement->nbr_cabine_pmr, $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>

                            <tr>
                                <td>Formations sanitaires dont les latrines sont associées à un dispositif de
                                    lave-mains fonctionnel
                                </td>
                                <td id="nb_dlm_instal_sante">{{ $acces_assainissement->nb_dlm_instal }}</td>
                                <td id="taux_nb_dlm_instal_sante">
                                    {{ $safeTaux($acces_assainissement->nb_dlm_instal, $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h6>Besoins en assainissement des Formations sanitaires</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>
                                <th>Indicateur</th>
                                <th>Nombre</th>
                                <th>Taux</th>
                            </tr>
                        </thead>
                        @php
                            $besoin_assainissement = $report['santes']['besoin_assainissement'];
                        @endphp
                        <tbody>


                            <tr>
                                <td>Formations sanitaires dont les besoins en latrines sont satisfaits </td>
                                <td id="nb_sante_besoin_latr_satisf">
                                    {{ $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'] }}</td>
                                <td id="taux_nb_sante_besoin_latr_satisf">
                                    {{ $safeTaux($report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf'], $report['donnees_generales']['nb_santes']) }}
                                
                                </td>
                            </tr>
                            <tr>
                                <td>Formations sanitaires dont les besoins en latrines ne sont pas satisfaits</td>
                                <td id="nb_sante_besoin_latr_non_satisf">
                                    {{ $report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'] }}
                                </td>
                                <td id="taux_nb_sante_besoin_latr_non_satisf">
                                    {{ $safeTaux($report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf'], $report['donnees_generales']['nb_santes']) }}
                                
                                </td>

                            </tr>
                            <tr>
                                <td>Formations sanitaires ne disposant d'aucunes latrines</td>
                                <td id="nb_sante_sans_latr">
                                    {{ $report['santes']['besoin_assainissement']['nb_sante_sans_latr'] }}
                                </td>
                                <td id="taux_nb_sante_sans_latr">
                                    {{ $safeTaux($report['santes']['besoin_assainissement']['nb_sante_sans_latr'], $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre estimatif de cabines de latrines à construire dans les structures de
                                    santé</td>
                                <td id="nbr_cabine_a_const_sante">
                                    {{ $report['santes']['besoin_assainissement']['nbr_cabine_a_const'] }}</td>
                                <td></td>

                            </tr>
                            <tr>
                                <td>Formations sanitaires dont les besoins ne sont pas pris en compte par un projet
                                </td>
                                <td id="projet_sans_besoins_sante">{{ $report['santes']['projet_sans_besoins'] }}
                                </td>
                                <td id="taux_projet_sans_besoins_sante">
                                    {{ $safeTaux($report['santes']['projet_sans_besoins'], $report['donnees_generales']['nb_santes']) }}
                                    
                                </td>
                            </tr>


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart9')</h6>
                    <canvas id="chart9"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title9') | <img
            src="/assets/admin/dist/img/marker-icon-grey.png">@lang('map.title10') | <img
            src="/assets/admin/dist/img/marker-icon-violet.png">@lang('map.title11')</h6>
            <div class="leaflet-container" id="map4"></div>

                    <h6>Projets passés de réalisation de latrines</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Intitulé du projet </th>
                                <th>Opérateur</th>
                                <th>Année de réception</th>
                                <th>Nombre de Formations sanitaires ciblées</th>
                                <th>Nombre de cabines réalisées</th>
                                <th>Nombre de cabines réhabilitées</th>
                            </tr>
                        </thead>
                        <tbody id="projets_passes_santes">

                            @foreach ($report['santes']['projets_passees'] as $projets_passees)
                                <tr>

                                    <td>{{ $projets_passees->intitule_resume }}</td>
                                    <td>{{ $projets_passees->intitule }}</td>
                                    <td>{{ $projets_passees->an_recept }}</td>
                                    <td>{{ $projets_passees->infrastructures }}</td>
                                    <td>{{ $projets_passees->nbr_cabine_const }}</td>
                                    <td>{{ $projets_passees->nbr_cabine_rehab }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>

                    <h6>Projets en perspectives de réalisation de latrines</h6>
                    <table class="mb-4 table-element" border="3">
                        <thead>
                            <tr>

                                <th>Intitulé du projet </th>
                                <th>Opérateur</th>
                                <th>Année de réception</th>
                                <th>Nombre de Formations sanitaires ciblées</th>
                                <th>Nombre de cabines à réaliser</th>
                                <th>Nombre de cabines à réhabiliter</th>
                            </tr>
                        </thead>
                        <tbody id="projets_perspectives_santes">

                            @foreach ($report['santes']['projets_perspectives'] as $projets_perspectives)
                                <tr>

                                    <td>{{ $projets_perspectives->intitule_resume }}</td>
                                    <td>{{ $projets_perspectives->intitule }}</td>
                                    <td>{{ $projets_perspectives->an_program }}</td>
                                    <td>{{ $projets_perspectives->infrastructures }}</td>
                                    <td>{{ $projets_perspectives->nbr_cabine_a_const }}</td>
                                    <td>{{ $projets_perspectives->nbr_cabine_a_rehab }}</td>

                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title13')</h6>
                    <div class="leaflet-container" id="map6"></div>
                    <h6>Accès à l'hygiène des Formations sanitaires</h6>
                    <table class="mb-4 table-element" border="3">
                        <tbody>
                            <tr>
                                <th>Classification selon les Objectifs de Développement Durable</th>
                                <th>Taux</th>
                                <th>Nb localités prises en compte</th>
                            </tr>


                            @php
                                $m = $report['santes']['access_hygiene'];
                            @endphp



                            <tr>
                                <td>Accès à un service d'hygiène de base</td>
                                <td id="nbr_santes_dlm_complet">{{ $m->nb_dlm_complet }}</td>
                                <td id="nbr_santes_complet">{{ $m->nbr_inf_complet }}</td>
                            </tr>


                            <tr>
                                <td>Accès à un service d'hygiène limité</td>
                                <td id="nb_santes_dlm_incomplet">{{ $m->nb_dlm_incomplet }}</td>
                                <td id="nbr_santes_incomplet">{{ $m->nbr_inf_incomplet }}</td>
                            </tr>


                            <tr>
                                <td>Aucun service</td>
                                <td id="nb_santes_sans_dlm">{{ $m->nb_sans_dlm }}</td>
                                <td id="nbr_santes_sans">{{ $m->nbr_inf_sans }}</td>
                            </tr>


                        </tbody>
                    </table>
                    <h6>@lang('tb.chart10')</h6>
                    <canvas id="chart10"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
                <!-- /.col -->
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
    
    <!-- Bibliothèques pour PDF et impression -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    
    <!-- Bibliothèques pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    
    <!-- Bibliothèques pour les cartes -->
    <script src="{{ asset('assets/admin/js/BoundaryCanvas.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/leaflet.extra-markers.min.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/Leaflet.BigImage.min.js') }}"></script>
@endpush
@push('custom')
<script>
    $('#wilaya').on('change', function() {
            if ($(this).val() === 'all') {
                window.location.href = "{{ route('admin.reporting.index') }}";
            }
    });

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
        /* Styles d'impression */
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body {
                font-size: 12pt !important;
                line-height: 1.4 !important;
                font-family: "Arial", sans-serif !important;
                color: #000 !important;
                background: white !important;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 15px;
                border-bottom: 2px solid #00A95C;
            }
            
            .print-footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ccc;
                font-size: 10pt;
                color: #666;
            }
            
            .text-container {
                border: 1px solid #000 !important;
                padding: 12px !important;
                margin: 15px 0 !important;
                font-size: 11pt !important;
                page-break-inside: avoid !important;
                background: #f9f9f9 !important;
            }
            
            .table-element {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10pt !important;
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
            
            /* CORRECTION ICI : Utilisation cohérente de pt pour l'impression */
            h4 {
                text-align: center !important;
                font-size: 16pt !important;  /* Changé de 22px à 16pt */
                margin: 20px 0 10px 0 !important;
                color: #333 !important;
                font-weight: bold !important;
            }
            
            h5 {
                background-color: #00A95C !important;
                color: white !important;
                padding: 10px !important;
                margin: 20px 0 15px 0 !important;
                text-align: center !important;
                font-size: 14pt !important;  /* Changé de 22pt à 14pt (plus cohérent) */
                page-break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
            }
            
            h6 {
                background-color: #FFD700 !important;
                color: #000 !important;
                padding: 8px !important;
                margin: 15px 0 10px 0 !important;
                text-align: center !important;
                font-size: 13pt !important;  /* Changé de 20pt à 13pt */
                page-break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
            }
            
            /* Masquer les cartes et graphiques */
            .leaflet-container,
            #map, #map2, #map3, #map4, #map5, #map6,
            canvas {
                display: none !important;
            }
            
            /* Masquer les titres de légende des cartes */
            h6:has(img) {
                display: none !important;
            }
            
            /* Masquer les boutons d'action */
            .action-buttons {
                display: none !important;
            }
            
            /* Sauts de page */
            .page-break {
                page-break-before: always !important;
            }
            
            /* Éviter les coupures */
            .no-break {
                page-break-inside: avoid !important;
            }
            
            /* Masquer les éléments d'interface */
            .no-print {
                display: none !important;
            }
        }
        
        /* Styles pour l'aperçu à l'écran */
        @media screen {
            body {
                font-family: "Arial", sans-serif;
                padding: 20px;
                margin: 0;
                background: #f5f5f5;
            }
            
            .content-wrapper {
                max-width: 794px; /* Largeur A4 */
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
                font-size: 12px;
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
        
        /* Styles communs pour l'écran */
        body {
            font-family: "Arial", sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .text-container {
            border: 1px solid #000;
            padding: 15px;
            margin: 18px 0;
            font-size: 12px;
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
        
        /* CORRECTION ICI : Styles cohérents pour l'écran */
        h4 {
            text-align: center;
            margin: 20px 0 10px 0;
            font-size: 18px;  /* Taille cohérente pour l'écran */
            color: #333;
            font-weight: bold;
        }
        
        h5 {
            background-color: #00A95C;
            color: white;
            padding: 12px;
            margin: 25px 0 15px 0;
            text-align: center;
            font-size: 16px;  /* Taille cohérente pour l'écran */
            font-weight: bold;
        }
        
        h6 {
            background-color: #FFD700;
            color: #000;
            padding: 10px;
            margin: 20px 0 12px 0;
            text-align: center;
            font-size: 14px;  /* Taille cohérente pour l'écran */
            font-weight: bold;
        }
        
        /* Assurez-vous que ces styles sont toujours visibles */
        h4, h5, h6 {
            display: block !important;
            visibility: visible !important;
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
            <h1 style="color: #00A95C; margin-bottom: 10px;">${reportTitle}</h1>
            <p style="font-size: 14px; color: #666;">Rapport d'assainissement - Direction de l'Assainissement</p>
            <p style="font-size: 12px; color: #888;">Date de génération : ${date}</p>
        </div>
        
        <div class="print-notice no-print">
            <strong>Note :</strong> Les cartes géographiques et graphiques interactifs ne sont pas inclus dans cette version imprimée.
            Pour une vue complète avec les visualisations, utilisez la fonction d'export PDF.
        </div>
        
        <div id="print-content">
            ${clone.innerHTML}
        </div>
        
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></\script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></\script>
    <script>
        // Fonction pour obtenir le filtre actuel
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
        
        // Fonction pour charger des scripts
        function loadScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.head.appendChild(script);
            });
        }
        
        // Fonction pour exporter en PDF depuis la fenêtre d'impression
        async function exportToPDFFromPrintWindow() {
            const btn = document.getElementById('print-window-pdf');
            const originalText = btn.innerHTML;
            
            try {
                // Afficher l'indicateur de chargement
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération PDF...';
                btn.disabled = true;
                
                // Obtenir les bibliothèques nécessaires
                if (typeof window.jspdf === 'undefined' || typeof html2canvas === 'undefined') {
                    // Charger les bibliothèques si elles ne sont pas déjà chargées
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
                
                // Créer un div temporaire pour le contenu PDF
                const tempDiv = document.createElement('div');
                tempDiv.style.position = 'absolute';
                tempDiv.style.left = '-9999px';
                tempDiv.style.top = '0';
                tempDiv.style.width = '794px'; // Largeur A4
                tempDiv.style.backgroundColor = 'white';
                tempDiv.style.padding = '20px';
                
                // Cloner le contenu principal
                const pdfContent = content.cloneNode(true);
                
                // Supprimer les éléments non nécessaires
                const elementsToRemove = pdfContent.querySelectorAll(
                    '.action-buttons, .print-notice, .filter-info, .no-print'
                );
                elementsToRemove.forEach(el => el.remove());
                
                // Supprimer les cartes et graphiques
                const mapsAndCharts = pdfContent.querySelectorAll(
                    '.leaflet-container, canvas, #map, #map2, #map3, #map4, #map5, #map6'
                );
                mapsAndCharts.forEach(el => el.remove());
                
                // Supprimer les titres de légende des cartes
                const mapLegends = pdfContent.querySelectorAll('h6');
                mapLegends.forEach(legend => {
                    if (legend.innerHTML.includes('img')) {
                        legend.remove();
                    }
                });
                
                // Ajouter un en-tête au contenu
                const header = document.createElement('div');
                header.style.textAlign = 'center';
                header.style.marginBottom = '30px';
                header.style.paddingBottom = '15px';
                header.style.borderBottom = '2px solid #00A95C';
                header.innerHTML = '<h2 style="color: #00A95C; margin-bottom: 10px;">' + reportTitle + '</h2>' +
                                   '<p style="font-size: 14px; color: #666; margin: 5px 0;">Rapport d\\'assainissement - Direction de l\\'Assainissement</p>' +
                                   '<p style="font-size: 12px; color: #888; margin: 5px 0;">' + date + '</p>' +
                                   (currentFilter !== 'Aucun filtre' ? '<p style="font-size: 12px; color: #555; margin: 5px 0;"><strong>Filtre :</strong> ' + currentFilter + '</p>' : '');
                
                pdfContent.insertBefore(header, pdfContent.firstChild);
                
                // CORRECTION SUPPLEMENTAIRE : Forcer les tailles de police dans le PDF
                const headings = pdfContent.querySelectorAll('h4, h5, h6');
                headings.forEach(heading => {
                    if (heading.tagName === 'H4') {
                        heading.style.fontSize = '16pt';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H5') {
                        heading.style.fontSize = '14pt';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H6') {
                        heading.style.fontSize = '13pt';
                        heading.style.fontWeight = 'bold';
                    }
                });
                
                // Ajouter le contenu au div temporaire
                tempDiv.appendChild(pdfContent);
                document.body.appendChild(tempDiv);
                
                // Attendre un peu pour le rendu
                await new Promise(resolve => setTimeout(resolve, 500));
                
                // Convertir le contenu en canvas
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
                
                const imgWidth = pageWidth - 20; // Marge de 10mm de chaque côté
                const imgHeight = (canvas.height * imgWidth) / canvas.width;
                
                let position = 10;
                let remainingHeight = imgHeight;
                
                // Ajouter l'image au PDF (avec pagination si nécessaire)
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
                                '_' + 
                                Math.floor(Math.random() * 1000) + 
                                '.pdf';
                
                pdf.save(fileName);
                
            } catch (error) {
                console.error('Erreur lors de la génération du PDF:', error);
                alert('Erreur lors de la génération du PDF. Veuillez réessayer ou utiliser l\\'impression standard.');
            } finally {
                // Restaurer le bouton
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
        
        // Fonction pour imprimer depuis la fenêtre d'impression
        function printFromPrintWindow() {
            window.print();
        }
        
        // Fonction pour fermer la fenêtre
        function closePrintWindow() {
            window.close();
        }
        
        // Méthode de secours simple pour le PDF
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
                
                // En-tête
                pdf.setFontSize(20);
                pdf.setTextColor(0, 169, 92);
                pdf.text(reportTitle, pageWidth / 2, 20, { align: 'center' });
                
                pdf.setFontSize(20);
                pdf.setTextColor(100, 100, 100);
                pdf.text('Rapport d\\'assainissement - Direction de l\\'Assainissement', pageWidth / 2, 30, { align: 'center' });
                pdf.text(date, pageWidth / 2, 35, { align: 'center' });
                
                if (currentFilter !== 'Aucun filtre') {
                    pdf.text('Filtre: ' + currentFilter, pageWidth / 2, 40, { align: 'center' });
                }
                
                // Ajouter un message d'information
                pdf.setFontSize(20);
                pdf.setTextColor(0, 0, 0);
                pdf.text('Pour le rapport complet avec graphiques et cartes,', 20, 60);
                pdf.text('veuillez utiliser la fonction d\\'impression du navigateur.', 20, 70);
                
                // Pied de page
                pdf.setFontSize(20);
                pdf.setTextColor(128, 128, 128);
                pdf.text('Document généré par le Système PIAM', pageWidth / 2, pageHeight - 10, { align: 'center' });
                pdf.text('Direction de l\\'Assainissement - Ministère de l\\'Hydraulique et de l\\'Assainissement', pageWidth / 2, pageHeight - 5, { align: 'center' });
                
                // Télécharger
                const fileName = 'rapport_assainissement_simple_' + new Date().toISOString().slice(0, 10) + '.pdf';
                pdf.save(fileName);
            } catch (error) {
                alert('Impossible de générer le PDF simple.');
            }
        }
        
        // Initialisation
        window.onload = function() {
            // Ajouter des sauts de page avant les sections principales
            const sections = document.querySelectorAll('h5');
            sections.forEach((section, index) => {
                if (index > 0) {
                    const pageBreak = document.createElement('div');
                    pageBreak.className = 'page-break';
                    pageBreak.style.height = '1px';
                    section.parentNode.insertBefore(pageBreak, section);
                }
            });
            
            // Attacher les événements aux boutons
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
        footer.style.fontSize = '20px';
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
            pdf.setFontSize(20);
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
        table.style.fontSize = '20px';
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
        container.style.fontSize = '20px';
        container.style.padding = '10px';
        container.style.margin = '15px 0';
    });
    
    // Optimiser les titres
    const h5 = element.querySelectorAll('h5');
    h5.forEach(title => {
        title.style.fontSize = '20px';
        title.style.padding = '8px';
        title.style.margin = '20px 0 10px 0';
    });
    
    const h6 = element.querySelectorAll('h6');
    h6.forEach(title => {
        title.style.fontSize = '22px';
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
        Chart.register(ChartDataLabels);

        function safeNumber(value) {
            const number = parseFloat(value);
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
            return formatTaux(safePercentValue(numerator, denominator, decimals), decimals);
        }

        function formatTaux(number, decimals = 1) {
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
        }
        
        function formatNumberReport(number, decimals = 0) {
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
        }

        function formatNumberReport2(number, decimals = 0) {
            
            
            if (number === undefined || number === null || number === '') {
                
                return 0;
            }
            
            const num = parseFloat(number);
            
            
            if (isNaN(num)) {
                
                return 0;
            }
            
            const dec = parseInt(decimals) || 0;
            
            
            const result = new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: dec,
                maximumFractionDigits: dec
            }).format(num);
            
            
            return result;
        }
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.wilaya').select2()
            $('.moughata').select2()
            $('.commune').select2()

            $('.type').select2()

        })

        var chart_1 = null;
        var chart_2 = null;
        var chart_3 = null;
        var chart_4 = null;
        var chart_5 = null;
        var chart_6 = null;
        var chart_7 = null;
        var chart_8 = null;
        var chart_9 = null;
        var chart_10 = null;

        var chart10 = {!! json_encode($report['santes']['access_hygiene']) !!};



        var chart10Labels = [@json(__('tb.nbr_menages_dlm_complet')), @json(__('tb.nbr_menages_dlm_incomplet')),
            @json(__('tb.nbr_menages_sans_dlm'))
        ];


        var chart10Data = [chart10.nbr_menages_dlm_complet, chart10.nbr_menages_dlm_incomplet, chart10
        .nbr_menages_sans_dlm];

        var chart10ChartCanvas = $('#chart10').get(0).getContext('2d')
        var chart10 = {
            labels: chart10Labels,
            datasets: [{
                data: chart10Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }
        var chart10Options = {
            maintainAspectRatio: false,
            responsive: true,
        }
        chart_10 = new Chart(chart10ChartCanvas, {
            type: 'doughnut',
            data: chart10,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })
        var nb_sante_besoin_latr_satisf = {!! json_encode($report['santes']['besoin_assainissement']['nb_sante_besoin_latr_satisf']) !!};
        var nb_sante_besoin_latr_non_satisf = {!! json_encode($report['santes']['besoin_assainissement']['nb_sante_besoin_latr_non_satisf']) !!};
        var nb_sante_sans_latr = {!! json_encode($report['santes']['besoin_assainissement']['nb_sante_sans_latr']) !!};
        var chart9Labels = [@json(__('tb.nb_sante_besoin_latr_satisf')), @json(__('tb.nb_sante_besoin_latr_non_satisf')), @json(__('tb.nb_sante_sans_latr'))];
        var chart9Data = [nb_sante_besoin_latr_satisf, nb_sante_besoin_latr_non_satisf, nb_sante_sans_latr];

        var chart9ChartCanvas = $('#chart9').get(0).getContext('2d')
        var chart9 = {
            labels: chart9Labels,
            datasets: [{
                data: chart9Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }

        chart_9 = new Chart(chart9ChartCanvas, {
            type: 'doughnut',
            data: chart9,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })


        var chart8 = {!! json_encode($report['santes']['approv_eau']) !!};
        var chart8Labels = [];
        var chart8Data = [];

        $.each(chart8, function(key, value) {
            chart8Labels.push(value.approv_eau);
            chart8Data.push(value.infrastructures);
        })

        var chart8ChartCanvas = $('#chart8').get(0).getContext('2d')
        var chart8 = {
            labels: chart8Labels,
            datasets: [{
                data: chart8Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }

        chart_8 = new Chart(chart8ChartCanvas, {
            type: 'doughnut',
            data: chart8,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })

        var chart7 = {!! json_encode($report['ecoles']['access_hygiene']) !!};



        var chart7Labels = [@json(__('tb.nbr_menages_dlm_complet')), @json(__('tb.nbr_menages_dlm_incomplet')),
            @json(__('tb.nbr_menages_sans_dlm'))
        ];


        var chart7Data = [chart7.nbr_menages_dlm_complet, chart7.nbr_menages_dlm_incomplet, chart7.nbr_menages_sans_dlm];

        var chart7ChartCanvas = $('#chart7').get(0).getContext('2d')
        var chart7 = {
            labels: chart7Labels,
            datasets: [{
                data: chart7Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }
        var chart7Options = {
            maintainAspectRatio: false,
            responsive: true,
        }
        chart_7 = new Chart(chart7ChartCanvas, {
            type: 'doughnut',
            data: chart7,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {
                            let val = value;
                            return val;
                        }
                    }
                }
            }
        })

        var nb_ecole_besoin_latr_satisf = {!! json_encode($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_satisf']) !!};
        var nb_ecole_besoin_latr_non_satisf = {!! json_encode($report['ecoles']['besoin_assainissement']['nb_ecole_besoin_latr_non_satisf']) !!};
        var nb_ecole_sans_latr = {!! json_encode($report['ecoles']['besoin_assainissement']['nb_ecole_sans_latr']) !!};
        var chart6Labels = [@json(__('tb.nb_ecole_besoin_latr_satisf')), @json(__('tb.nb_ecole_besoin_latr_non_satisf')), @json(__('tb.nb_sante_sans_latr'))];
        var chart6Data = [nb_ecole_besoin_latr_satisf, nb_ecole_besoin_latr_non_satisf, nb_ecole_sans_latr];

        var chart6ChartCanvas = $('#chart6').get(0).getContext('2d')
        var chart6 = {
            labels: chart6Labels,
            datasets: [{
                data: chart6Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }

        chart_6 = new Chart(chart6ChartCanvas, {
            type: 'doughnut',
            data: chart6,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })

        var chart5 = {!! json_encode($report['ecoles']['approv_eau']) !!};
        var chart5Labels = [];
        var chart5Data = [];

        $.each(chart5, function(key, value) {
            chart5Labels.push(value.approv_eau);
            chart5Data.push(value.infrastructures);
        })

        var chart5ChartCanvas = $('#chart5').get(0).getContext('2d')
        var chart5 = {
            labels: chart5Labels,
            datasets: [{
                data: chart5Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }

        chart_5 = new Chart(chart5ChartCanvas, {
            type: 'doughnut',
            data: chart5,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })



        var chart4 = {!! json_encode($report['charts']['approv_eau']) !!};
        var chart4Labels = [];
        var chart4Data = [];

        $.each(chart4, function(key, value) {
            chart4Labels.push(value.approv_eau);
            chart4Data.push(value.localites);
        })

        var chart4ChartCanvas = $('#chart4').get(0).getContext('2d')
        var chart4 = {
            labels: chart4Labels,
            datasets: [{
                data: chart4Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }

        chart_4 = new Chart(chart4ChartCanvas, {
            type: 'doughnut',
            data: chart4,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })
        var chart3 = {!! json_encode($report['charts']['access_hygiene']) !!};



        var chart3Labels = [@json(__('tb.nbr_menages_dlm_complet')), @json(__('tb.nbr_menages_dlm_incomplet')),
            @json(__('tb.nbr_menages_sans_dlm'))
        ];

        var chart3Data = [chart3.nbr_menages_dlm_complet, chart3.nbr_menages_dlm_incomplet, chart3.nbr_menages_sans_dlm];

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
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {

                            return value;
                        }
                    }
                }
            }
        })

        var c_menages_EDL_max = {!! json_encode($report['menages_EDL_max']) !!};
        
        var securise = {!! json_encode($report['access_assainisement_familial']['securise']) !!};

        var base = {!! json_encode($report['access_assainisement_familial']['base']) !!};
        var non_ameliores = {!! json_encode($report['access_assainisement_familial']['non_ameliores']) !!};
        var men_dal = {!! json_encode($report['access_assainisement_familial']['men_dal']) !!};
        var menages = {!! json_encode($report['access_assainisement_familial']['menages']) !!};
        var pm_menages = {!! json_encode($report['access_assainisement_familial']['pm']['menages']) !!};

        var taux_securise = safePercentValue(securise, c_menages_EDL_max);
        var taux_base = safePercentValue(base, c_menages_EDL_max);
        var taux_non_ameliores = safePercentValue(non_ameliores, c_menages_EDL_max);
        var taux_men_dal = safePercentValue(men_dal, pm_menages);
        var taux_limite = Math.max(0, 100 - (safeNumber(taux_base) + safeNumber(taux_non_ameliores) + safeNumber(taux_men_dal))).toFixed(1);

        var chart2Labels = [@json(__('tb.securise')), @json(__('tb.limite')),
            @json(__('tb.base')),
            @json(__('tb.non_ameliores')), @json(__('tb.men_dal'))
        ];
        var chart2Data = [taux_securise, taux_limite, taux_base, taux_non_ameliores, taux_men_dal];


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
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {
                            let val = value + '%';
                            return val;
                        }
                    }
                }
            }
        })
        var chart1 = {!! json_encode($report['charts']['nb_loc']) !!};

        var labels = [];
        var data1 = [];
        var data2 = [];
        $.each(chart1, function(key, value) {
            labels.push(value.year);
            data1.push(value.localites_1);
            data2.push(value.localites_3);
        })
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
        var chart1Data = $.extend(true, {}, chart1)

        var chart1Options = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false
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
                        display: function(context) {
                            return context.dataset.data[context.dataIndex] > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {
                            return value;
                        }
                    }
                }
            }
        })
        
                var chart0Labels = [@json(__('tb.nb_loc_declench')), @json(__('tb.nb_loc_redeclench')),
            @json(__('tb.nb_loc_jamais_declench'))
        ];
        var villages_declenches = {!! json_encode($report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod'] + $report['access_assainisement_familial']['mise_atcp']['villages_declenches_noncod']) !!};
        var villages_redeclenches = {!! json_encode($report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_cod'] + $report['access_assainisement_familial']['mise_atcp']['villages_redeclenches_noncod']) !!};
        var villages_jamais_declench = {!! json_encode($report['donnees_generales']['nb_localites_codifiees'] - $report['access_assainisement_familial']['mise_atcp']['villages_declenches_cod']) !!};
        var chart0Data = [villages_declenches,villages_redeclenches,villages_jamais_declench];
        
        var chart0ChartCanvas = $('#chart0').get(0).getContext('2d')
        var chart0 = {
            labels: chart0Labels,
            datasets: [{
                data: chart0Data,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
            }]
        }
        var chart0Options = {
            maintainAspectRatio: false,
            responsive: true,
        }
        chart_0 = new Chart(chart0ChartCanvas, {
            type: 'doughnut',
            data: chart0,
            options: {
                plugins: {
                    datalabels: {
                        backgroundColor: function(context) {
                            return context.dataset.backgroundColor;
                        },
                        borderColor: 'white',
                        borderRadius: 25,
                        borderWidth: 2,
                        color: 'white',
                        display: function(context) {
                            var dataset = context.dataset;
                            var count = dataset.data.length;
                            var value = dataset.data[context.dataIndex];
                            return value > 0;
                        },
                        font: {
                            weight: 'bold',
                        },
                        formatter: function(value, context) {
                            let val = value;
                            return val;
                        }
                    }
                }
            }
        })
        $(document).ready(function() {
            $('#type').on('change', function() {

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
            $('#wilaya').on('change', function() {

                $('#type').val(null).trigger('change');
                var idWilaya = this.value;

                $("#moughata").html('');

                $.ajax({

                    url: "{{ url('admin/reporting/fetch_reporting_wilaya') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(result) {

                        var chart10 = result.santes.access_hygiene;
                        var chart10Labels_wilaya = [@json(__('tb.nbr_menages_dlm_complet')),
                            @json(__('tb.nbr_menages_dlm_incomplet')),
                            @json(__('tb.nbr_menages_sans_dlm'))
                        ];

                        var chart10Data_wilaya = [chart10.nbr_menages_dlm_complet,
                            chart10.nbr_menages_dlm_incomplet, chart10.nbr_menages_sans_dlm
                        ];

                        if (chart_10) {
                            chart_10.data.labels = chart10Labels_wilaya;
                            chart_10.data.datasets[0].data = chart10Data_wilaya;

                            chart_10.update();
                        }
                        var nb_sante_besoin_latr_satisf_wilaya = result.santes
                            .besoin_assainissement.nb_sante_besoin_latr_satisf;
                        var nb_sante_besoin_latr_non_satisf_wilaya = result.santes
                            .besoin_assainissement.nb_sante_besoin_latr_non_satisf;
                        var nb_sante_sans_latr_wilaya = result.santes.besoin_assainissement
                            .nb_sante_sans_latr;

                        var chart9Labels_wilaya = [@json(__('tb.nb_sante_besoin_latr_satisf')),
                            @json(__('tb.nb_sante_besoin_latr_non_satisf')),
                            @json(__('tb.nb_sante_sans_latr'))
                        ];
                        var chart9Data_wilaya = [nb_sante_besoin_latr_satisf_wilaya,
                            nb_sante_besoin_latr_non_satisf_wilaya,
                            nb_sante_sans_latr_wilaya
                        ];

                        if (chart_9) {
                            chart_9.data.labels = chart9Labels_wilaya;
                            chart_9.data.datasets[0].data = chart9Data_wilaya;

                            chart_9.update();
                        }

                        var chart8 = result.santes.approv_eau;

                        var chart8Labels_wilaya = [];
                        var chart8Data_wilaya = [];
                        $.each(chart8, function(key, value) {
                            chart8Labels_wilaya.push(value.approv_eau);
                            chart8Data_wilaya.push(value.infrastructures);
                        })
                        if (chart_8) {
                            chart_8.data.labels = chart8Labels_wilaya;
                            chart_8.data.datasets[0].data = chart8Data_wilaya;

                            chart_8.update();
                        }
                        var chart7 = result.ecoles.access_hygiene;
                        var chart7Labels_wilaya = [@json(__('tb.nbr_menages_dlm_complet')),
                            @json(__('tb.nbr_menages_dlm_incomplet')),
                            @json(__('tb.nbr_menages_sans_dlm'))
                        ];

                        var chart7Data_wilaya = [chart7.nbr_menages_dlm_complet,
                            chart7.nbr_menages_dlm_incomplet, chart7.nbr_menages_sans_dlm
                        ];

                        if (chart_7) {
                            chart_7.data.labels = chart7Labels_wilaya;
                            chart_7.data.datasets[0].data = chart7Data_wilaya;

                            chart_7.update();
                        }
                        var nb_ecole_besoin_latr_satisf_wilaya = result.ecoles
                            .besoin_assainissement.nb_ecole_besoin_latr_satisf;
                        var nb_ecole_besoin_latr_non_satisf_wilaya = result.ecoles
                            .besoin_assainissement.nb_ecole_besoin_latr_non_satisf;
                        var nb_ecole_sans_latr_wilaya = result.ecoles.besoin_assainissement
                            .nb_ecole_sans_latr;

                        var chart6Labels_wilaya = [@json(__('tb.nb_ecole_besoin_latr_satisf')),
                            @json(__('tb.nb_ecole_besoin_latr_non_satisf')),
                            @json(__('tb.nb_ecole_sans_latr'))
                        ];
                        var chart6Data_wilaya = [nb_ecole_besoin_latr_satisf_wilaya,
                            nb_ecole_besoin_latr_non_satisf_wilaya,
                            nb_ecole_sans_latr_wilaya
                        ];

                        if (chart_6) {
                            chart_6.data.labels = chart6Labels_wilaya;
                            chart_6.data.datasets[0].data = chart6Data_wilaya;

                            chart_6.update();
                        }

                        var chart5 = result.ecoles.approv_eau;

                        var chart5Labels_wilaya = [];
                        var chart5Data_wilaya = [];
                        $.each(chart5, function(key, value) {
                            chart5Labels_wilaya.push(value.approv_eau);
                            chart5Data_wilaya.push(value.infrastructures);
                        })
                        if (chart_5) {
                            chart_5.data.labels = chart5Labels_wilaya;
                            chart_5.data.datasets[0].data = chart5Data_wilaya;

                            chart_5.update();
                        }
                        var chart4 = result.charts.approv_eau;
                        var chart4Labels_wilaya = [];
                        var chart4Data_wilaya = [];

                        $.each(chart4, function(key, value) {
                            chart4Labels_wilaya.push(value.approv_eau);
                            chart4Data_wilaya.push(value.localites);
                        })
                        if (chart_4) {
                            chart_4.data.labels = chart4Labels_wilaya;
                            chart_4.data.datasets[0].data = chart4Data_wilaya;

                            chart_4.update();
                        }

                        var chart3Labels_wilaya = result.charts.access_hygiene;
                        var chart3Labels = [@json(__('tb.nbr_menages_dlm_complet')),
                            @json(__('tb.nbr_menages_dlm_incomplet')),
                            @json(__('tb.nbr_menages_sans_dlm'))
                        ];

                        var chart3Data_wilaya = [chart3.nbr_menages_dlm_complet,
                            chart3.nbr_menages_dlm_incomplet, chart3.nbr_menages_sans_dlm
                        ];

                        if (chart_3) {
                            chart_3.data.labels = chart3Labels_wilaya;
                            chart_3.data.datasets[0].data = chart3Data_wilaya;

                            chart_3.update();
                        }

                        c_menages_EDL_max = result.menages_EDL_max;
                        var securise_charts = result.access_assainisement_familial.securise;

                        var base_charts = result.access_assainisement_familial.base;
                        var non_ameliores_charts = result.access_assainisement_familial.non_ameliores;
                        var men_dal_charts = result.access_assainisement_familial.men_dal;


                        var taux_securise = safePercentValue(securise_charts, result.access_assainisement_familial.menages);
                        var taux_base = safePercentValue(base_charts, result.access_assainisement_familial.menages);
                        var taux_non_ameliores = safePercentValue(non_ameliores_charts, result.access_assainisement_familial.menages);
                        var taux_men_dal = safePercentValue(men_dal_charts, result.access_assainisement_familial.pm.menages);
                        var taux_limite = Math.max(0, 100 - (safeNumber(taux_base) + safeNumber(taux_non_ameliores) + safeNumber(taux_men_dal))).toFixed(1);

                        var chart2Data_wilaya = [taux_securise, taux_limite, taux_base,
                            taux_non_ameliores, taux_men_dal
                        ];

                        var chart2Labels_wilaya = [@json(__('tb.securise')),
                            @json(__('tb.limite')),
                            @json(__('tb.base')),
                            @json(__('tb.non_ameliores')), @json(__('tb.men_dal'))
                        ];


                        if (chart_2) {
                            chart_2.data.labels = chart2Labels_wilaya;
                            chart_2.data.datasets[0].data = chart2Data_wilaya;

                            chart_2.update();
                        }
                        var chart1_wilaya = result.charts.nb_loc;
                        var c_nb_loc_declench = result.nb_loc_declench;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function(key, value) {
                            labels.push(value.year);
                            data1.push(safePercentValue(value.localites_1, c_nb_loc_declench));
                            data2.push(safePercentValue(value.localites_3, c_nb_loc_declench));
                        })

                        if (chart_1) {
                            chart_1.data.labels = labels;
                            chart_1.data.datasets[0].data = data1;
                            chart_1.data.datasets[0].label = @json(__('tb.op_declechement'));
                            chart_1.data.datasets[1].data = data2;
                            chart_1.data.datasets[1].label = @json(__('tb.op_fdal'));
                            chart_1.update();

                        }
                        
                        var chart0Data_wilaya = [result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod
                        ,result.access_assainisement_familial.mise_atcp.villages_redeclenches_cod + result.access_assainisement_familial.mise_atcp.villages_redeclenches_n,result.donnees_generales.nb_localites - (result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod)];

                        var chart0Labels_wilaya = [@json(__('tb.nb_loc_declench')), @json(__('tb.nb_loc_redeclench')),
                            @json(__('tb.nb_loc_jamais_declench'))
                        ];



                        if (chart_0) {
                            chart_0.data.labels = chart0Labels_wilaya;
                            chart_0.data.datasets[0].data = chart0Data_wilaya;

                            chart_0.update();
                        }


                        if (result.ecoles.count === 0) {
                            $("#ecoles").show();

                        } else {
                            $("#ecoles").show();
                        }
                        if (result.santes.count === 0) {
                            $("#santes").show();

                        } else {
                            $("#santes").show();
                        }

                        const intitule = document.getElementById("intitule");
                        if (intitule) {
                            intitule.textContent = result.donnees_generales
                                .wilaya;
                        }
                        const territoire = document.getElementById("territoire");
                        if (territoire) {
                            territoire.textContent = @json(__('Wilaya'));
                        }


                        const nbr_santes_dlm_complet = document.getElementById(
                            "nbr_santes_dlm_complet");
                        if (nbr_santes_dlm_complet) {
                            nbr_santes_dlm_complet.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nb_dlm_complet);
                        }
                        const nb_santes_dlm_incomplet = document.getElementById(
                            "nb_santes_dlm_incomplet");
                        if (nb_santes_dlm_incomplet) {
                            nb_santes_dlm_incomplet.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nb_dlm_incomplet);
                        }
                        const nb_santes_sans_dlm = document.getElementById(
                            "nb_santes_sans_dlm");
                        if (nb_santes_sans_dlm) {
                            nb_santes_sans_dlm.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nb_sans_dlm);
                        }


                        const nbr_santes_complet = document.getElementById(
                            "nbr_santes_complet");
                        if (nbr_santes_complet) {
                            nbr_santes_complet.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nbr_inf_complet);
                        }
                        const nbr_santes_incomplet = document.getElementById(
                            "nbr_santes_incomplet");
                        if (nbr_santes_incomplet) {
                            nbr_santes_incomplet.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nbr_inf_incomplet);
                        }
                        const nbr_santes_sans = document.getElementById(
                            "nbr_santes_sans");
                        if (nbr_santes_sans) {
                            nbr_santes_sans.textContent = formatNumberReport2(result
                                .santes
                                .access_hygiene.nbr_inf_sans);
                        }

                        const projet_sans_besoins_sante = document.getElementById(
                            "projet_sans_besoins_sante");
                        if (projet_sans_besoins_sante) {
                            projet_sans_besoins_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins);
                        }


                        const taux_projet_sans_besoins_sante = document.getElementById(
                            "taux_projet_sans_besoins_sante");
                        if (taux_projet_sans_besoins_sante) {
                            taux_projet_sans_besoins_sante.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }


                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr);
                        }
                        const nbr_cabine_a_const_sante = document.getElementById(
                            "nbr_cabine_a_const_sante");
                        if (nbr_cabine_a_const_sante) {
                            nbr_cabine_a_const_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_sante_sans_latr = document.getElementById(
                            "taux_nb_sante_sans_latr");
                        if (taux_nb_sante_sans_latr) {
                            taux_nb_sante_sans_latr.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const taux_nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_non_satisf");
                        if (taux_nb_sante_besoin_latr_non_satisf) {
                            taux_nb_sante_besoin_latr_non_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf *
                                100 /
                                result
                                .donnees_generales.nb_santes);
                        }
                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf);
                        }
                        const taux_nb_sante_besoin_latr_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_satisf");
                        if (taux_nb_sante_besoin_latr_satisf) {
                            taux_nb_sante_besoin_latr_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal_sante = document.getElementById(
                            "taux_nb_dlm_instal_sante");
                        if (taux_nb_dlm_instal_sante) {
                            taux_nb_dlm_instal_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_santes);
                        }
                        const nb_dlm_instal_sante = document.getElementById(
                            "nb_dlm_instal_sante");
                        if (nb_dlm_instal_sante) {
                            nb_dlm_instal_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr_sante = document.getElementById(
                            "taux_nbr_cabine_pmr_sante");
                        if (taux_nbr_cabine_pmr_sante) {
                            taux_nbr_cabine_pmr_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_pmr_sante = document.getElementById(
                            "nbr_cabine_pmr_sante");
                        if (nbr_cabine_pmr_sante) {
                            nbr_cabine_pmr_sante.textContent = formatNumberReport(result.santes
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg_sante = document.getElementById(
                            "taux_nbr_cabine_fem_hyg_sante");
                        if (taux_nbr_cabine_fem_hyg_sante) {
                            taux_nbr_cabine_fem_hyg_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_fem_hyg_sante = document.getElementById(
                            "nbr_cabine_fem_hyg_sante");
                        if (nbr_cabine_fem_hyg_sante) {
                            nbr_cabine_fem_hyg_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem_sante = document.getElementById(
                            "taux_dist_cab_hom_fem_sante");
                        if (taux_dist_cab_hom_fem_sante) {
                            taux_dist_cab_hom_fem_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const dist_cab_hom_fem_sante = document.getElementById(
                            "dist_cab_hom_fem_sante");
                        if (dist_cab_hom_fem_sante) {
                            dist_cab_hom_fem_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const couvert_sante_latr = document.getElementById(
                            "couvert_sante_latr");
                        if (couvert_sante_latr) {
                            couvert_sante_latr.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.couvert_sante_latr);
                        }

                        const taux_couvert_sante_latr = document.getElementById(
                            "taux_couvert_sante_latr");
                        if (taux_couvert_sante_latr) {
                            taux_couvert_sante_latr.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.couvert_sante_latr * 100 / result
                                .donnees_generales.nb_santes)
                        }


                        const nbr_ecoles_dlm_complet = document.getElementById(
                            "nbr_ecoles_dlm_complet");
                        if (nbr_ecoles_dlm_complet) {
                            nbr_ecoles_dlm_complet.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nb_dlm_complet);
                        }
                        const nb_ecoles_dlm_incomplet = document.getElementById(
                            "nb_ecoles_dlm_incomplet");
                        if (nb_ecoles_dlm_incomplet) {
                            nb_ecoles_dlm_incomplet.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nb_dlm_incomplet);
                        }
                        const nb_ecoles_sans_dlm = document.getElementById(
                            "nb_ecoles_sans_dlm");
                        if (nb_ecoles_sans_dlm) {
                            nb_ecoles_sans_dlm.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nb_sans_dlm);
                        }


                        const nbr_ecoles_complet = document.getElementById(
                            "nbr_ecoles_complet");
                        if (nbr_ecoles_complet) {
                            nbr_ecoles_complet.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nbr_inf_complet);
                        }
                        const nbr_ecoles_incomplet = document.getElementById(
                            "nbr_ecoles_incomplet");
                        if (nbr_ecoles_incomplet) {
                            nbr_ecoles_incomplet.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nbr_inf_incomplet);
                        }
                        const nbr_ecoles_sans = document.getElementById(
                            "nbr_ecoles_sans");
                        if (nbr_ecoles_sans) {
                            nbr_ecoles_sans.textContent = formatNumberReport2(result
                                .ecoles
                                .access_hygiene.nbr_inf_sans);
                        }

                        const projet_sans_besoins = document.getElementById(
                            "projet_sans_besoins");
                        if (projet_sans_besoins) {
                            projet_sans_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins);
                        }

                        const projet_besoins = document.getElementById(
                            "projet_besoins");
                        if (projet_besoins) {
                            projet_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_besoins);
                        }


                        const taux_projet_sans_besoins = document.getElementById(
                            "taux_projet_sans_besoins");
                        if (taux_projet_sans_besoins) {
                            taux_projet_sans_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const taux_projet_besoins = document.getElementById(
                            "taux_projet_besoins");
                        if (taux_projet_besoins) {
                            taux_projet_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr);
                        }
                        const nbr_cabine_a_const = document.getElementById(
                            "nbr_cabine_a_const");
                        if (nbr_cabine_a_const) {
                            nbr_cabine_a_const.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_ecole_sans_latr = document.getElementById(
                            "taux_nb_ecole_sans_latr");
                        if (taux_nb_ecole_sans_latr) {
                            taux_nb_ecole_sans_latr.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const taux_nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_non_satisf");
                        if (taux_nb_ecole_besoin_latr_non_satisf) {
                            taux_nb_ecole_besoin_latr_non_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf *
                                100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf);
                        }
                        const taux_nb_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_satisf");
                        if (taux_nb_ecole_besoin_latr_satisf) {
                            taux_nb_ecole_besoin_latr_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal = document.getElementById(
                            "taux_nb_dlm_instal");
                        if (taux_nb_dlm_instal) {
                            taux_nb_dlm_instal.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_dlm_instal = document.getElementById(
                            "nb_dlm_instal");
                        if (nb_dlm_instal) {
                            nb_dlm_instal.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr = document.getElementById(
                            "taux_nbr_cabine_pmr");
                        if (taux_nbr_cabine_pmr) {
                            taux_nbr_cabine_pmr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_pmr = document.getElementById(
                            "nbr_cabine_pmr");
                        if (nbr_cabine_pmr) {
                            nbr_cabine_pmr.textContent = formatNumberReport(result.ecoles
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg = document.getElementById(
                            "taux_nbr_cabine_fem_hyg");
                        if (taux_nbr_cabine_fem_hyg) {
                            taux_nbr_cabine_fem_hyg.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_fem_hyg = document.getElementById(
                            "nbr_cabine_fem_hyg");
                        if (nbr_cabine_fem_hyg) {
                            nbr_cabine_fem_hyg.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem = document.getElementById(
                            "taux_dist_cab_hom_fem");
                        if (taux_dist_cab_hom_fem) {
                            taux_dist_cab_hom_fem.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const dist_cab_hom_fem = document.getElementById(
                            "dist_cab_hom_fem");
                        if (dist_cab_hom_fem) {
                            dist_cab_hom_fem.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const ratio = document.getElementById(
                            "ratio");
                        if (ratio) {
                            ratio.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result.ecoles
                                .acces_assainissement.nbr_cabine_fonct_sum);
                        }

                        const effectif = document.getElementById(
                            "effectif");
                        if (effectif) {
                            effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif);
                        }


                        const taux_effectif = document.getElementById(
                            "taux_effectif");
                        if (taux_effectif) {
                            taux_effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result
                                .donnees_generales.nb_ecoles);
                        }

                        const couvert_ecole_latr = document.getElementById(
                            "couvert_ecole_latr");
                        if (couvert_ecole_latr) {
                            couvert_ecole_latr.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr);
                        }

                        const taux_couvert_ecole_latr = document.getElementById(
                            "taux_couvert_ecole_latr");
                        if (taux_couvert_ecole_latr) {
                            taux_couvert_ecole_latr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }


                        $('#projets_perspectives_santes').html('');
                        $.each(result.santes.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_santes').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_program) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
                                </tr>`
                            );
                        })
                        $('#projets_perspectives_ecoles').html('');
                        $.each(result.ecoles.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_ecoles').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_program) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
                                </tr>`
                            );
                        })


                        $('#projets_passes_santes').html('');
                        $.each(result.santes.projets_passees, function(key,
                            value) {
                            $('#projets_passes_santes').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_recept) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                </tr>`
                            );
                        })
                        $('#projets_passes_ecoles').html('');

                        $.each(result.ecoles.projets_passees, function(key,
                            value) {
                            $('#projets_passes_ecoles').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_recept) + `</td>
                                    
                                    <td>` + value.infrastructures + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                </tr>`
                            );
                        })

                        $('#approv_eau_santes').html('');
                        if(result.santes.approv_eau.length == 0){
                            $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                            );
                        } else{

                            $.each(result.santes.approv_eau, function(key,
                                value) {
                                $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 / result.donnees_generales
                                        .nb_santes) + `</td>
                                    </tr>`
                                );
                            })
                        }
                        $('#approv_eau_ecoles').html('');
                        if(result.ecoles.approv_eau.length == 0){
                            $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{

                            $.each(result.ecoles.approv_eau, function(key,
                                value) {
                                $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 /
                                        result.donnees_generales.nb_ecoles) + `</td>
                                    </tr>`
                                );
                            })
                        }
                        const nb_ecoles = document.getElementById("nb_ecoles");
                        if (nb_ecoles) {
                            nb_ecoles.textContent = formatNumberReport(result.donnees_generales
                                .nb_ecoles);
                        }
                        const nb_santes = document.getElementById("nb_santes");
                        if (nb_santes) {
                            nb_santes.textContent = formatNumberReport(result.donnees_generales
                                .nb_santes);
                        }

                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = formatNumberReport(result.donnees_generales
                                .nb_eleves);
                        }


                        const nb_wilayas = document.getElementById("nb_wilayas");
                        if (nb_wilayas) {
                            nb_wilayas.textContent = formatNumberReport(result.donnees_generales
                                .nb_wilayas);
                        }


                        const nbr_sans_dlm = document.getElementById("nbr_sans_dlm");
                        if (nbr_sans_dlm) {
                            nbr_sans_dlm.textContent = formatNumberReport2(result.access_hygiene
                                .nbr_sans_dlm);
                        }

                        const nbr_localites_incomplet = document.getElementById(
                            "nbr_localites_incomplet");
                        if (nbr_localites_incomplet) {
                            nbr_localites_incomplet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_incomplet);
                        }
                        const nbr_localites_complet = document.getElementById(
                            "nbr_localites_complet");
                        if (nbr_localites_complet) {
                            nbr_localites_complet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_complet);
                        }
                        const nbr_localites = document.getElementById("nbr_localites");
                        if (nbr_localites) {
                            nbr_localites.textContent = formatNumberReport(result.access_hygiene
                                .nbr_localites);
                        }

                        const nbr_menages_sans_dlm = document.getElementById(
                            "nbr_menages_sans_dlm");
                        if (nbr_menages_sans_dlm) {
                            nbr_menages_sans_dlm.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_sans_dlm);
                        }




                        const nbr_menages_dlm_incomplet = document.getElementById(
                            "nbr_menages_dlm_incomplet");
                        if (nbr_menages_dlm_incomplet) {
                            nbr_menages_dlm_incomplet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_incomplet);
                        }

                        const nbr_menages_dlm_complet = document.getElementById(
                            "nbr_menages_dlm_complet");
                        if (nbr_menages_dlm_complet) {
                            nbr_menages_dlm_complet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_complet);
                        }



                        $('#financement').html('');
                        $('#financement').append(
                            `<tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(
                                result
                                .access_assainisement_familial
                                .auto_finance) +
                            `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latrine_autofin) +
                            `</td></tr>
                            <tr><td>Nombre de ménages pour lesquels les données d'investissement ont été recueillies</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latr_invest_men_dispo) +
                            `</td></tr>
                            <tr><td>Montant total investi par les ménages dans leurs latrines</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .investi) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
                            <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .investi / result
                                .access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
                            <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_entrainde) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_entrainde) +
                            `</td></tr>
                            
                            <tr><td>Taux de latrines subventionnées</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_subv) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_subv) +
                            `</td></tr>`
                        );

                        $('#resultas').html('');
                        $('#resultas').append(
                            `<tr>
                            <td>Nombre de latrines familiales avant mise en œuvre de l'ATPC</td>
                            <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_EDL) + `</td>
                            <td>
                                ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_EDL) + `
                            </td>

                            </tr>
                            <tr>
                                <td>Taux de couverture initial en latrines familiales avant mise en œuvre de l'ATPC</td>
                                <td>` + formatTaux(result
                                .access_assainisement_familial.couvertures_EDL * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `</td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines familiales lors du dernier suivi</td>
                                <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_dernier) + `</td>
                            <td>
                                ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_dernier) + `
                                
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de couverture final en latrines familiales</td>

                                <td>
                                    ` +
                            formatTaux(result
                                .access_assainisement_familial.couvertures_dernier * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de nouvelles latrines construites</td>
                                <td>
                                    ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nouv_latr_const) +
                            `</td>
                            <td>
                                ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nb_localites_nouv_latr_const
                            ) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines auto-construites</td>

                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_autoconst) +
                            `</td>
                                
                                
                                    <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_autoconst) +
                            `</td>
                            </tr>
                            <tr>
                                <td>Taux de latrines réalisées avec l'aide d'un maçon</td>

                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_realise_macon) +
                            `</td>
                                <td>
                                    
                                    ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_realise_macon) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines familiales en construction lors du dernier suivi</td>
                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_en_constr) +
                            `</td>
                                <td>
                                    
                                    ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_en_constr) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes</td>
                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_menages_ltr_end_phm) +
                            `</td>
                            <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_menages_ltr_end_phm) +
                            `</td>
                            </tr>

                            <tr>
                                <td>Nb de latrines familiales associées à un DLM fonctionnel</td>

                                <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_dlm) +
                            `</td>
                                <td>
                                    
                                    ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_localites) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines associées à un DLM fonctionnel</td>

                                <td>` +
                            formatTaux(result.access_assainisement_familial.nbr_menages_dlm
                                .nbr_dlm * 100 / result.access_assainisement_familial
                                .nbr_menages_dlm.nb_tot_latr_fam) +
                            `</td><td></td>
                            </tr>`
                        );


                        const securise = document.getElementById("securise");
                        if (securise) {
                            securise.textContent = formatTaux(result
                                .access_assainisement_familial
                                .securise * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }

                        const nb_localite_securise = document.getElementById(
                            "nb_localite_securise");
                        if (nb_localite_securise) {
                            nb_localite_securise.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_securise);
                        }
                        const base = document.getElementById("base");
                        if (base) {
                            base.textContent = formatTaux(result.access_assainisement_familial
                                .base * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }
                        const nb_localite_base = document.getElementById(
                            "nb_localite_base");
                        if (nb_localite_base) {
                            nb_localite_base.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_base);
                        }
                        const limite = document.getElementById("limite");
                        if (limite) {
                            limite.textContent = formatTaux(100 - ((result
                                .access_assainisement_familial
                                .base * 100 / result
                                .access_assainisement_familial
                                .menages) + (result
                                .access_assainisement_familial
                                .non_ameliores * 100 / result
                                .access_assainisement_familial
                                .menages) + (result
                                .access_assainisement_familial
                                .men_dal * 100 / result
                                .access_assainisement_familial
                                .pm.menages)));

                        }
                        const nb_localite_limite = document.getElementById(
                            "nb_localite_limite");
                        if (nb_localite_limite) {
                            nb_localite_limite.textContent = formatNumberReport(null);
                        }


                        const non_ameliores = document.getElementById("non_ameliores");
                        if (non_ameliores) {
                            non_ameliores.textContent = formatTaux(result
                                .access_assainisement_familial
                                .non_ameliores * 100 / result
                                .access_assainisement_familial
                                .menages);
                        }
                        const nb_localite_non_ameliores = document.getElementById(
                            "nb_localite_non_ameliores");
                        if (nb_localite_non_ameliores) {
                            nb_localite_non_ameliores.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_non_ameliores);
                        }
                        const men_dal = document.getElementById("men_dal");
                        if (men_dal) {
                            men_dal.textContent = formatTaux(result
                                .access_assainisement_familial
                                .men_dal * 100 / result.access_assainisement_familial
                                .pm.menages);
                        }
                        const nb_localite_men_dal = document.getElementById(
                            "nb_localite_men_dal");
                        if (nb_localite_men_dal) {
                            nb_localite_men_dal.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_men_dal);
                        }


                        $('#fdal').html('');
                        var cumul_decle = 0;
                        var cumul_certif = 0;
                        $.each(result.access_assainisement_familial.fdal, function(key, value) {
                            cumul_decle += parseInt(value.localites_1);
                            cumul_certif += parseInt(value.localites_3);
                            $('#fdal').append(
                                `<tr>
                                    <td>` + (value.year == null ? '' : value.year) + `</td>
                                    <td>` + formatNumberReport(value.localites_1) + `</td>
                                    <td>` + formatNumberReport(value.localites_3) + `</td>
                                </tr>`
                            );


                        })

                        $('#fdal').append(`<td>Total</td> <td>` + cumul_decle + `</td>
                                <td>` + cumul_certif + `</td>`);
                        const certification_taux = document.getElementById(
                            "certification_taux");
                        if (certification_taux) {
                            certification_taux.textContent = formatTaux(
                                cumul_certif * 100 / cumul_decle);
                        }
                        const certification_nbr = document.getElementById(
                            "certification_nbr");
                        if (certification_nbr) {
                            certification_nbr.textContent = formatNumberReport(
                                cumul_decle - cumul_certif);
                        }

                        $('#approv_eau').html('');
                        var approv_eau = 0;
                        $.each(result.access_assainisement_familial.approv_eau, function(key,
                            value) {
                            approv_eau += parseInt(value.localites);
                        });
                        if(result.access_assainisement_familial.approv_eau.length == 0){
                            $('#approv_eau').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{
                            $.each(result.access_assainisement_familial.approv_eau, function(key,
                                value) {
    
                                $('#approv_eau').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.localites) + `</td>
                                        <td>` + formatTaux(value.localites * 100 /
                                        approv_eau) + `</td>
                                    </tr>`
                                );
                            })
                        }



                        const pm_menages = document.getElementById("pm_menages");
                        if (pm_menages) {
                            pm_menages.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .menages);
                        }
                        const pm_population = document.getElementById("pm_population");
                        if (pm_population) {
                            pm_population.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .population);
                        }




                        const villages_appui_suivi = document.getElementById(
                            "villages_appui_suivi");
                        if (villages_appui_suivi) {
                            villages_appui_suivi.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi);
                        }
                        const taux_villages_appui_suivi = document.getElementById(
                            "taux_villages_appui_suivi");
                        if (taux_villages_appui_suivi) {
                            taux_villages_appui_suivi.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_redeclenches_noncod = document.getElementById(
                            "villages_redeclenches_noncod");
                        if (villages_redeclenches_noncod) {
                            villages_redeclenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod);
                        }

                        const taux_villages_redeclenches_noncod = document.getElementById(
                            "taux_villages_redeclenches_noncod");
                        if (taux_villages_redeclenches_noncod) {
                            taux_villages_redeclenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }
                        const villages_redeclenches_cod = document.getElementById(
                            "villages_redeclenches_cod");
                        if (villages_redeclenches_cod) {
                            villages_redeclenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod);
                        }
                        const taux_villages_redeclenches_cod = document.getElementById(
                            "taux_villages_redeclenches_cod");
                        if (taux_villages_redeclenches_cod) {
                            taux_villages_redeclenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }

                        const villages_declenches = document.getElementById(
                            "villages_declenches");
                        if (villages_declenches) {
                            villages_declenches.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod + result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches = document.getElementById(
                            "taux_villages_declenches");
                        if (taux_villages_declenches) {
                            taux_villages_declenches.textContent = formatTaux((result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_noncod + result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }



                        const villages_declenches_noncod = document.getElementById(
                            "villages_declenches_noncod");
                        if (villages_declenches_noncod) {
                            villages_declenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod);
                        }
                        //const taux_villages_declenches_noncod = document.getElementById(
                          //  "taux_villages_declenches_noncod");
                        /*if (taux_villages_declenches_noncod) {
                            taux_villages_declenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }*/

                        const villages_declenches_cod = document.getElementById(
                            "villages_declenches_cod");
                        if (villages_declenches_cod) {
                            villages_declenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches_cod = document.getElementById(
                            "taux_villages_declenches_cod");
                        if (taux_villages_declenches_cod) {
                            taux_villages_declenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_jamais_declenches = document.getElementById(
                            "villages_jamais_declenches");
                        if (villages_jamais_declenches) {
                            villages_jamais_declenches.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees - result
                                .access_assainisement_familial
                                .mise_atcp.villages_declenches_cod);
                        }
                        const taux_villages_jamais_declenches = document.getElementById(
                            "taux_villages_jamais_declenches");
                        if (taux_villages_jamais_declenches) {
                            taux_villages_jamais_declenches.textContent = formatTaux((
                                    result
                                    .donnees_generales.nb_localites_codifiees - result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }

                        $('#projets').html('');
                        $.each(result.access_assainisement_familial.projets, function(key,
                            value) {
                            $('#projets').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td>` + (value.year == null ? '' : value.year) + `</td><td>` + value.localites +
                                `</td></tr>`
                            );
                        })

                        const nb_moughatas = document.getElementById("nb_moughatas");
                        if (nb_moughatas) {
                            nb_moughatas.textContent = formatNumberReport(result
                                .donnees_generales.nb_moughatas);
                        }
                        const nb_communes = document.getElementById("nb_communes");
                        if (nb_communes) {
                            nb_communes.textContent = formatNumberReport(result
                                .donnees_generales.nb_communes);
                        }

                        const nb_localites = document.getElementById("nb_localites");
                        if (nb_localites) {
                            nb_localites.textContent = formatNumberReport(result
                                .donnees_generales.nb_localites);
                        }
                        const nb_localites_codifiees = document.getElementById(
                            "nb_localites_codifiees");
                        if (nb_localites_codifiees) {
                            nb_localites_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }

                        const nb_localites_non_codifiees = document.getElementById(
                            "nb_localites_non_codifiees");
                        if (nb_localites_non_codifiees) {
                            nb_localites_non_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_non_codifiees);
                        }



                        const population = document.getElementById("population");
                        if (population) {
                            population.textContent = formatNumberReport(result.donnees_generales
                                .population);
                        }
                        const rapport = document.getElementById("rapport");
                        if (rapport) {
                            rapport.textContent = 'Rapport de la Wilaya du ' + result
                                .donnees_generales.wilaya;
                        }

                    }

                });
                $.ajax({

                    url: "{{ url('admin/api/fetch-moughatas') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function(key, value) {
                            $("#moughata").append('<option value="' + value.id + '">' + (value.intitule_fr || value.intitule || value.id) + '</option>');
                        });



                    }

                });

            });
            $('#moughata').on('change', function() {
                $('#type').val(null).trigger('change');
                var idMoughata = this.value;
                var idWilaya = $("#wilaya").val();


                $("#commune").html('');
                $.ajax({

                    url: "{{ url('admin/reporting/fetch_reporting_moughata') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',
                    success: function(result) {
                        var chart4 = result.charts.approv_eau;
                        var chart4Labels_wilaya = [];
                        var chart4Data_wilaya = [];

                        $.each(chart4, function(key, value) {
                            chart4Labels_wilaya.push(value.approv_eau);
                            chart4Data_wilaya.push(value.localites);
                        })
                        if (chart_4) {
                            chart_4.data.labels = chart4Labels_wilaya;
                            chart_4.data.datasets[0].data = chart4Data_wilaya;

                            chart_4.update();
                        }
                        var chart3_wilaya = result.charts.access_hygiene;

                        var chart3Labels_wilaya = [@json(__('tb.nbr_menages_dlm_complet')),
                            @json(__('tb.nbr_menages_dlm_incomplet')),
                            @json(__('tb.nbr_menages_sans_dlm'))
                        ];
                        var chart3Data_wilaya = [chart3_wilaya.nbr_menages_dlm_complet,
                            chart3_wilaya
                            .nbr_menages_dlm_incomplet, chart3_wilaya
                            .nbr_menages_sans_dlm
                        ];




                        if (chart_3) {
                            chart_3.data.labels = chart3Labels_wilaya;
                            chart_3.data.datasets[0].data = chart3Data_wilaya;

                            chart_3.update();
                        }

                        var securise_charts = result.access_assainisement_familial.securise;
                        var base_charts = result.access_assainisement_familial.base;
                        var non_ameliores_charts = result.access_assainisement_familial.non_ameliores;
                        var men_dal_charts = result.access_assainisement_familial.men_dal;
                        var menages_m = safeNumber(result.access_assainisement_familial.menages) || 1;
                        var pm_menages_m = safeNumber(result.access_assainisement_familial.pm.menages) || 1;
                        var taux_securise_m = safePercentValue(securise_charts, menages_m);
                        var taux_base_m = safePercentValue(base_charts, menages_m);
                        var taux_non_ameliores_m = safePercentValue(non_ameliores_charts, menages_m);
                        var taux_men_dal_m = safePercentValue(men_dal_charts, pm_menages_m);
                        var limite_charts = Math.max(0, 100 - (safeNumber(taux_base_m) + safeNumber(taux_non_ameliores_m) + safeNumber(taux_men_dal_m))).toFixed(1);


                        var chart2Labels_wilaya = [@json(__('tb.securise')),
                            @json(__('tb.limite')), @json(__('tb.base')),
                            @json(__('tb.non_ameliores')), @json(__('tb.men_dal'))
                        ];
                        var chart2Data_wilaya = [taux_securise_m, limite_charts, taux_base_m,
                            taux_non_ameliores_m,
                            taux_men_dal_m
                        ];

                        if (chart_2) {
                            chart_2.data.labels = chart2Labels_wilaya;
                            chart_2.data.datasets[0].data = chart2Data_wilaya;

                            chart_2.update();
                        }
                        var chart1_wilaya = result.charts.nb_loc;
                        var c_nb_loc_declench = result.nb_loc_declench;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function(key, value) {
                            labels.push(value.year);
                            data1.push(safePercentValue(value.localites_1, c_nb_loc_declench));
                            data2.push(safePercentValue(value.localites_3, c_nb_loc_declench));
                        })

                        if (chart_1) {
                            chart_1.data.labels = labels;
                            chart_1.data.datasets[0].data = data1;
                            chart_1.data.datasets[0].label = @json(__('tb.op_declechement'));
                            chart_1.data.datasets[1].data = data2;
                            chart_1.data.datasets[1].label = @json(__('tb.op_fdal'));
                            chart_1.update();

                        }
                        var chart0Data_wilaya = [result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod
                        ,result.access_assainisement_familial.mise_atcp.villages_redeclenches_cod + result.access_assainisement_familial.mise_atcp.villages_redeclenches_n,result.donnees_generales.nb_localites - (result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod)];

                        var chart0Labels_wilaya = [@json(__('tb.nb_loc_declench')), @json(__('tb.nb_loc_redeclench')),
                            @json(__('tb.nb_loc_jamais_declench'))
                        ];



                        if (chart_0) {
                            chart_0.data.labels = chart0Labels_wilaya;
                            chart_0.data.datasets[0].data = chart0Data_wilaya;

                            chart_0.update();
                        }

                        if (result.ecoles.count === 0) {
                            $("#ecoles").show();

                        } else {
                            $("#ecoles").show();
                        }
                        if (result.santes.count === 0) {
                            $("#santes").show();

                        } else {
                            $("#santes").show();
                        }

                        const intitule = document.getElementById("intitule");
                        if (intitule) {
                            intitule.textContent = result.donnees_generales
                                .wilaya;
                        }
                        const territoire = document.getElementById("territoire");
                        if (territoire) {
                            territoire.textContent = @json(__('Moughata'));
                        }

                        const projet_sans_besoins_sante = document.getElementById(
                            "projet_sans_besoins_sante");
                        if (projet_sans_besoins_sante) {
                            projet_sans_besoins_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins);
                        }


                        const taux_projet_sans_besoins_sante = document.getElementById(
                            "taux_projet_sans_besoins_sante");
                        if (taux_projet_sans_besoins_sante) {
                            taux_projet_sans_besoins_sante.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }


                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr);
                        }
                        const nbr_cabine_a_const_sante = document.getElementById(
                            "nbr_cabine_a_const_sante");
                        if (nbr_cabine_a_const_sante) {
                            nbr_cabine_a_const_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_sante_sans_latr = document.getElementById(
                            "taux_nb_sante_sans_latr");
                        if (taux_nb_sante_sans_latr) {
                            taux_nb_sante_sans_latr.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const taux_nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_non_satisf");
                        if (taux_nb_sante_besoin_latr_non_satisf) {
                            taux_nb_sante_besoin_latr_non_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf *
                                100 /
                                result
                                .donnees_generales.nb_santes);
                        }
                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf);
                        }
                        const taux_nb_sante_besoin_latr_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_satisf");
                        if (taux_nb_sante_besoin_latr_satisf) {
                            taux_nb_sante_besoin_latr_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal_sante = document.getElementById(
                            "taux_nb_dlm_instal_sante");
                        if (taux_nb_dlm_instal_sante) {
                            taux_nb_dlm_instal_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_santes);
                        }
                        const nb_dlm_instal_sante = document.getElementById(
                            "nb_dlm_instal_sante");
                        if (nb_dlm_instal_sante) {
                            nb_dlm_instal_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr_sante = document.getElementById(
                            "taux_nbr_cabine_pmr_sante");
                        if (taux_nbr_cabine_pmr_sante) {
                            taux_nbr_cabine_pmr_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_pmr_sante = document.getElementById(
                            "nbr_cabine_pmr_sante");
                        if (nbr_cabine_pmr_sante) {
                            nbr_cabine_pmr_sante.textContent = formatNumberReport(result.santes
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg_sante = document.getElementById(
                            "taux_nbr_cabine_fem_hyg_sante");
                        if (taux_nbr_cabine_fem_hyg_sante) {
                            taux_nbr_cabine_fem_hyg_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_fem_hyg_sante = document.getElementById(
                            "nbr_cabine_fem_hyg_sante");
                        if (nbr_cabine_fem_hyg_sante) {
                            nbr_cabine_fem_hyg_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem_sante = document.getElementById(
                            "taux_dist_cab_hom_fem_sante");
                        if (taux_dist_cab_hom_fem_sante) {
                            taux_dist_cab_hom_fem_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const dist_cab_hom_fem_sante = document.getElementById(
                            "dist_cab_hom_fem_sante");
                        if (dist_cab_hom_fem_sante) {
                            dist_cab_hom_fem_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const couvert_sante_latr = document.getElementById(
                            "couvert_sante_latr");
                        if (couvert_sante_latr) {
                            couvert_sante_latr.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.couvert_sante_latr);
                        }

                        const taux_couvert_sante_latr = document.getElementById(
                            "taux_couvert_sante_latr");
                        if (taux_couvert_sante_latr) {
                            taux_couvert_sante_latr.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.couvert_sante_latr * 100 / result
                                .donnees_generales.nb_santes)
                        }


                        const projet_sans_besoins = document.getElementById(
                            "projet_sans_besoins");
                        if (projet_sans_besoins) {
                            projet_sans_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins);
                        }

                        const projet_besoins = document.getElementById(
                            "projet_besoins");
                        if (projet_besoins) {
                            projet_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_besoins);
                        }


                        const taux_projet_sans_besoins = document.getElementById(
                            "taux_projet_sans_besoins");
                        if (taux_projet_sans_besoins) {
                            taux_projet_sans_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const taux_projet_besoins = document.getElementById(
                            "taux_projet_besoins");
                        if (taux_projet_besoins) {
                            taux_projet_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr);
                        }
                        const nbr_cabine_a_const = document.getElementById(
                            "nbr_cabine_a_const");
                        if (nbr_cabine_a_const) {
                            nbr_cabine_a_const.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_ecole_sans_latr = document.getElementById(
                            "taux_nb_ecole_sans_latr");
                        if (taux_nb_ecole_sans_latr) {
                            taux_nb_ecole_sans_latr.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const taux_nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_non_satisf");
                        if (taux_nb_ecole_besoin_latr_non_satisf) {
                            taux_nb_ecole_besoin_latr_non_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf /
                                result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf);
                        }
                        const taux_nb_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_satisf");
                        if (taux_nb_ecole_besoin_latr_satisf) {
                            taux_nb_ecole_besoin_latr_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal = document.getElementById(
                            "taux_nb_dlm_instal");
                        if (taux_nb_dlm_instal) {
                            taux_nb_dlm_instal.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_dlm_instal = document.getElementById(
                            "nb_dlm_instal");
                        if (nb_dlm_instal) {
                            nb_dlm_instal.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr = document.getElementById(
                            "taux_nbr_cabine_pmr");
                        if (taux_nbr_cabine_pmr) {
                            taux_nbr_cabine_pmr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_pmr = document.getElementById(
                            "nbr_cabine_pmr");
                        if (nbr_cabine_pmr) {
                            nbr_cabine_pmr.textContent = formatNumberReport(result.ecoles
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg = document.getElementById(
                            "taux_nbr_cabine_fem_hyg");
                        if (taux_nbr_cabine_fem_hyg) {
                            taux_nbr_cabine_fem_hyg.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_fem_hyg = document.getElementById(
                            "nbr_cabine_fem_hyg");
                        if (nbr_cabine_fem_hyg) {
                            nbr_cabine_fem_hyg.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem = document.getElementById(
                            "taux_dist_cab_hom_fem");
                        if (taux_dist_cab_hom_fem) {
                            taux_dist_cab_hom_fem.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const dist_cab_hom_fem = document.getElementById(
                            "dist_cab_hom_fem");
                        if (dist_cab_hom_fem) {
                            dist_cab_hom_fem.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const ratio = document.getElementById(
                            "ratio");
                        if (ratio) {
                            ratio.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result.ecoles
                                .acces_assainissement.nbr_cabine_fonct_sum);
                        }

                        const effectif = document.getElementById(
                            "effectif");
                        if (effectif) {
                            effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif);
                        }


                        const taux_effectif = document.getElementById(
                            "taux_effectif");
                        if (taux_effectif) {
                            taux_effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result
                                .donnees_generales.nb_ecoles);
                        }

                        const couvert_ecole_latr = document.getElementById(
                            "couvert_ecole_latr");
                        if (couvert_ecole_latr) {
                            couvert_ecole_latr.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr);
                        }

                        const taux_couvert_ecole_latr = document.getElementById(
                            "taux_couvert_ecole_latr");
                        if (taux_couvert_ecole_latr) {
                            taux_couvert_ecole_latr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }


                        $('#projets_perspectives_santes').html('');
                        $.each(result.santes.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_santes').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_program) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
                                </tr>`
                            );
                        })
                        $('#projets_perspectives_ecoles').html('');
                        $.each(result.ecoles.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_ecoles').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_program) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
                                </tr>`
                            );
                        })


                        $('#projets_passes_santes').html('');
                        $.each(result.santes.projets_passees, function(key,
                            value) {
                            $('#projets_passes_santes').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_recept) + `</td>
                                    
                                    <td>` + formatNumberReport(value.infrastructures) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                </tr>`
                            );
                        })
                        $('#projets_passes_ecoles').html('');

                        $.each(result.ecoles.projets_passees, function(key,
                            value) {
                            $('#projets_passes_ecoles').append(
                                `<tr>
                                    <td>` + value.intitule_resume + `</td>
                                    <td>` + value.intitule + `</td>
                                    <td>` + formatNumberReport(value.an_recept) + `</td>
                                    
                                    <td>` + value.infrastructures + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
                                    <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                </tr>`
                            );
                        })

                        $('#approv_eau_santes').html('');
                        if(result.santes.approv_eau.length == 0){
                            $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                            );
                        } else{

                            $.each(result.santes.approv_eau, function(key,
                                value) {
                                $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 / result.donnees_generales
                                        .nb_santes) + `</td>
                                    </tr>`
                                );
                            })
                        }
                        $('#approv_eau_ecoles').html('');
                        if(result.ecoles.approv_eau.length == 0){
                            $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{

                            $.each(result.ecoles.approv_eau, function(key,
                                value) {
                                $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 /
                                        result.donnees_generales.nb_ecoles) + `</td>
                                    </tr>`
                                );
                            })
                        }

                        const nb_ecoles = document.getElementById("nb_ecoles");
                        if (nb_ecoles) {
                            nb_ecoles.textContent = formatNumberReport(result.donnees_generales
                                .nb_ecoles);
                        }
                        const nb_santes = document.getElementById("nb_santes");
                        if (nb_santes) {
                            nb_santes.textContent = formatNumberReport(result.donnees_generales
                                .nb_santes);
                        }

                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = formatNumberReport(result.donnees_generales
                                .nb_eleves);
                        }


                        const nb_wilayas = document.getElementById("nb_wilayas");
                        if (nb_wilayas) {
                            nb_wilayas.textContent = formatNumberReport(result.donnees_generales
                                .nb_wilayas);
                        }


                        const nbr_sans_dlm = document.getElementById("nbr_sans_dlm");
                        if (nbr_sans_dlm) {
                            nbr_sans_dlm.textContent = formatNumberReport2(result.access_hygiene
                                .nbr_sans_dlm);
                        }

                        const nbr_localites_incomplet = document.getElementById(
                            "nbr_localites_incomplet");
                        if (nbr_localites_incomplet) {
                            nbr_localites_incomplet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_incomplet);
                        }
                        const nbr_localites_complet = document.getElementById(
                            "nbr_localites_complet");
                        if (nbr_localites_complet) {
                            nbr_localites_complet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_complet);
                        }
                        const nbr_localites = document.getElementById("nbr_localites");
                        if (nbr_localites) {
                            nbr_localites.textContent = formatNumberReport(result.access_hygiene
                                .nbr_localites);
                        }

                        const nbr_menages_sans_dlm = document.getElementById(
                            "nbr_menages_sans_dlm");
                        if (nbr_menages_sans_dlm) {
                            nbr_menages_sans_dlm.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_sans_dlm);
                        }




                        const nbr_menages_dlm_incomplet = document.getElementById(
                            "nbr_menages_dlm_incomplet");
                        if (nbr_menages_dlm_incomplet) {
                            nbr_menages_dlm_incomplet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_incomplet);
                        }

                        const nbr_menages_dlm_complet = document.getElementById(
                            "nbr_menages_dlm_complet");
                        if (nbr_menages_dlm_complet) {
                            nbr_menages_dlm_complet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_complet);
                        }



                        $('#financement').html('');
                        $('#financement').append(
                            `<tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(
                                result
                                .access_assainisement_familial
                                .auto_finance) +
                            `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latrine_autofin) +
                            `</td></tr>
                            <tr><td>Nombre de ménages pour lesquels les données d'investissement ont été recueillies</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latr_invest_men_dispo) +
                            `</td></tr>
                            <tr><td>Montant total investi par les ménages dans leurs latrines</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .investi) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
                            <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .investi / result
                                .access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
                            <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_entrainde) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_entrainde) +
                            `</td></tr>
    
                            <tr><td>Taux de latrines subventionnées</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_subv) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_subv) +
                            `</td></tr>`
                        );

                        $('#resultas').html('');
                        $('#resultas').append(
                            `<tr>
                        <td>Nombre de latrines familiales avant mise en œuvre de l'ATPC</td>
                        <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_EDL) + `</td>
                        <td>
                            ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_EDL) + `
                        </td>

                        </tr>
                        <tr>
                        <td>Taux de couverture initial en latrines familiales avant mise en œuvre de l'ATPC</td>
                        <td>` + formatTaux(result
                                .access_assainisement_familial.couvertures_EDL * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `</td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                            <td>Nombre de latrines familiales lors du dernier suivi</td>
                            <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_dernier) + `</td>
                            <td>
                            ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_dernier) + `
        
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de couverture final en latrines familiales</td>

                                <td>
                                    ` +
                            formatTaux(result
                                .access_assainisement_familial.couvertures_dernier * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                        <td>Nombre de nouvelles latrines construites</td>
                        <td>
                            ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nouv_latr_const) +
                            `</td>
                            <td>
                                ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nb_localites_nouv_latr_const
                            ) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Taux de latrines auto-construites</td>

                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_autoconst) +
                            `</td>
        
        
                            <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_autoconst) +
                            `</td>
                            </tr>
                            <tr>
                                <td>Taux de latrines réalisées avec l'aide d'un maçon</td>

                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_realise_macon) +
                            `</td>
                            <td>
            
                            ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_realise_macon) +
                            `
                            </td>
                        </tr>
                        <tr>
                            <td>Nombre de latrines familiales en construction lors du dernier suivi</td>
                            <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_en_constr) +
                            `</td>
                                <td>
                                    
                                    ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_en_constr) +
                            `
                                </td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes</td>
                                <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_menages_ltr_end_phm) +
                            `</td>
                            <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_menages_ltr_end_phm) +
                            `</td>
                            </tr>

                            <tr>
                                <td>Nb de latrines familiales associées à un DLM fonctionnel</td>

                                <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_dlm) +
                            `</td>
        					<td>
            
                            ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_localites) +
                            `
                          </td>
                      </tr>
                      <tr>
                          <td>Taux de latrines associées à un DLM fonctionnel</td>

                          <td>` +
                            formatTaux(result.access_assainisement_familial.nbr_menages_dlm
                                .nbr_dlm * 100 / result.access_assainisement_familial
                                .nbr_menages_dlm.nb_tot_latr_fam) +
                            `</td><td></td>
   							 </tr>`
                        );


                        const securise = document.getElementById("securise");
                        if (securise) {
                            securise.textContent = formatTaux(result
                                .access_assainisement_familial
                                .securise * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }
                        const nb_localite_securise = document.getElementById(
                            "nb_localite_securise");
                        if (nb_localite_securise) {
                            nb_localite_securise.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_securise);
                        }
                        const base = document.getElementById("base");
                        if (base) {
                            base.textContent = formatTaux(result.access_assainisement_familial
                                .base * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }
                        const nb_localite_base = document.getElementById(
                            "nb_localite_base");
                        if (nb_localite_base) {
                            nb_localite_base.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_base);
                        }
                        const limite = document.getElementById("limite");
                        if (limite) {
                            limite.textContent = formatTaux(result.access_assainisement_familial
                                .limite * 100 / result.access_assainisement_familial
                                .menages_1);

                        }
                        const nb_localite_limite = document.getElementById(
                            "nb_localite_limite");
                        if (nb_localite_limite) {
                            nb_localite_limite.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_limite);
                        }

                        const non_ameliores = document.getElementById("non_ameliores");
                        if (non_ameliores) {
                            non_ameliores.textContent = formatTaux(result
                                .access_assainisement_familial
                                .non_ameliores * 100 / result
                                .access_assainisement_familial
                                .menages);
                        }
                        const nb_localite_non_ameliores = document.getElementById(
                            "nb_localite_non_ameliores");
                        if (nb_localite_non_ameliores) {
                            nb_localite_non_ameliores.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_non_ameliores);
                        }
                        const men_dal = document.getElementById("men_dal");
                        if (men_dal) {
                            men_dal.textContent = formatTaux(result
                                .access_assainisement_familial
                                .men_dal * 100 / result.access_assainisement_familial
                                .menages_1);
                        }
                        const nb_localite_men_dal = document.getElementById(
                            "nb_localite_men_dal");
                        if (nb_localite_men_dal) {
                            nb_localite_men_dal.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_men_dal);
                        }


                        $('#fdal').html('');
                        var cumul_decle = 0;
                        var cumul_certif = 0;
                        $.each(result.access_assainisement_familial.fdal, function(key, value) {
                            cumul_decle += parseInt(value.localites_1);
                            cumul_certif += parseInt(value.localites_3);
                            $('#fdal').append(
                                `<tr>
                                  <td>` + (value.year == null ? '' : value.year) + `</td>
                                  <td>` + formatNumberReport(value.localites_1) + `</td>
                                  <td>` + formatNumberReport(value.localites_3) + `</td>
                              </tr>`
                            );


                        })

                        $('#fdal').append(`<td>Total</td> <td>` + cumul_decle + `</td>
        				<td>` + cumul_certif + `</td>`);
                        const certification_taux = document.getElementById(
                            "certification_taux");
                        if (certification_taux) {
                            certification_taux.textContent = formatTaux(
                                cumul_certif * 100 / cumul_decle);
                        }
                        const certification_nbr = document.getElementById(
                            "certification_nbr");
                        if (certification_nbr) {
                            certification_nbr.textContent = formatNumberReport(
                                cumul_decle - cumul_certif);
                        }

                        $('#approv_eau').html('');
                        var approv_eau = 0;
                        $.each(result.access_assainisement_familial.approv_eau, function(key,
                            value) {
                            approv_eau += parseInt(value.localites);
                        });
                                                if(result.access_assainisement_familial.approv_eau.length == 0){
                            $('#approv_eau').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{
                            $.each(result.access_assainisement_familial.approv_eau, function(key,
                                value) {
    
                                $('#approv_eau').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.localites) + `</td>
                                        <td>` + formatTaux(value.localites * 100 /
                                        approv_eau) + `</td>
                                    </tr>`
                                );
                            })
                        }



                        const pm_menages = document.getElementById("pm_menages");
                        if (pm_menages) {
                            pm_menages.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .menages);
                        }
                        const pm_population = document.getElementById("pm_population");
                        if (pm_population) {
                            pm_population.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .population);
                        }




                        const villages_appui_suivi = document.getElementById(
                            "villages_appui_suivi");
                        if (villages_appui_suivi) {
                            villages_appui_suivi.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi);
                        }
                        const taux_villages_appui_suivi = document.getElementById(
                            "taux_villages_appui_suivi");
                        if (taux_villages_appui_suivi) {
                            taux_villages_appui_suivi.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_redeclenches_noncod = document.getElementById(
                            "villages_redeclenches_noncod");
                        if (villages_redeclenches_noncod) {
                            villages_redeclenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod);
                        }

                        /*const taux_villages_redeclenches_noncod = document.getElementById(
                            "taux_villages_redeclenches_noncod");
                        if (taux_villages_redeclenches_noncod) {
                            taux_villages_redeclenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }*/
                        const villages_redeclenches_cod = document.getElementById(
                            "villages_redeclenches_cod");
                        if (villages_redeclenches_cod) {
                            villages_redeclenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod);
                        }
                        const taux_villages_redeclenches_cod = document.getElementById(
                            "taux_villages_redeclenches_cod");
                        if (taux_villages_redeclenches_cod) {
                            taux_villages_redeclenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }

                        const villages_declenches = document.getElementById(
                            "villages_declenches");
                        if (villages_declenches) {
                            villages_declenches.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod + result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches = document.getElementById(
                            "taux_villages_declenches");
                        if (taux_villages_declenches) {
                            taux_villages_declenches.textContent = formatTaux((result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_noncod + result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }



                        const villages_declenches_noncod = document.getElementById(
                            "villages_declenches_noncod");
                        if (villages_declenches_noncod) {
                            villages_declenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod);
                        }
                        /*const taux_villages_declenches_noncod = document.getElementById(
                            "taux_villages_declenches_noncod");
                        if (taux_villages_declenches_noncod) {
                            taux_villages_declenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }*/

                        const villages_declenches_cod = document.getElementById(
                            "villages_declenches_cod");
                        if (villages_declenches_cod) {
                            villages_declenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches_cod = document.getElementById(
                            "taux_villages_declenches_cod");
                        if (taux_villages_declenches_cod) {
                            taux_villages_declenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_jamais_declenches = document.getElementById(
                            "villages_jamais_declenches");
                        if (villages_jamais_declenches) {
                            villages_jamais_declenches.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees - result
                                .access_assainisement_familial
                                .mise_atcp.villages_declenches_cod);
                        }
                        const taux_villages_jamais_declenches = document.getElementById(
                            "taux_villages_jamais_declenches");
                        if (taux_villages_jamais_declenches) {
                            taux_villages_jamais_declenches.textContent = formatTaux((
                                    result
                                    .donnees_generales.nb_localites_codifiees - result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }

                        $('#projets').html('');
                        $.each(result.access_assainisement_familial.projets, function(key,
                            value) {
                            $('#projets').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td>` + (value.year == null ? '' : value.year) + `</td><td>` + value.localites +
                                `</td></tr>`
                            );
                        })

                        const nb_moughatas = document.getElementById("nb_moughatas");
                        if (nb_moughatas) {
                            nb_moughatas.textContent = formatNumberReport(result
                                .donnees_generales.nb_moughatas);
                        }
                        const nb_communes = document.getElementById("nb_communes");
                        if (nb_communes) {
                            nb_communes.textContent = formatNumberReport(result
                                .donnees_generales.nb_communes);
                        }

                        const nb_localites = document.getElementById("nb_localites");
                        if (nb_localites) {
                            nb_localites.textContent = formatNumberReport(result
                                .donnees_generales.nb_localites);
                        }
                        const nb_localites_codifiees = document.getElementById(
                            "nb_localites_codifiees");
                        if (nb_localites_codifiees) {
                            nb_localites_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }

                        const nb_localites_non_codifiees = document.getElementById(
                            "nb_localites_non_codifiees");
                        if (nb_localites_non_codifiees) {
                            nb_localites_non_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_non_codifiees);
                        }



                        const population = document.getElementById("population");
                        if (population) {
                            population.textContent = formatNumberReport(result.donnees_generales
                                .population);
                        }

                        const rapport = document.getElementById("rapport");
                        if (rapport) {
                            rapport.textContent = 'Rapport de la moughata du ' + result
                                .donnees_generales.wilaya;
                        }

                    }

                });


                $.ajax({

                    url: "{{ url('admin/api/fetch-communes') }}",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function(key, value) {
                            $("#commune").append('<option value="' + value.id + '">' + (value.intitule_fr || value.intitule || value.id) + '</option>');
                        });

                    }

                });

            });


            $('#commune').on('change', function() {
                $('#type').val(null).trigger('change');
                var idMoughata = $("#moughata").val();
                var idWilaya = $("#wilaya").val();
                var idCommune = this.value;

                $.ajax({

                    url: "{{ url('admin/reporting/fetch_reporting_commune') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(result) {

                        var chart4 = result.charts.approv_eau;
                        var chart4Labels_wilaya = [];
                        var chart4Data_wilaya = [];

                        $.each(chart4, function(key, value) {
                            chart4Labels_wilaya.push(value.approv_eau);
                            chart4Data_wilaya.push(value.localites);
                        })
                        if (chart_4) {
                            chart_4.data.labels = chart4Labels_wilaya;
                            chart_4.data.datasets[0].data = chart4Data_wilaya;

                            chart_4.update();
                        }

                        var chart3_wilaya = result.charts.access_hygiene;

                        var chart3Labels_wilaya = [@json(__('tb.nbr_menages_dlm_complet')),
                            @json(__('tb.nbr_menages_dlm_incomplet')),
                            @json(__('tb.nbr_menages_sans_dlm'))
                        ];
                        var chart3Data_wilaya = [chart3_wilaya.nbr_menages_dlm_complet,
                            chart3_wilaya
                            .nbr_menages_dlm_incomplet, chart3_wilaya
                            .nbr_menages_sans_dlm
                        ];




                        if (chart_3) {
                            chart_3.data.labels = chart3Labels_wilaya;
                            chart_3.data.datasets[0].data = chart3Data_wilaya;

                            chart_3.update();
                        }

                        var securise_charts = result.charts.securise;
                        var base_charts = result.charts.base;
                        var non_ameliores_charts = result.charts.non_ameliores;
                        var men_dal_charts = result.charts.men_dal;
                        var menages_c = safeNumber(result.access_assainisement_familial.menages) || 1;
                        var pm_menages_c = safeNumber(result.access_assainisement_familial.pm.menages) || 1;
                        var taux_securise_c = safePercentValue(securise_charts, menages_c);
                        var taux_base_c = safePercentValue(base_charts, menages_c);
                        var taux_non_ameliores_c = safePercentValue(non_ameliores_charts, menages_c);
                        var taux_men_dal_c = safePercentValue(men_dal_charts, pm_menages_c);
                        var limite_charts = Math.max(0, 100 - (safeNumber(taux_base_c) + safeNumber(taux_non_ameliores_c) + safeNumber(taux_men_dal_c))).toFixed(1);


                        var chart2Labels_wilaya = [@json(__('tb.securise')),
                            @json(__('tb.limite')), @json(__('tb.base')),
                            @json(__('tb.non_ameliores')), @json(__('tb.men_dal'))
                        ];
                        var chart2Data_wilaya = [taux_securise_c, limite_charts, taux_base_c,
                            taux_non_ameliores_c,
                            taux_men_dal_c
                        ];

                        if (chart_2) {
                            chart_2.data.labels = chart2Labels_wilaya;
                            chart_2.data.datasets[0].data = chart2Data_wilaya;

                            chart_2.update();
                        }
                        var chart1_wilaya = result.charts.nb_loc;
                        var c_nb_loc_declench = result.nb_loc_declench;
                        var labels = [];
                        var data1 = [];
                        var data2 = [];
                        $.each(chart1_wilaya, function(key, value) {
                            labels.push(value.year);
                            data1.push(safePercentValue(value.localites_1, c_nb_loc_declench));
                            data2.push(safePercentValue(value.localites_3, c_nb_loc_declench));
                        })

                        if (chart_1) {
                            chart_1.data.labels = labels;
                            chart_1.data.datasets[0].data = data1;
                            chart_1.data.datasets[0].label = @json(__('tb.op_declechement'));
                            chart_1.data.datasets[1].data = data2;
                            chart_1.data.datasets[1].label = @json(__('tb.op_fdal'));
                            chart_1.update();

                        }

                        var chart0Data_wilaya = [result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod
                        ,result.access_assainisement_familial.mise_atcp.villages_redeclenches_cod + result.access_assainisement_familial.mise_atcp.villages_redeclenches_n,result.donnees_generales.nb_localites - (result.access_assainisement_familial.mise_atcp.villages_declenches_cod + result.access_assainisement_familial.mise_atcp.villages_declenches_noncod)];

                        var chart0Labels_wilaya = [@json(__('tb.nb_loc_declench')), @json(__('tb.nb_loc_redeclench')),
                            @json(__('tb.nb_loc_jamais_declench'))
                        ];



                        if (chart_0) {
                            chart_0.data.labels = chart0Labels_wilaya;
                            chart_0.data.datasets[0].data = chart0Data_wilaya;

                            chart_0.update();
                        }
                        if (result.ecoles.count === 0) {
                            $("#ecoles").show();

                        } else {
                            $("#ecoles").show();
                        }
                        if (result.santes.count === 0) {
                            $("#santes").show();

                        } else {
                            $("#santes").show();
                        }

                        const intitule = document.getElementById("intitule");
                        if (intitule) {
                            intitule.textContent = result.donnees_generales
                                .wilaya;
                        }
                        const territoire = document.getElementById("territoire");
                        if (territoire) {
                            territoire.textContent = @json(__('Commune'));
                        }
                        const projet_sans_besoins_sante = document.getElementById(
                            "projet_sans_besoins_sante");
                        if (projet_sans_besoins_sante) {
                            projet_sans_besoins_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins);
                        }


                        const taux_projet_sans_besoins_sante = document.getElementById(
                            "taux_projet_sans_besoins_sante");
                        if (taux_projet_sans_besoins_sante) {
                            taux_projet_sans_besoins_sante.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }


                        const nb_sante_sans_latr = document.getElementById(
                            "nb_sante_sans_latr");
                        if (nb_sante_sans_latr) {
                            nb_sante_sans_latr.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr);
                        }
                        const nbr_cabine_a_const_sante = document.getElementById(
                            "nbr_cabine_a_const_sante");
                        if (nbr_cabine_a_const_sante) {
                            nbr_cabine_a_const_sante.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_sante_sans_latr = document.getElementById(
                            "taux_nb_sante_sans_latr");
                        if (taux_nb_sante_sans_latr) {
                            taux_nb_sante_sans_latr.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_sans_latr * 100 /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const taux_nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_non_satisf");
                        if (taux_nb_sante_besoin_latr_non_satisf) {
                            taux_nb_sante_besoin_latr_non_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf *
                                100 /
                                result
                                .donnees_generales.nb_santes);
                        }
                        const nb_sante_besoin_latr_non_satisf = document.getElementById(
                            "nb_sante_besoin_latr_non_satisf");
                        if (nb_sante_besoin_latr_non_satisf) {
                            nb_sante_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_non_satisf);
                        }
                        const taux_nb_sante_besoin_latr_satisf = document.getElementById(
                            "taux_nb_sante_besoin_latr_satisf");
                        if (taux_nb_sante_besoin_latr_satisf) {
                            taux_nb_sante_besoin_latr_satisf.textContent = formatTaux(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf /
                                result
                                .donnees_generales.nb_santes);
                        }

                        const nb_sante_besoin_latr_satisf = document.getElementById(
                            "nb_sante_besoin_latr_satisf");
                        if (nb_sante_besoin_latr_satisf) {
                            nb_sante_besoin_latr_satisf.textContent = formatNumberReport(result
                                .santes
                                .besoin_assainissement.nb_sante_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal_sante = document.getElementById(
                            "taux_nb_dlm_instal_sante");
                        if (taux_nb_dlm_instal_sante) {
                            taux_nb_dlm_instal_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_santes);
                        }
                        const nb_dlm_instal_sante = document.getElementById(
                            "nb_dlm_instal_sante");
                        if (nb_dlm_instal_sante) {
                            nb_dlm_instal_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr_sante = document.getElementById(
                            "taux_nbr_cabine_pmr_sante");
                        if (taux_nbr_cabine_pmr_sante) {
                            taux_nbr_cabine_pmr_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_pmr_sante = document.getElementById(
                            "nbr_cabine_pmr_sante");
                        if (nbr_cabine_pmr_sante) {
                            nbr_cabine_pmr_sante.textContent = formatNumberReport(result.santes
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg_sante = document.getElementById(
                            "taux_nbr_cabine_fem_hyg_sante");
                        if (taux_nbr_cabine_fem_hyg_sante) {
                            taux_nbr_cabine_fem_hyg_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const nbr_cabine_fem_hyg_sante = document.getElementById(
                            "nbr_cabine_fem_hyg_sante");
                        if (nbr_cabine_fem_hyg_sante) {
                            nbr_cabine_fem_hyg_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem_sante = document.getElementById(
                            "taux_dist_cab_hom_fem_sante");
                        if (taux_dist_cab_hom_fem_sante) {
                            taux_dist_cab_hom_fem_sante.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_santes);
                        }

                        const dist_cab_hom_fem_sante = document.getElementById(
                            "dist_cab_hom_fem_sante");
                        if (dist_cab_hom_fem_sante) {
                            dist_cab_hom_fem_sante.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const couvert_sante_latr = document.getElementById(
                            "couvert_sante_latr");
                        if (couvert_sante_latr) {
                            couvert_sante_latr.textContent = formatNumberReport(result
                                .santes
                                .acces_assainissement.couvert_sante_latr);
                        }

                        const taux_couvert_sante_latr = document.getElementById(
                            "taux_couvert_sante_latr");
                        if (taux_couvert_sante_latr) {
                            taux_couvert_sante_latr.textContent = formatTaux(result
                                .santes
                                .acces_assainissement.couvert_sante_latr * 100 / result
                                .donnees_generales.nb_santes)
                        }


                        const projet_sans_besoins = document.getElementById(
                            "projet_sans_besoins");
                        if (projet_sans_besoins) {
                            projet_sans_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins);
                        }

                        const projet_besoins = document.getElementById(
                            "projet_besoins");
                        if (projet_besoins) {
                            projet_besoins.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.projet_besoins);
                        }


                        const taux_projet_sans_besoins = document.getElementById(
                            "taux_projet_sans_besoins");
                        if (taux_projet_sans_besoins) {
                            taux_projet_sans_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_sans_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const taux_projet_besoins = document.getElementById(
                            "taux_projet_besoins");
                        if (taux_projet_besoins) {
                            taux_projet_besoins.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.projet_besoins * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }


                        const nb_ecole_sans_latr = document.getElementById(
                            "nb_ecole_sans_latr");
                        if (nb_ecole_sans_latr) {
                            nb_ecole_sans_latr.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr);
                        }
                        const nbr_cabine_a_const = document.getElementById(
                            "nbr_cabine_a_const");
                        if (nbr_cabine_a_const) {
                            nbr_cabine_a_const.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nbr_cabine_a_const);
                        }


                        const taux_nb_ecole_sans_latr = document.getElementById(
                            "taux_nb_ecole_sans_latr");
                        if (taux_nb_ecole_sans_latr) {
                            taux_nb_ecole_sans_latr.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_sans_latr * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const taux_nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_non_satisf");
                        if (taux_nb_ecole_besoin_latr_non_satisf) {
                            taux_nb_ecole_besoin_latr_non_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf /
                                result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_ecole_besoin_latr_non_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_non_satisf");
                        if (nb_ecole_besoin_latr_non_satisf) {
                            nb_ecole_besoin_latr_non_satisf.textContent = formatNumberReport(
                                result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_non_satisf);
                        }
                        const taux_nb_ecole_besoin_latr_satisf = document.getElementById(
                            "taux_nb_ecole_besoin_latr_satisf");
                        if (taux_nb_ecole_besoin_latr_satisf) {
                            taux_nb_ecole_besoin_latr_satisf.textContent = formatTaux(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf * 100 /
                                result
                                .donnees_generales.nb_ecoles);
                        }

                        const nb_ecole_besoin_latr_satisf = document.getElementById(
                            "nb_ecole_besoin_latr_satisf");
                        if (nb_ecole_besoin_latr_satisf) {
                            nb_ecole_besoin_latr_satisf.textContent = formatNumberReport(result
                                .ecoles
                                .besoin_assainissement.nb_ecole_besoin_latr_satisf);
                        }

                        const taux_nb_dlm_instal = document.getElementById(
                            "taux_nb_dlm_instal");
                        if (taux_nb_dlm_instal) {
                            taux_nb_dlm_instal.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal * 100 / result
                                .donnees_generales.nb_ecoles);
                        }
                        const nb_dlm_instal = document.getElementById(
                            "nb_dlm_instal");
                        if (nb_dlm_instal) {
                            nb_dlm_instal.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nb_dlm_instal);
                        }

                        const taux_nbr_cabine_pmr = document.getElementById(
                            "taux_nbr_cabine_pmr");
                        if (taux_nbr_cabine_pmr) {
                            taux_nbr_cabine_pmr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_pmr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_pmr = document.getElementById(
                            "nbr_cabine_pmr");
                        if (nbr_cabine_pmr) {
                            nbr_cabine_pmr.textContent = formatNumberReport(result.ecoles
                                .acces_assainissement.nbr_cabine_pmr);
                        }

                        const taux_nbr_cabine_fem_hyg = document.getElementById(
                            "taux_nbr_cabine_fem_hyg");
                        if (taux_nbr_cabine_fem_hyg) {
                            taux_nbr_cabine_fem_hyg.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const nbr_cabine_fem_hyg = document.getElementById(
                            "nbr_cabine_fem_hyg");
                        if (nbr_cabine_fem_hyg) {
                            nbr_cabine_fem_hyg.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.nbr_cabine_fem_hyg);
                        }
                        const taux_dist_cab_hom_fem = document.getElementById(
                            "taux_dist_cab_hom_fem");
                        if (taux_dist_cab_hom_fem) {
                            taux_dist_cab_hom_fem.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem * 100 / result
                                .donnees_generales.nb_ecoles);
                        }

                        const dist_cab_hom_fem = document.getElementById(
                            "dist_cab_hom_fem");
                        if (dist_cab_hom_fem) {
                            dist_cab_hom_fem.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.dist_cab_hom_fem);
                        }


                        const ratio = document.getElementById(
                            "ratio");
                        if (ratio) {
                            ratio.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result.ecoles
                                .acces_assainissement.nbr_cabine_fonct_sum);
                        }

                        const effectif = document.getElementById(
                            "effectif");
                        if (effectif) {
                            effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif);
                        }


                        const taux_effectif = document.getElementById(
                            "taux_effectif");
                        if (taux_effectif) {
                            taux_effectif.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.effectif / result
                                .donnees_generales.nb_ecoles);
                        }

                        const couvert_ecole_latr = document.getElementById(
                            "couvert_ecole_latr");
                        if (couvert_ecole_latr) {
                            couvert_ecole_latr.textContent = formatNumberReport(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr);
                        }

                        const taux_couvert_ecole_latr = document.getElementById(
                            "taux_couvert_ecole_latr");
                        if (taux_couvert_ecole_latr) {
                            taux_couvert_ecole_latr.textContent = formatTaux(result
                                .ecoles
                                .acces_assainissement.couvert_ecole_latr * 100 / result
                                .donnees_generales.nb_ecoles);
                        }


                        $('#projets_perspectives_santes').html('');
                        $.each(result.santes.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_santes').append(
                                `<tr>
            <td>` + value.intitule_resume + `</td>
            <td>` + value.intitule + `</td>
            <td>` + formatNumberReport(value.an_program) + `</td>
            
            <td>` + formatNumberReport(value.infrastructures) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
        </tr>`
                            );
                        })
                        $('#projets_perspectives_ecoles').html('');
                        $.each(result.ecoles.projets_perspectives, function(key,
                            value) {
                            $('#projets_perspectives_ecoles').append(
                                `<tr>
            <td>` + value.intitule_resume + `</td>
            <td>` + value.intitule + `</td>
            <td>` + formatNumberReport(value.an_program) + `</td>
            
            <td>` + formatNumberReport(value.infrastructures) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_a_const) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_a_rehab) + `</td>
        </tr>`
                            );
                        })


                        $('#projets_passes_santes').html('');
                        $.each(result.santes.projets_passees, function(key,
                            value) {
                            $('#projets_passes_santes').append(
                                `<tr>
            <td>` + value.intitule_resume + `</td>
            <td>` + value.intitule + `</td>
            <td>` + formatNumberReport(value.an_recept) + `</td>
            
            <td>` + formatNumberReport(value.infrastructures) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
        </tr>`
                            );
                        })
                        $('#projets_passes_ecoles').html('');

                        $.each(result.ecoles.projets_passees, function(key,
                            value) {
                            $('#projets_passes_ecoles').append(
                                `<tr>
            <td>` + value.intitule_resume + `</td>
            <td>` + value.intitule + `</td>
            <td>` + formatNumberReport(value.an_recept) + `</td>
            
            <td>` + value.infrastructures + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_const) + `</td>
            <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
        </tr>`
                            );
                        })

                        $('#approv_eau_santes').html('');
                        if(result.santes.approv_eau.length == 0){
                            $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                            );
                        } else{

                            $.each(result.santes.approv_eau, function(key,
                                value) {
                                $('#approv_eau_santes').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 / result.donnees_generales
                                        .nb_santes) + `</td>
                                    </tr>`
                                );
                            })
                        }
                        $('#approv_eau_ecoles').html('');
                        if(result.ecoles.approv_eau.length == 0){
                            $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{

                            $.each(result.ecoles.approv_eau, function(key,
                                value) {
                                $('#approv_eau_ecoles').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatTaux(value.infrastructures * 100 /
                                        result.donnees_generales.nb_ecoles) + `</td>
                                    </tr>`
                                );
                            })
                        }

                        const nb_ecoles = document.getElementById("nb_ecoles");
                        if (nb_ecoles) {
                            nb_ecoles.textContent = formatNumberReport(result.donnees_generales
                                .nb_ecoles);
                        }
                        const nb_santes = document.getElementById("nb_santes");
                        if (nb_santes) {
                            nb_santes.textContent = formatNumberReport(result.donnees_generales
                                .nb_santes);
                        }

                        const nb_eleves = document.getElementById("nb_eleves");
                        if (nb_eleves) {
                            nb_eleves.textContent = formatNumberReport(result.donnees_generales
                                .nb_eleves);
                        }


                        const nb_wilayas = document.getElementById("nb_wilayas");
                        if (nb_wilayas) {
                            nb_wilayas.textContent = formatNumberReport(result.donnees_generales
                                .nb_wilayas);
                        }


                        const nbr_sans_dlm = document.getElementById("nbr_sans_dlm");
                        if (nbr_sans_dlm) {
                            nbr_sans_dlm.textContent = formatNumberReport2(result.access_hygiene
                                .nbr_sans_dlm);
                        }

                        const nbr_localites_incomplet = document.getElementById(
                            "nbr_localites_incomplet");
                        if (nbr_localites_incomplet) {
                            nbr_localites_incomplet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_incomplet);
                        }
                        const nbr_localites_complet = document.getElementById(
                            "nbr_localites_complet");
                        if (nbr_localites_complet) {
                            nbr_localites_complet.textContent = formatNumberReport2(result
                                .access_hygiene
                                .nbr_localites_complet);
                        }
                        const nbr_localites = document.getElementById("nbr_localites");
                        if (nbr_localites) {
                            nbr_localites.textContent = formatNumberReport(result.access_hygiene
                                .nbr_localites);
                        }

                        const nbr_menages_sans_dlm = document.getElementById(
                            "nbr_menages_sans_dlm");
                        if (nbr_menages_sans_dlm) {
                            nbr_menages_sans_dlm.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_sans_dlm);
                        }




                        const nbr_menages_dlm_incomplet = document.getElementById(
                            "nbr_menages_dlm_incomplet");
                        if (nbr_menages_dlm_incomplet) {
                            nbr_menages_dlm_incomplet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_incomplet);
                        }

                        const nbr_menages_dlm_complet = document.getElementById(
                            "nbr_menages_dlm_complet");
                        if (nbr_menages_dlm_complet) {
                            nbr_menages_dlm_complet.textContent = formatNumberReport(result
                                .access_hygiene
                                .nbr_menages_dlm_complet);
                        }



                        $('#financement').html('');
                        $('#financement').append(
                            `<tr><td>Taux de latrines auto-financées</td><td>` + formatTaux(
                                result
                                .access_assainisement_familial
                                .auto_finance) +
                            `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latrine_autofin) +
                            `</td></tr>
    <tr><td>Nombre de ménages pour lesquels les données d'investissement ont été recueillies</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .localites_latr_invest_men_dispo) +
                            `</td></tr>
    <tr><td>Montant total investi par les ménages dans leurs latrines</td><td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .investi) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
    <tr><td>Montant moyen investi par les ménages dans une latrine(MRU)</td><td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .investi / result
                                .access_assainisement_familial
                                .nb_menages_inv) + `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_mont_invest_men) +
                            `</td></tr>
    <tr><td>Taux de latrines financées par entraide communautaire</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_entrainde) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_entrainde) +
                            `</td></tr>

    <tr><td>Taux de latrines subventionnées</td><td>` +
                            formatTaux(result
                                .access_assainisement_familial
                                .latrines_subv) +
                            `</td><td>` + formatNumberReport(result
                                .access_assainisement_familial.localites_subv) +
                            `</td></tr>`
                        );

                        $('#resultas').html('');
                        $('#resultas').append(
                            `<tr>
<td>Nombre de latrines familiales avant mise en œuvre de l'ATPC</td>
<td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_EDL) + `</td>
<td>
    ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_EDL) + `
</td>

</tr>
<tr>
<td>Taux de couverture initial en latrines familiales avant mise en œuvre de l'ATPC</td>
<td>` + formatTaux(result
                                .access_assainisement_familial.couvertures_EDL * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `</td>
        <td>
        </td>
    </tr>
    <tr>
    <td>Nombre de latrines familiales lors du dernier suivi</td>
    <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .couvertures_dernier) + `</td>
    <td>
    ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nb_localites_couvertures_dernier) + `

        </td>
    </tr>
    <tr>
        <td>Taux de couverture final en latrines familiales</td>

        <td>
            ` +
                            formatTaux(result
                                .access_assainisement_familial.couvertures_dernier * 100 /
                                result
                                .access_assainisement_familial.pm.menages) + `
        </td>
        <td>
        </td>
    </tr>
    <tr>
<td>Nombre de nouvelles latrines construites</td>
<td>
    ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nouv_latr_const) +
                            `</td>
    <td>
        ` +
                            formatNumberReport(result
                                .access_assainisement_familial.nb_localites_nouv_latr_const
                            ) +
                            `
        </td>
    </tr>
    <tr>
        <td>Taux de latrines auto-construites</td>

        <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_autoconst) +
                            `</td>


    <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_autoconst) +
                            `</td>
    </tr>
    <tr>
        <td>Taux de latrines réalisées avec l'aide d'un maçon</td>

        <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_realise_macon) +
                            `</td>
    <td>

    ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_realise_macon) +
                            `
    </td>
</tr>
<tr>
    <td>Nombre de latrines familiales en construction lors du dernier suivi</td>
    <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_latrine_en_constr) +
                            `</td>
        <td>
            
            ` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_latrine_en_constr) +
                            `
        </td>
    </tr>
    <tr>
        <td>Nombre de latrines endommagées suite à des phénomènes météorologiques extrêmes</td>
        <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_menages_ltr_end_phm) +
                            `</td>
    <td>` +
                            formatNumberReport(result
                                .access_assainisement_familial
                                .nbr_localites_menages_ltr_end_phm) +
                            `</td>
    </tr>

    <tr>
        <td>Nb de latrines familiales associées à un DLM fonctionnel</td>

        <td>` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_dlm) +
                            `</td>
    <td>

    ` +
                            formatNumberReport(result.access_assainisement_familial
                                .nbr_menages_dlm
                                .nbr_localites) +
                            `
  </td>
</tr>
<tr>
  <td>Taux de latrines associées à un DLM fonctionnel</td>

  <td>` +
                            formatTaux(result.access_assainisement_familial.nbr_menages_dlm
                                .nbr_dlm * 100 / result.access_assainisement_familial
                                .nbr_menages_dlm.nb_tot_latr_fam) +
                            `</td><td></td>
        </tr>`
                        );


                        const securise = document.getElementById("securise");
                        if (securise) {
                            securise.textContent = formatTaux(result
                                .access_assainisement_familial
                                .securise * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }
                        const nb_localite_securise = document.getElementById(
                            "nb_localite_securise");
                        if (nb_localite_securise) {
                            nb_localite_securise.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_securise);
                        }
                        const base = document.getElementById("base");
                        if (base) {
                            base.textContent = formatTaux(result.access_assainisement_familial
                                .base * 100 / result
                                .access_assainisement_familial
                                .menages);

                        }
                        const nb_localite_base = document.getElementById(
                            "nb_localite_base");
                        if (nb_localite_base) {
                            nb_localite_base.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_base);
                        }
                        const limite = document.getElementById("limite");
                        if (limite) {
                            limite.textContent = formatTaux(result.access_assainisement_familial
                                .limite * 100 / result.access_assainisement_familial
                                .menages_1);

                        }
                        const nb_localite_limite = document.getElementById(
                            "nb_localite_limite");
                        if (nb_localite_limite) {
                            nb_localite_limite.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_limite);
                        }

                        const non_ameliores = document.getElementById("non_ameliores");
                        if (non_ameliores) {
                            non_ameliores.textContent = formatTaux(result
                                .access_assainisement_familial
                                .non_ameliores * 100 / result
                                .access_assainisement_familial
                                .menages);
                        }
                        const nb_localite_non_ameliores = document.getElementById(
                            "nb_localite_non_ameliores");
                        if (nb_localite_non_ameliores) {
                            nb_localite_non_ameliores.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_non_ameliores);
                        }
                        const men_dal = document.getElementById("men_dal");
                        if (men_dal) {
                            men_dal.textContent = formatTaux(result
                                .access_assainisement_familial
                                .men_dal * 100 / result.access_assainisement_familial
                                .menages_1);
                        }
                        const nb_localite_men_dal = document.getElementById(
                            "nb_localite_men_dal");
                        if (nb_localite_men_dal) {
                            nb_localite_men_dal.textContent = formatNumberReport(result
                                .access_assainisement_familial.nb_localite_men_dal);
                        }


                        $('#fdal').html('');
                        var cumul_decle = 0;
                        var cumul_certif = 0;
                        $.each(result.access_assainisement_familial.fdal, function(key, value) {
                            cumul_decle += parseInt(value.localites_1);
                            cumul_certif += parseInt(value.localites_3);
                            $('#fdal').append(
                                `<tr>
          <td>` + (value.year == null ? '' : value.year) + `</td>
          <td>` + formatNumberReport(value.localites_1) + `</td>
          <td>` + formatNumberReport(value.localites_3) + `</td>
      </tr>`
                            );


                        })

                        $('#fdal').append(`<td>Total</td> <td>` + cumul_decle + `</td>
<td>` + cumul_certif + `</td>`);
                        const certification_taux = document.getElementById(
                            "certification_taux");
                        if (certification_taux) {
                            certification_taux.textContent = formatTaux(
                                cumul_certif * 100 / cumul_decle);
                        }
                        const certification_nbr = document.getElementById(
                            "certification_nbr");
                        if (certification_nbr) {
                            certification_nbr.textContent = formatNumberReport(
                                cumul_decle - cumul_certif);
                        }

                        $('#approv_eau').html('');
                        var approv_eau = 0;
                        $.each(result.access_assainisement_familial.approv_eau, function(key,
                            value) {
                            approv_eau += parseInt(value.localites);
                        });
                        if(result.access_assainisement_familial.approv_eau.length == 0){
                            $('#approv_eau').append(
                                    `<tr>
                                        <td>NR</td>
                                        <td>NR</td>
                                        <td>NR</td>
                                    </tr>`
                                );
                        }else{
                            $.each(result.access_assainisement_familial.approv_eau, function(key,
                                value) {
    
                                $('#approv_eau').append(
                                    `<tr>
                                        <td>` + value.approv_eau + `</td>
                                        <td>` + formatNumberReport(value.localites) + `</td>
                                        <td>` + formatTaux(value.localites * 100 /
                                        approv_eau) + `</td>
                                    </tr>`
                                );
                            })
                        }



                        const pm_menages = document.getElementById("pm_menages");
                        if (pm_menages) {
                            pm_menages.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .menages);
                        }
                        const pm_population = document.getElementById("pm_population");
                        if (pm_population) {
                            pm_population.textContent = formatNumberReport(result
                                .access_assainisement_familial.pm
                                .population);
                        }




                        const villages_appui_suivi = document.getElementById(
                            "villages_appui_suivi");
                        if (villages_appui_suivi) {
                            villages_appui_suivi.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi);
                        }
                        const taux_villages_appui_suivi = document.getElementById(
                            "taux_villages_appui_suivi");
                        if (taux_villages_appui_suivi) {
                            taux_villages_appui_suivi.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_appui_suivi * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_redeclenches_noncod = document.getElementById(
                            "villages_redeclenches_noncod");
                        if (villages_redeclenches_noncod) {
                            villages_redeclenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod);
                        }

                        /*const taux_villages_redeclenches_noncod = document.getElementById(
                            "taux_villages_redeclenches_noncod");
                        if (taux_villages_redeclenches_noncod) {
                            taux_villages_redeclenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_noncod * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }*/
                        const villages_redeclenches_cod = document.getElementById(
                            "villages_redeclenches_cod");
                        if (villages_redeclenches_cod) {
                            villages_redeclenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod);
                        }
                        const taux_villages_redeclenches_cod = document.getElementById(
                            "taux_villages_redeclenches_cod");
                        if (taux_villages_redeclenches_cod) {
                            taux_villages_redeclenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_redeclenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }

                        const villages_declenches = document.getElementById(
                            "villages_declenches");
                        if (villages_declenches) {
                            villages_declenches.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod + result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches = document.getElementById(
                            "taux_villages_declenches");
                        if (taux_villages_declenches) {
                            taux_villages_declenches.textContent = formatTaux((result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_noncod + result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }



                        const villages_declenches_noncod = document.getElementById(
                            "villages_declenches_noncod");
                        if (villages_declenches_noncod) {
                            villages_declenches_noncod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod);
                        }
                        /*const taux_villages_declenches_noncod = document.getElementById(
                            "taux_villages_declenches_noncod");
                        if (taux_villages_declenches_noncod) {
                            taux_villages_declenches_noncod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_noncod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }*/

                        const villages_declenches_cod = document.getElementById(
                            "villages_declenches_cod");
                        if (villages_declenches_cod) {
                            villages_declenches_cod.textContent = formatNumberReport(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod);
                        }

                        const taux_villages_declenches_cod = document.getElementById(
                            "taux_villages_declenches_cod");
                        if (taux_villages_declenches_cod) {
                            taux_villages_declenches_cod.textContent = formatTaux(result
                                .access_assainisement_familial.mise_atcp
                                .villages_declenches_cod * 100 / result.donnees_generales
                                .nb_localites_codifiees);
                        }
                        const villages_jamais_declenches = document.getElementById(
                            "villages_jamais_declenches");
                        if (villages_jamais_declenches) {
                            villages_jamais_declenches.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees - result
                                .access_assainisement_familial
                                .mise_atcp.villages_declenches_cod);
                        }
                        const taux_villages_jamais_declenches = document.getElementById(
                            "taux_villages_jamais_declenches");
                        if (taux_villages_jamais_declenches) {
                            taux_villages_jamais_declenches.textContent = formatTaux((
                                    result
                                    .donnees_generales.nb_localites_codifiees - result
                                    .access_assainisement_familial.mise_atcp
                                    .villages_declenches_cod) * 100 / result
                                .donnees_generales.nb_localites_codifiees);
                        }

                        $('#projets').html('');
                        $.each(result.access_assainisement_familial.projets, function(key,
                            value) {
                            $('#projets').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
        <td>` + (value.year == null ? '' : value.year) + `</td><td>` + value.localites +
                                `</td></tr>`
                            );
                        })


                        const nb_moughatas = document.getElementById("nb_moughatas");
                        if (nb_moughatas) {
                            nb_moughatas.textContent = formatNumberReport(result
                                .donnees_generales.nb_moughatas);
                        }
                        const nb_communes = document.getElementById("nb_communes");
                        if (nb_communes) {
                            nb_communes.textContent = formatNumberReport(result
                                .donnees_generales.nb_communes);
                        }

                        const nb_localites = document.getElementById("nb_localites");
                        if (nb_localites) {
                            nb_localites.textContent = formatNumberReport(result
                                .donnees_generales.nb_localites);
                        }
                        const nb_localites_codifiees = document.getElementById(
                            "nb_localites_codifiees");
                        if (nb_localites_codifiees) {
                            nb_localites_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_codifiees);
                        }

                        const nb_localites_non_codifiees = document.getElementById(
                            "nb_localites_non_codifiees");
                        if (nb_localites_non_codifiees) {
                            nb_localites_non_codifiees.textContent = formatNumberReport(result
                                .donnees_generales
                                .nb_localites_non_codifiees);
                        }



                        const population = document.getElementById("population");
                        if (population) {
                            population.textContent = formatNumberReport(result.donnees_generales
                                .population);
                        }
                        const rapport = document.getElementById("rapport");
                        if (rapport) {
                            rapport.textContent = 'Rapport de la commune du ' + result
                                .donnees_generales.wilaya;
                        }

                    }

                });


            });
        });
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
        var map5 = L.map('map5')
            .setView([19.353, -11.448], 6),
            osmUrl =
                'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution =
                'Map data &copy; 2012 OpenStreetMap contributors';
        var map6 = L.map('map6')
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
        var osm5 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map5);
        var osm6 = L.TileLayer
            .boundaryCanvas(
                osmUrl, {
                boundary: latLngGeom,
                attribution: osmAttribution
            }).addTo(map6);
        $(document).ready(function () {
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

            L.control.bigImage({
                position: 'topright'
            }).addTo(map5);
            L.control.bigImage({
                position: 'topright'
            }).addTo(map6);

            $.ajax({

                url: "{{ url('admin/maps/fetch-maps') }}",

                type: "POST",

                data: {
                    _token: '{{ csrf_token() }}'

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

                    map4.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map4.removeLayer(layer);
                        }
                    });
                    map5.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map5.removeLayer(layer);
                        }
                    });
                    map6.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map6.removeLayer(layer);
                        }
                    });
                    $.each(res.santes_besoin_latr_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: goldIcon
                        })
                            .addTo(map4);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });

                    });
                    $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: goldIcon
                        })
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: violetIcon
                        })
                            .addTo(map4);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: violetIcon
                        })
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.santes_sans_latr, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: greyIcon
                        })
                            .addTo(map4);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.ecoles_sans_latr, function (key, value) {
                        var marker = L.marker([value.lat_infra_publ, value
                            .long_infra_publ
                        ], {
                            icon: greyIcon
                        })
                            .addTo(map3);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
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
                    $.each(res.non_declenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: redIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.redeclenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: greenIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });
                    $.each(res.declenches, function (key, value) {
                        var marker = L.marker([value.lat_loc, value.long_loc], {
                            icon: yellowIcon
                        })
                            .addTo(map);
                        marker.on('mouseover',
                            function () {
                                marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                            });
                    });



                }

            });

            $('#wilaya').on('change', function () {

                var idWilaya = this.value;
                $("#moughata").html('');
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        _token: '{{ csrf_token() }}'

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
                        map4.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                        map5.eachLayer(function (layer) {
                                if (layer instanceof L.Marker) {
                                    map5.removeLayer(layer);
                                }
                            });
                            map6.eachLayer(function (layer) {
                                if (layer instanceof L.Marker) {
                                    map6.removeLayer(layer);
                                }
                            });
                        map.setView([res.view.lat, res.view.lon], 8);
                        map2.setView([res.view.lat, res.view.lon], 8);
                        map3.setView([res.view.lat, res.view.lon], 8);
                        map4.setView([res.view.lat, res.view.lon], 8);
                        map5.setView([res.view.lat, res.view.lon], 8);
                        map6.setView([res.view.lat, res.view.lon], 8);
                        $.each(res.santes_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.santes_sans_latr, function (key, value) {

                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.ecoles_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
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

                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });



                    }

                });
                $.ajax({

                    url: "{{ url('admin/api/fetch-moughatas') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function (key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc_fr + '</option>');

                        });


                    }

                });

            });
            $('#moughata').on('change', function () {

                var idMoughata = this.value;
                var idWilaya = $("#wilaya").val();


                $("#commune").html('');
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        _token: '{{ csrf_token() }}'

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
                        map4.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                        map5.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map5.removeLayer(layer);
                            }
                        });
                        map6.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map6.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 10);
                        map2.setView([res.view.lat, res.view.lon], 10);
                        map3.setView([res.view.lat, res.view.lon], 10);
                        map5.setView([res.view.lat, res.view.lon], 10);
                        map6.setView([res.view.lat, res.view.lon], 10);
                        $.each(res.santes_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.santes_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });
                        $.each(res.ecoles_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });


                        });
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

                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });
                        });


                    }

                });
                $.ajax({

                    url: "{{ url('admin/api/fetch-communes') }}",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (res) {

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function (key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc_fr + '</option>');

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

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        _token: '{{ csrf_token() }}'

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
                        map4.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map4.removeLayer(layer);
                            }
                        });
                                                map5.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map5.removeLayer(layer);
                            }
                        });                        map6.eachLayer(function (layer) {
                            if (layer instanceof L.Marker) {
                                map6.removeLayer(layer);
                            }
                        });
                        map.setView([res.view.lat, res.view.lon], 8);
                        map2.setView([res.view.lat, res.view.lon], 8);
                        map3.setView([res.view.lat, res.view.lon], 8);
                        map4.setView([res.view.lat, res.view.lon], 8);
                        map5.setView([res.view.lat, res.view.lon], 8);
                        map6.setView([res.view.lat, res.view.lon], 8);
                        $.each(res.santes_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.ecoles_besoin_latr_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: goldIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.santes_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.ecoles_besoin_latr_non_satisf, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: violetIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.santes_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map4);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.ecoles_sans_latr, function (key, value) {
                            var marker = L.marker([value.lat_infra_publ, value
                                .long_infra_publ
                            ], {
                                icon: greyIcon
                            })
                                .addTo(map3);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
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

                        $.each(res.non_declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: redIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });

                        });
                        $.each(res.redeclenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: greenIcon
                            })
                                .addTo(map);
                            marker.on('mouseover',
                                function () {
                                    marker.bindPopup(value.intitule + ' LOC : ' + value.long_loc + ' , ' + value.lat_loc).openPopup();
                                });


                        });
                        $.each(res.declenches, function (key, value) {
                            var marker = L.marker([value.lat_loc, value.long_loc], {
                                icon: yellowIcon
                            })
                                .addTo(map);
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
