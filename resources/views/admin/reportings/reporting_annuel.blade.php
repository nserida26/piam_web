@extends('layouts.admin')
@section('title')
@lang('sidebar.reporting')
@endsection
@section('contentdeader')
@lang('sidebar.reporting')
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">

<link rel="stylesheet" href="{{ asset('assets/admin/plugins/year-picker/dist/yearpicker.css') }}">
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
        font-size: 20px;
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
        padding: 10px 8px;
        border: 2px solid #000000;
    }

    /* Alignement des chiffres à droite */
    .table-element td:not(:first-child),
    .table-element th:not(:first-child) {
        text-align: right;
        padding: 8px 12px;
        border: 2px solid #000000;
    }

    /* Alignement à gauche pour la première colonne (texte) */
    .table-element td:first-child,
    .table-element th:first-child {
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
            color: black;
        }
    }

    .card_title_center {
        text-align: center;
        font-weight: bold;
        margin: 20px 0;
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
    <!-- /.card-header -->

    <div class="card-body">
        <div class="row">
            
            <!-- 🟦 Année -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="annee" class="form-label fw-bold">Année</label>
                    <input type="text" class="form-control yearpicker" id="annee">
                </div>
            </div>

            <!-- 🟩 Wilaya -->
            <div class="col-md-3" id="wilayas">
                <div class="form-group">
                    <label for="wilaya" class="form-label fw-bold">Wilaya</label>
                    <select name="wilaya" id="wilaya" class="form-control wilaya" disabled="disabled">
                        <option value="all">TOUTES LES WILAYAS</option>
                        @foreach ($wilayas as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🟧 Moughataa -->
            <div class="col-md-3" id="moughatas">
                <div class="form-group">
                    <label for="moughata" class="form-label fw-bold">Moughataa</label>
                    <select name="moughata" id="moughata" class="form-control moughata" disabled="disabled">
                    </select>
                </div>
            </div>

            <!-- 🟥 Commune -->
            <div class="col-md-3" id="communes">
                <div class="form-group">
                    <label for="commune" class="form-label fw-bold">Commune</label>
                    <select name="commune" id="commune" class="form-control commune" disabled="disabled">
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
    <div id="familial">
        <div class="row">
            <div class="col-12">
                <h4 class="card_title_center" id="rapport"></h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">

                    <!-- /.card-header -->
                    <div class="card-body">

                        <h5>DONNEES GENERALES</h5>
                        <table class="mb-4 table-element" border="3">

                            <tbody>

                                <tr>
                                    <td>Annee</td>
                                    <td id="year"></td>
                                </tr>
                                <tr>
                                    <td>Territoire</td>
                                    <td id="territoire"></td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td id="date">{{ date('d/m/Y') }}</td>
                                </tr>


                            </tbody>
                        </table>
                        <h5>MISE EN ŒUVRE DE L'ATPC</h5>
                        <h6>Projets ayant mis en œuvre l'ATPC </h6>

                        <table class="mb-4 table-element" border="3">
                            <tr>
                                <th>Intitulé du projet</th>
                                <th>Opérateur</th>
                                <th>Territoire </th>

                            </tr>
                            <tbody id="projets">




                            </tbody>
                        </table>
                        <h6>Résultats de la mise en œuvre de l'ATPC</h6>
                        <table class="mb-4 table-element" border="3">

                            <tbody>

                                <tr>

                                    <td>Localités déclenchées une première fois</td>
                                    <td id="declenches"></td>
                                </tr>
                                <tr>

                                    <td>Localités redéclenchées</td>
                                    <td id="redeclenches"></td>
                                </tr>
                                <tr>

                                    <td>Population ciblée</td>
                                    <td id="population"></td>
                                </tr>
                                <tr>
                                    <td>Ménages ciblés</td>
                                    <td id="menages"></td>


                                </tr>

                                <tr>
                                    <td>Localités certifiées FDAL</td>
                                    <td id="fdal"></td>


                                </tr>

                                <tr>
                                    <td>Nb de latrines familiales réalisées dans les localités certifiées FDAL dans
                                        l'année
                                    </td>
                                    <td id="realise">
                                    </td>


                                </tr>
                                <tr>
                                    <td>Villages déclenchés et non certifiés FDAL</td>
                                    <td id="non_certif"> </td>


                                </tr>



                            </tbody>
                        </table>
                            <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-orange.png">@lang('map.title4') | <img
                            src="/assets/admin/dist/img/marker-icon-blue.png">@lang('map.title5')</h6>
                            <div class="leaflet-container" id="map2"></div>
                        <h5>MISE EN ŒUVRE DU SUIVI POST-FDAL</h5>
                        <h6>Projets ayant mis en œuvre le suivi post-FDAL</h6>

                        <table class="mb-4 table-element" border="3">
                            <tr>

                                <th>Intitulé du projet</th>
                                <th>Opérateur</th>
                                <th>Territoire </th>

                            </tr>
                            <tbody id="projets_post_fdal">




                            </tbody>
                        </table>
                        <h6>Cibles de la mise en œuvre du suivi post-FDAL</h6>
                        <table class="mb-4 table-element" border="3">

                            <tbody>

                                <tr>
                                    <td>Localités bénéficiaires d'un appui-suivi</td>
                                    <td id="localites"></td>


                                </tr>
                                <tr>

                                    <td>Population ciblée</td>
                                    <td id="nb_tot_pop"></td>
                                </tr>
                                <tr>
                                    <td>Ménages ciblés</td>
                                    <td id="nb_menages_comptabilises"></td>


                                </tr>
                                <tr>
                                    <td>Nb de latrines familiales réalisées dans le cadre de l'appui-suivi </td>
                                    <td id="appui_realise"></td>


                                </tr>

                                <tr>
                                    <td>Taux de couverture en latrines à la fin de l'appui-suivi </td>
                                    <td id="taux_appui_realise"></td>


                                </tr>








                            </tbody>
                        </table>

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
                        <h5>CONSTRUCTION DE LATRINES PUBLIQUES</h5>
                        <h6>Projets ayant réalisé des latrines publiques</h6>

                        <table class="mb-4 table-element" border="3">
                            <tr>
                                <th>Intitulé du projet</th>
                                <th>Opérateur</th>
                                <th>Territoire </th>
                            </tr>
                            <tbody id="projets_p">





                            </tbody>
                        </table>
                        <h6>Ouvrages réceptionnés au cours de l'année</h6>

                        <table class="mb-4 table-element" border="3">

                            <tr>

                                <th>Type de site</th>
                                <th>Nombre de sites ciblés</th>
                                <th>Nombre de bénéficiaires</th>
                                <th>Nombre de blocs réalisés</th>
                                <th>Nombre de cabines réalisées</th>
                                <th>Nombre de cabines réhabilitées</th>

                            </tr>
                            <tbody id="projets_passees">




                            </tbody>
                        </table>
                        <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title14')</h6>
                        <div class="leaflet-container" id="map7"></div>
                        <h6 class="mb-2"><img src="/assets/admin/dist/img/marker-icon-gold.png">@lang('map.title15')</h6>
                        <div class="leaflet-container" id="map8"></div>
                        
                    </div>
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
<script src="{{ asset('assets/admin/dist/js/html2pdf.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/year-picker/dist/yearpicker.js') }}"></script>
        <script src="{{ asset('assets/admin/js/BoundaryCanvas.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/leaflet.extra-markers.min.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/js/Leaflet.BigImage.min.js') }}"></script>
@endpush
@push('custom')
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
        /* Styles communs de base */
        body {
            font-family: "Arial", sans-serif;
            font-size: 16px;
            line-height: 1.4;
            color: #000;
        }
        
        .text-container {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            font-size: 16px;
            background: #f9f9f9;
        }
        
        .table-element {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
        }
        
        .table-element th {
            background-color: #f8f9fa;
            font-weight: bold;
            padding: 10px;
        }
        
        .table-element td {
            padding: 8px;
        }
        
        /* Styles spécifiques pour les titres (à la fois écran et impression) */
        h4 {
            text-align: center !important;
            font-size: 22px !important;
            margin: 20px 0 !important;
            color: #333 !important;
            font-weight: bold !important;
        }
        
        h5 {
            background-color: #00A95C !important;
            color: white !important;
            padding: 12px !important;
            margin: 30px 0 20px 0 !important;
            text-align: center !important;
            font-size: 20px !important;
            font-weight: bold !important;
        }
        
        h6 {
            background-color: #FFD700 !important;
            color: #000 !important;
            padding: 10px !important;
            margin: 25px 0 15px 0 !important;
            text-align: center !important;
            font-size: 18px !important;
            font-weight: bold !important;
        }
        
        /* Styles pour l'aperçu à l'écran */
        @media screen {
            body {
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
        
        /* Styles d'impression - DOIT ÊTRE APRÈS LES STYLES SCREEN */
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body {
                font-size: 14pt !important; /* Utiliser pt pour l'impression */
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
                font-size: 10pt !important;
                color: #666 !important;
            }
            
            .text-container {
                border: 1px solid #000 !important;
                padding: 12px !important;
                margin: 15px 0 !important;
                font-size: 12pt !important; /* Utiliser pt */
                page-break-inside: avoid !important;
                background: #f9f9f9 !important;
            }
            
            .table-element {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 11pt !important; /* Utiliser pt */
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
            
            /* Styles de titre spécifiques pour l'impression - SURCHARGE LES STYLES COMMUNS */
            h4 {
                text-align: center !important;
                font-size: 18pt !important; /* Plus grand pour l'impression */
                margin: 20px 0 10px 0 !important;
                color: #333 !important;
                font-weight: bold !important;
                page-break-after: avoid !important;
            }
            
            h5 {
                background-color: #00A95C !important;
                color: white !important;
                padding: 10px !important;
                margin: 25px 0 15px 0 !important;
                text-align: center !important;
                font-size: 16pt !important; /* Plus grand pour l'impression */
                page-break-after: avoid !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-weight: bold !important;
            }
            
            h6 {
                background-color: #FFD700 !important;
                color: #000 !important;
                padding: 8px !important;
                margin: 20px 0 12px 0 !important;
                text-align: center !important;
                font-size: 14pt !important; /* Plus grand pour l'impression */
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
            .action-buttons,
            .no-print {
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
            <h1 style="color: #00A95C; margin-bottom: 10px; font-size: 24px;">${reportTitle}</h1>
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
        
        <div class="print-footer no-print">
            <p>Document généré par le Système PIAM - Direction de l'Assainissement</p>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"><\/script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"><\/script>
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
                
                // FORCER LES TAILLES DE POLICE DANS LE PDF
                const headings = pdfContent.querySelectorAll('h4, h5, h6');
                headings.forEach(heading => {
                    if (heading.tagName === 'H4') {
                        heading.style.fontSize = '18pt';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H5') {
                        heading.style.fontSize = '16pt';
                        heading.style.fontWeight = 'bold';
                    } else if (heading.tagName === 'H6') {
                        heading.style.fontSize = '14pt';
                        heading.style.fontWeight = 'bold';
                    }
                });
                
                // Ajouter un en-tête au contenu
                const header = document.createElement('div');
                header.style.textAlign = 'center';
                header.style.marginBottom = '30px';
                header.style.paddingBottom = '15px';
                header.style.borderBottom = '2px solid #00A95C';
                header.innerHTML = '<h2 style="color: #00A95C; margin-bottom: 10px; font-size: 20pt;">' + reportTitle + '</h2>' +
                                   '<p style="font-size: 12pt; color: #666; margin: 5px 0;">Rapport d\\'assainissement - Direction de l\\'Assainissement</p>' +
                                   '<p style="font-size: 10pt; color: #888; margin: 5px 0;">' + date + '</p>' +
                                   (currentFilter !== 'Aucun filtre' ? '<p style="font-size: 10pt; color: #555; margin: 5px 0;"><strong>Filtre :</strong> ' + currentFilter + '</p>' : '');
                
                pdfContent.insertBefore(header, pdfContent.firstChild);
                
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
                
                // Ajouter un message d'information
                pdf.setFontSize(12);
                pdf.setTextColor(0, 0, 0);
                pdf.text('Pour le rapport complet avec graphiques et cartes,', 20, 60);
                pdf.text('veuillez utiliser la fonction d\\'impression du navigateur.', 20, 70);
                
                // Pied de page
                pdf.setFontSize(8);
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
            
            // Assurer que les titres sont correctement stylisés
            document.querySelectorAll('h4, h5, h6').forEach(el => {
                el.style.display = 'block';
                el.style.visibility = 'visible';
            });
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
            

        
        });
    </script>
<script>
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
    // Nouvelles fonctions pour le suivi post-FDAL (retourne 0 au lieu de NR)
    function formatNumberReportPostFDAL(number, decimals = 0) {
        if (isNaN(number) || number == null || !isFinite(number)) {
            return '<span style="text-align: right; display: block;">0</span>';
        }
        number = parseFloat(number);
        decimals = parseInt(decimals);
        const formatter = new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
        const formattedNumber = formatter.format(number);
        return `<span style="text-align: right; display: block;">${formattedNumber}</span>`;
    }

    function formatTauxPostFDAL(number, decimals = 1) {
        if (isNaN(number) || number == null || !isFinite(number)) {
            return '<span style="text-align: right; display: block;">0%</span>';
        }
        number = parseFloat(number);
        decimals = parseInt(decimals);
        const formatter = new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
        const formattedNumber = formatter.format(number);
        return `<span style="text-align: right; display: block;">${formattedNumber}%</span>`;
    }

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
        $('#content').hide();

        $('.yearpicker').yearpicker();

        //Initialize Select2 Elements
        $('.wilaya').select2();
        $('.moughata').select2();
        $('.commune').select2();
        $('.type').select2();
        $('#annee').on('change', function () {

            var annee = this.value;
            

            // Activer seulement la wilaya
            $('#wilaya').prop('disabled', false);
            $('#moughata').prop('disabled', true);
            $('#commune').prop('disabled', true);
            
            $('#wilaya').val(null).trigger('change');

            $('#type').val(null).trigger('change');
            const year = document.getElementById("year");
            if (year) {
                year.textContent = annee;
            }
        });


        $('#wilaya').on('change', function () {

            var idWilaya = this.value;


            $("#moughata").html('');

            var annee = $("#annee").val();
            $('#type').val(null).trigger('change');

            if (idWilaya === 'all') {
                $('#moughata').prop('disabled', true);
                $('#commune').prop('disabled', true);

                // Appel AJAX pour toutes les wilayas
                $.ajax({

                    url: "{{ url('admin/reporting/fetch_reporting_annuel_api') }}",

                    type: "POST",

                    data: {

                        annee: annee,
                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (result) {
                        

                        $('#content').show();
                        
                        const territoire = document.getElementById(
                            "territoire");
                        if (territoire) {
                            territoire.textContent = result.territoire;
                        }
                        const rapport = document.getElementById(
                            "rapport");
                        if (rapport) {
                            rapport.textContent = 'Rapport ' + annee + ' du pays';
                        }

                        $('#projets').html('');
                        $.each(result.projets, function (key,
                            value) {
                            $('#projets').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td>` + value.wilaya + `</td></tr>`
                            );
                        })
                        $('#projets_post_fdal').html('');
                        $.each(result.projets_post_fdal, function (key,
                            value) {
                            $('#projets_post_fdal').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                             <td>` + value.wilaya + `</td></tr>`
                            );
                        })

                        $('#projets_p').html('');
                        $.each(result.projets_p, function (key,
                            value) {
                            $('#projets_p').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td>` + value.wilaya + `</td></tr>`
                            );
                        })

                        $('#projets_passees').html('');
                        $.each(result.projets_passees, function (key,
                            value) {

                            $('#projets_passees').append(
                                `<tr><td>` + value.infra_publ + `</td><td>` +
                                formatNumberReport(value.infrastructures) + `</td>
                                        <td>` + formatNumberReport(value.effectif) + `</td>
                                        <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                        <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                        <td>` + formatNumberReport(value.nbr_cabine_const) + `</td></tr>`
                            );
                        })


                        // Par cette nouvelle version :
                        const nb_menages_comptabilises = document.getElementById("nb_menages_comptabilises");
                        if (nb_menages_comptabilises) {
                            nb_menages_comptabilises.innerHTML = formatNumberReportPostFDAL(result.appui_nb_menages_comptabilises);
                        }
                        const appui_realise = document.getElementById("appui_realise");
                        if (appui_realise) {
                            appui_realise.innerHTML = formatNumberReportPostFDAL(result.appui_realise);
                        }
                        const taux_appui_realise = document.getElementById("taux_appui_realise");
                        if (taux_appui_realise) {
                            taux_appui_realise.innerHTML = formatTauxPostFDAL(result.appui_realise * 100 / result.appui_nb_menages_comptabilises);
                        }

                        const nb_tot_pop = document.getElementById("nb_tot_pop");
                        if (nb_tot_pop) {
                            nb_tot_pop.innerHTML = formatNumberReportPostFDAL(result.appui_nb_tot_pop);
                        }

                        const localites = document.getElementById("localites");
                        if (localites) {
                            localites.innerHTML = formatNumberReportPostFDAL(result.appui);
                        }
                        const declenches = document.getElementById("declenches");
                        if (declenches) {
                            declenches.textContent = formatNumberReport(result.declenches);
                        }
                        const redeclenches = document.getElementById("redeclenches");
                        if (redeclenches) {
                            redeclenches.textContent = formatNumberReport(result.redeclenches);
                        }
                        const population = document.getElementById("population");
                        if (population) {
                            population.textContent = formatNumberReport(result.population);
                        }
                        const menages = document.getElementById("menages");
                        if (menages) {
                            menages.textContent = formatNumberReport(result.menages);
                        }
                        const fdal = document.getElementById("fdal");
                        if (fdal) {
                            fdal.textContent = formatNumberReport(result.fdal);
                        }

                        const realise = document.getElementById("realise");
                        if (realise) {
                            realise.textContent = formatNumberReport(result.realise);
                        }
                        const non_certif = document.getElementById("non_certif");
                        if (non_certif) {
                            non_certif.textContent = formatNumberReport(result.non_certif);
                        }


                    }

                });
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {

                        annee: annee,
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
            } else {
                // Activer moughata seulement si une wilaya spécifique est sélectionnée
                $('#moughata').prop('disabled', false);
                $('#commune').prop('disabled', true);
                

                $.ajax({

                    url: "{{ url('admin/reporting/fetch_reporting_annuel_api') }}",

                    type: "POST",

                    data: {

                        annee: annee,
                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (result) {

                        $('#projets').html('');

                        const territoire = document.getElementById(
                            "territoire");
                        if (territoire) {
                            territoire.textContent = result.territoire;
                        }
                        const rapport = document.getElementById(
                            "rapport");
                        if (rapport) {
                            rapport.textContent = 'Rapport ' + annee + ' de la Wilaya du ' + result.territoire;
                        }


                        $.each(result.projets, function (key,
                            value) {
                            $('#projets').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td>` + value.wilaya + `</td></tr>`
                            );
                        })
                        $('#projets_post_fdal').html('');
                        $.each(result.projets_post_fdal, function (key,
                            value) {
                            $('#projets_post_fdal').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                            );
                        })

                        $('#projets_p').html('');
                        $.each(result.projets_p, function (key,
                            value) {
                            $('#projets_p').append(
                                `<tr><td>` + value.intitule_resume + `</td><td>` +
                                value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                            );
                        })

                        $('#projets_passees').html('');
                        $.each(result.projets_passees, function (key,
                            value) {

                            $('#projets_passees').append(
                                `<tr><td>` + value.infra_publ + `</td><td>` +
                                formatNumberReport(value.infrastructures) + `</td>
                                <td>` + formatNumberReport(value.effectif) + `</td>
                                <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                <td>` + formatNumberReport(value.nbr_cabine_const) + `</td></tr>`
                            );
                        })


                        // Par cette nouvelle version :
                        const nb_menages_comptabilises = document.getElementById("nb_menages_comptabilises");
                        if (nb_menages_comptabilises) {
                            nb_menages_comptabilises.innerHTML = formatNumberReportPostFDAL(result.appui_nb_menages_comptabilises);
                        }
                        const appui_realise = document.getElementById("appui_realise");
                        if (appui_realise) {
                            appui_realise.innerHTML = formatNumberReportPostFDAL(result.appui_realise);
                        }
                        const taux_appui_realise = document.getElementById("taux_appui_realise");
                        if (taux_appui_realise) {
                            taux_appui_realise.innerHTML = formatTauxPostFDAL(result.appui_realise * 100 / result.appui_nb_menages_comptabilises);
                        }

                        const nb_tot_pop = document.getElementById("nb_tot_pop");
                        if (nb_tot_pop) {
                            nb_tot_pop.innerHTML = formatNumberReportPostFDAL(result.appui_nb_tot_pop);
                        }

                        const localites = document.getElementById("localites");
                        if (localites) {
                            localites.innerHTML = formatNumberReportPostFDAL(result.appui);
                        }
                        const declenches = document.getElementById("declenches");
                        if (declenches) {
                            declenches.textContent = formatNumberReport(result.declenches);
                        }
                        const redeclenches = document.getElementById("redeclenches");
                        if (redeclenches) {
                            redeclenches.textContent = formatNumberReport(result.redeclenches);
                        }
                        const population = document.getElementById("population");
                        if (population) {
                            population.textContent = formatNumberReport(result.population);
                        }
                        const menages = document.getElementById("menages");
                        if (menages) {
                            menages.textContent = formatNumberReport(result.menages);
                        }
                        const fdal = document.getElementById("fdal");
                        if (fdal) {
                            fdal.textContent = formatNumberReport(result.fdal);
                        }

                        const realise = document.getElementById("realise");
                        if (realise) {
                            realise.textContent = formatNumberReport(result.realise);
                        }
                        const non_certif = document.getElementById("non_certif");
                        if (non_certif) {
                            non_certif.textContent = formatNumberReport(result.non_certif);
                        }


                    }

                });
                
                $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {


                        annee: annee,
                        wilaya_id: idWilaya,
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

                $.ajax({

                    url: "{{ url('admin/api/fetch-moughatas') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (result) {

                        

                        $.each(result.moughatas, function (key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });



                    }

                });
            }



        });
        $('#moughata').on('change', function () {

            var idMoughata = this.value;
            var idWilaya = $("#wilaya").val();

            $('#type').val(null).trigger('change');

            $("#commune").html('');

            var annee = $("#annee").val();

            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_annuel_api') }}",

                type: "POST",

                data: {

                    annee: annee,
                    wilaya_id: idWilaya,
                    moughata_id: idMoughata,

                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (result) {

                    $('#projets').html('');
                    const territoire = document.getElementById(
                        "territoire");
                    if (territoire) {
                        territoire.textContent = result.territoire;
                    }
                    if (rapport) {
                        rapport.textContent = 'Rapport ' + annee + ' de la Moughata du ' + result.territoire;
                    }
                    $.each(result.projets, function (key,
                        value) {
                        $('#projets').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })
                    $('#projets_post_fdal').html('');
                    $.each(result.projets_post_fdal, function (key,
                        value) {
                        $('#projets_post_fdal').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })

                    $('#projets_p').html('');
                    $.each(result.projets_p, function (key,
                        value) {
                        $('#projets_p').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })

                    $('#projets_passees').html('');
                    $.each(result.projets_passees, function (key,
                        value) {

                        $('#projets_passees').append(
                            `<tr><td>` + value.infra_publ + `</td><td>` +
                            value.infrastructures + `</td>
                                <td>` + value.effectif + `</td>
                                <td>` + value.nbr_cabine_rehab + `</td>
                                <td>` + value.nbr_bloc_const + `</td>
                                <td>` + value.nbr_cabine_const + `</td></tr>`
                        );
                    })



                    // Par cette nouvelle version :
                    const nb_menages_comptabilises = document.getElementById("nb_menages_comptabilises");
                    if (nb_menages_comptabilises) {
                        nb_menages_comptabilises.innerHTML = formatNumberReportPostFDAL(result.appui_nb_menages_comptabilises);
                    }
                    const appui_realise = document.getElementById("appui_realise");
                    if (appui_realise) {
                        appui_realise.innerHTML = formatNumberReportPostFDAL(result.appui_realise);
                    }
                    const taux_appui_realise = document.getElementById("taux_appui_realise");
                    if (taux_appui_realise) {
                        taux_appui_realise.innerHTML = formatTauxPostFDAL(result.appui_realise * 100 / result.appui_nb_menages_comptabilises);
                    }

                    const nb_tot_pop = document.getElementById("nb_tot_pop");
                    if (nb_tot_pop) {
                        nb_tot_pop.innerHTML = formatNumberReportPostFDAL(result.appui_nb_tot_pop);
                    }

                    const localites = document.getElementById("localites");
                    if (localites) {
                        localites.innerHTML = formatNumberReportPostFDAL(result.appui);
                    }
                    const declenches = document.getElementById("declenches");
                    if (declenches) {
                        declenches.textContent = result.declenches;
                    }
                    const redeclenches = document.getElementById("redeclenches");
                    if (redeclenches) {
                        redeclenches.textContent = result.redeclenches;
                    }
                    const population = document.getElementById("population");
                    if (population) {
                        population.textContent = result.population;
                    }
                    const menages = document.getElementById("menages");
                    if (menages) {
                        menages.textContent = result.menages;
                    }
                    const fdal = document.getElementById("fdal");
                    if (fdal) {
                        fdal.textContent = result.fdal;
                    }

                    const realise = document.getElementById("realise");
                    if (realise) {
                        realise.textContent = result.declenches != 0 ? (result.fdal * 100 /
                            result.declenches).toFixed(1) : 'NR';
                    }
                    const non_certif = document.getElementById("non_certif");
                    if (non_certif) {
                        non_certif.textContent = result.declenches < result.fdal ? 0 : result.declenches - result.fdal;
                    }


                }

            });
            
            $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {


                        annee: annee,
                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
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
            $.ajax({

                url: "{{ url('admin/api/fetch-communes') }}",

                type: "POST",

                data: {

                    moughata_id: idMoughata,

                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (res) {

                    

                    $.each(res.communes, function (key, value) {

                        $("#commune").append('<option value="' + value

                            .id + '">' + value.intitule_fr + '</option>');

                    });

                }

            });

        });


        $('#commune').on('change', function () {
            $('#type').val(null).trigger('change');

            var idMoughata = $("#moughata").val();
            var idWilaya = $("#wilaya").val();
            var idCommune = this.value;
            var annee = $("#annee").val();

            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_annuel_api') }}",

                type: "POST",

                data: {

                    annee: annee,
                    wilaya_id: idWilaya,
                    moughata_id: idMoughata,
                    commune_id: idCommune,
                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (result) {

                    $('#projets').html('');
                    const territoire = document.getElementById(
                        "territoire");
                    if (territoire) {
                        territoire.textContent = result.territoire;
                    }
                    if (rapport) {
                        rapport.textContent = 'Rapport ' + annee + ' de la commune ' + result.territoire;
                    }
                    $.each(result.projets, function (key,
                        value) {
                        $('#projets').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })
                    $('#projets_post_fdal').html('');
                    $.each(result.projets_post_fdal, function (key,
                        value) {
                        $('#projets_post_fdal').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })

                    $('#projets_p').html('');
                    $.each(result.projets_p, function (key,
                        value) {
                        $('#projets_p').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td>
                                <td></td><td>` + value.wilaya + `</td></tr>`
                        );
                    })

                    $('#projets_passees').html('');
                    $.each(result.projets_passees, function (key,
                        value) {

                        $('#projets_passees').append(
                            `<tr><td>` + value.infra_publ + `</td><td>` +
                            formatNumberReport(value.infrastructures) + `</td>
                                <td>` + formatNumberReport(value.effectif) + `</td>
                                <td>` + formatNumberReport(value.nbr_cabine_rehab) + `</td>
                                <td>` + formatNumberReport(value.nbr_bloc_const) + `</td>
                                <td>` + formatNumberReport(value.nbr_cabine_const) + `</td></tr>`
                        );
                    })



                    const nb_menages_comptabilises = document.getElementById(
                        "nb_menages_comptabilises");
                    if (nb_menages_comptabilises) {
                        nb_menages_comptabilises.textContent = formatNumberReport(result
                            .appui_nb_menages_comptabilises);
                    }
                    const appui_realise = document.getElementById(
                        "appui_realise");
                    if (appui_realise) {
                        appui_realise.textContent = formatNumberReport(result
                            .appui_realise);
                    }
                    const taux_appui_realise = document.getElementById(
                        "taux_appui_realise");
                    if (taux_appui_realise) {
                        taux_appui_realise.textContent = formatTaux(result
                            .appui_realise * 100 / result
                                .appui_nb_menages_comptabilises);
                    }


                    const nb_tot_pop = document.getElementById("nb_tot_pop");
                    if (nb_tot_pop) {
                        nb_tot_pop.textContent = formatNumberReport(result.appui_nb_tot_pop);
                    }

                    const localites = document.getElementById("localites");
                    if (localites) {
                        localites.textContent = formatNumberReport(result.appui);
                    }
                    const declenches = document.getElementById("declenches");
                    if (declenches) {
                        declenches.textContent = formatNumberReport(result.declenches);
                    }
                    const redeclenches = document.getElementById("redeclenches");
                    if (redeclenches) {
                        redeclenches.textContent = formatNumberReport(result.redeclenches);
                    }
                    const population = document.getElementById("population");
                    if (population) {
                        population.textContent = formatNumberReport(result.population);
                    }
                    const menages = document.getElementById("menages");
                    if (menages) {
                        menages.textContent = formatNumberReport(result.menages);
                    }
                    const fdal = document.getElementById("fdal");
                    if (fdal) {
                        fdal.textContent = formatNumberReport(result.fdal);
                    }

                    const realise = document.getElementById("realise");
                    if (realise) {
                        realise.textContent = formatNumberReport(result.realise);
                    }
                    const non_certif = document.getElementById("non_certif");
                    if (non_certif) {
                        non_certif.textContent = result.declenches < result.fdal ? 0 : result.declenches - result.fdal;
                    }



                }

            });
            $.ajax({

                    url: "{{ url('admin/maps/fetch-maps') }}",

                    type: "POST",

                    data: {


                        annee: annee,
                        wilaya_id: idWilaya,
                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        
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

@endpush
