@extends('layouts.admin')
@section('title')
@lang('sidebar.reporting')
@endsection
@section('contentdeader')
@lang('sidebar.reporting')
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
<style>
    .cell-grey {
        background-color: rgb(92, 92, 92);
    }

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
    }

    /* Mise en gras pour les en-têtes de tableau */
    .table-element th {
        font-weight: bold;
        background-color: #f8f9fa;
    }

    /* Alignement des chiffres à droite */
    .table-element td:not(:first-child),
    .table-element th:not(:first-child) {
        text-align: right;
        padding: 8px 12px;
    }

    /* Alignement à gauche pour la première colonne (texte) */
    .table-element td:first-child,
    .table-element th:first-child {
        text-align: left;
        padding: 8px 12px;
    }

    /* Style pour les cellules grises */
    .cell-grey {
        background-color: #f0f0f0;
        text-align: center;
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
@endpush
@section('content')
<div class="card">
    <div class="card-header">
        <p class="card-title card_title_center">@lang('general.filter')</p>
    </div>

    <div class="card-body">
        <div class="row">

            <!-- 🟦 Wilaya -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="wilaya" class="form-label fw-bold">Wilaya</label>
                    
                    <select name="wilaya" id="wilaya" class="form-control wilaya">
                        <option value=""></option>
                        @foreach ($wilayas as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🟩 Moughataa -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="moughata" class="form-label fw-bold">Moughataa</label>
                    <select name="moughata" id="moughata" class="form-control moughata">
                    </select>
                </div>
            </div>

            <!-- 🟧 Commune -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="commune" class="form-label fw-bold">Commune</label>
                    <select name="commune" id="commune" class="form-control commune">

                    </select>
                </div>
            </div>

            <!-- 🟥 Localité -->
            <div class="col-md-3">
                <div class="form-group">
                    <label for="localite" class="form-label fw-bold">Localité</label>
                    <select name="localite" id="localite" class="form-control localite">
                    </select>
                </div>
            </div>

            <!-- 🟨 Type d'assainissement -->
            <div class="col-md-6">
                <div class="form-group mt-2">
                    <label for="type" class="form-label fw-bold">Type d'assainissement</label>
                    <select name="type" id="type" class="form-control type">
                        <option value="deux">Assainissement familial et public</option>
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
                                <td>Formation sanitaire</td>
                                <td id="info_sante"></td>
                            </tr>


                            <tr>
                                <td>Date de la requête</td>
                                <td id="date">{{ date('d/m/Y') }}</td>
                            </tr>


                        </tbody>
                    </table>
                    <h5>ACCES A L'ASSAINISSEMENT ET L'HYGIENE DES MENAGES</h5>

                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <td>Source d'approvisionnement en eau de la localité</td>
                            <td id="approv_eau"></td>


                        </tr>
                    </table>

                    <h6>Projet ayant mis en œuvre l'ATPC / Suivi post-FDAL </h6>

                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Intitulé du projet</th>
                            <th>Opérateur</th>
                            <th>Période </th>

                        </tr>
                        <tbody id="projets_atcp">





                        </tbody>
                    </table>
                    <h6>Historique de la mise en œuvre de l'ATPC / Suivi post-FDAL</h6>
                    <table class="table table-responsive mb-4 table-element" border="3">
                        <tr>
                            <th>Etapes de la mise en œuvre de l'ATPC et du suivi post-FDAL</th>
                            <th>Date</th>
                            <th>Pop.</th>
                            <th>Nombre de ménages</th>
                            <th>Nombre de ménages avec latrines</th>
                            <th>Taux de couverture en latrines</th>

                            <th>Nombre latrines construites</th>
                            <th>Nombre ménages DAL</th>
                            <th>Taux ménages DAL</th>


                        </tr>
                        <tbody id="operations">
                        </tbody>
                    </table>
                    <h6>Financement des latrines familiales</h6>


                    <table class="mb-4 table-element" border="3">
                        <tr>

                            <td>Indicateur</td>
                            <td></td>


                        </tr>
                        <tbody id="financement">
                            <tr>
                                <td>Taux de latrines auto-financées</td>
                                <td id="auto_finance"></td>
                            </tr>
                            <tr>
                                <td>Nombre de latrines réalisées pour lesquelles les données d'investissement ont été
                                    recueillies</td>
                                <td id="nb_menages_inv"></td>
                            </tr>
                            <tr>
                                <td>Montant total investi par les ménages dans leurs latrines (MRU)</td>
                                <td id="investi"></td>
                            </tr>
                            <tr>
                                <td>Montant moyen investi par les ménages dans une latrine(MRU)</td>
                                <td id="investi_menages"></td>
                            </tr>
                            <tr>
                                <td>Taux de latrines financées par entraide communautaire</td>
                                <td id="latrines_entrainde"></td>
                            </tr>
                            <tr>
                                <td>Taux de latrines subventionnées</td>
                                <td id="latrines_subv"></td>
                            </tr>
                        </tbody>
                    </table>
                    <h6>Accès des ménages à l'assainissement (résultats du dernier suivi)</h6>
                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Classification selon les Objectifs de Développement Durable</th>
                            <th>Taux</th>


                        </tr>

                        <tbody>

                            <tr>
                                <td>Accès à un service d'assainissement sécurisé</td>
                                <td id="securise"></td>


                            </tr>
                            <tr>

                                <td>Accès à un service d'assainissement basique (latrines améliorées)</td>
                                <td id="basique"></td>
                            </tr>
                            <tr>
                                <td>Accès à un service d'assainissement limité (latrines partagées)</td>
                                <td id="limite"></td>


                            </tr>
                            <tr>
                                <td>Accès à un service d'assainissement non amélioré</td>
                                <td id="non_amelior"></td>
                            </tr>
                            <tr>
                                <td>Taux de ménages déféquant à l'air libre</td>
                                <td id="dal"></td>
                            </tr>








                        </tbody>
                    </table>

                    <h6>Accès des ménages à l'hygiène (résultats du dernier suivi)</h6>

                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Classification selon les Objectifs de Développement Durable</th>
                            <th>Taux</th>


                        </tr>

                        <tbody>

                            <tr>
                                <td>Latrines associées à un DLM fonctionnel</td>
                                <td id="hygiene_DLM"></td>


                            </tr>
                            <tr>

                                <td>Accès à un service d'hygiène de base (eau + savon)</td>
                                <td id="hygiene_base"></td>
                            </tr>
                            <tr>
                                <td>Accès à un service d'hygiène limité (eau ou savon)</td>
                                <td id="hygiene_limite"></td>


                            </tr>
                            <tr>
                                <td>Accès à aucun service</td>
                                <td id="hygiene_aucun_service"></td>
                            </tr>


                        </tbody>
                    </table>

                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div></div>
    <div id="public">
        <div class="row">
            <div class="col-12">
            <div class="card">

                <!-- /.card-header -->
                <div class="card-body">
        <div id="infrastructure_ecole">
            <h5>ACCES A L'EAU, L'ASSAINISSEMENT ET L'HYGIENE AU NIVEAU DE L'ECOLE FONDAMENTALE</h5>
            <div class="sante-container mb-5">
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nom de l'école</td>
                            <td id="nom_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Code MEN</td>
                            <td id="code_men_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Date du dernier inventaire</td>
                            <td id="date_inventaire_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Source d'approvisionnement en eau</td>
                            <td id="approv_eau_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Distance du site</td>
                            <td id="dist_approv_ecole">NR</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Situation de l'accès à l'assainissement de l'école</h5>
                <table class="mb-4 table-element" border="3">
                    <tr>
                        <th>Indicateur</th>
                        <th>Réponse</th>
                    </tr>
                    <tbody>
                        <tr>
                            <td>Nombre d'élèves (garçons)</td>
                            <td id="effectif_garcon_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre d'élèves (filles)</td>
                            <td id="effectif_fille_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Effectif total</td>
                            <td id="effectif_total_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre de blocs de latrines</td>
                            <td id="nbr_bloc_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre de cabines existantes</td>
                            <td id="nbr_cabine_exist_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre de cabines fonctionnelles</td>
                            <td id="nbr_cabine_fonct_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Ratio (Nombre d'élèves par cabine)</td>
                            <td id="ratio_eleves_cabine_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Besoins satisfaits</td>
                            <td id="besoin_latr_satisf_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Cabines filles/garçons bien distinctes</td>
                            <td id="dist_cab_hom_fem_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Cabines filles permettant une bonne gestion de l'hygiène menstruelle</td>
                            <td id="nbr_cabine_fem_hyg_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Au moins une cabine accessible aux PMR</td>
                            <td id="nbr_cabine_pmr_ecole">NR</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Besoins en assainissement de l'école</h5>
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nb de cabines à réaliser</td>
                            <td id="nbr_cabine_a_const_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Nb de cabines à réhabiliter</td>
                            <td id="nbr_cabine_a_rehab_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Programmation des travaux</td>
                            <td id="an_program_ecole">NR</td>
                        </tr>
                    </tbody>
                </table>

                <div class="projets-passes-ecole">
                    <h5>Projets passés de réalisation de latrines</h5>
                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Intitulé du projet</th>
                            <th>Opérateur</th>
                            <th>Période</th>
                            <th>Nombre de cabines réalisées</th>
                            <th>Nombre de cabines réhabilitées</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>Accès à l'hygiène de l'école</h5>
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nombre de dispositif de lave-mains en place</td>
                            <td id="nb_dlm_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Accès à un service d'hygiène de base (installation destinée au lavage des
                                mains muni d'eau et savon)</td>
                            <td id="nb_dlm_complet_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Accès à un service d'hygiène limité (installation destinée au lavage des
                                mains dépourvu d'eau et/ou savon)</td>
                            <td id="nb_dlm_incomplet_ecole">NR</td>
                        </tr>
                        <tr>
                            <td>Aucun service (aucune installation destinée au lavage des mains)</td>
                            <td id="nb_sans_dlm_ecole">NR</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="infrastructure_sante">
            <h5>ACCES A L'EAU, L'ASSAINISSEMENT ET L'HYGIENE AU NIVEAU DE LA Formation sanitaire</h5>
            <div class="ecole-container mb-5">
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nom de la Formation sanitaire</td>
                            <td id="nom_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Date du dernier inventaire</td>
                            <td id="date_inventaire_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Source d'approvisionnement en eau</td>
                            <td id="approv_eau_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Distance du site</td>
                            <td id="dist_approv_sante">NR</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Situation de l'accès à l'assainissement de la Formation sanitaire</h5>
                <table class="mb-4 table-element" border="3">
                    <tr>
                        <th>Indicateur</th>
                        <th>Réponse</th>
                    </tr>
                    <tbody>
                        <tr>
                            <td>Nombre de blocs de latrines</td>
                            <td id="nbr_bloc_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre de cabines existantes</td>
                            <td id="nbr_cabine_exist_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Nombre de cabines fonctionnelles</td>
                            <td id="nbr_cabine_fonct_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Besoins satisfaits</td>
                            <td id="besoin_latr_satisf_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Cabines filles/garçons bien distinctes</td>
                            <td id="dist_cab_hom_fem_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Cabines filles permettant une bonne gestion de l'hygiène menstruelle</td>
                            <td id="nbr_cabine_fem_hyg_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Au moins une cabine accessible aux PMR</td>
                            <td id="nbr_cabine_pmr_sante">NR</td>
                        </tr>
                    </tbody>
                </table>

                <h5>Besoins en assainissement de la Formation sanitaire</h5>
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nb de cabines à réaliser</td>
                            <td id="nbr_cabine_a_const_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Nb de cabines à réhabiliter</td>
                            <td id="nbr_cabine_a_rehab_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Programmation des travaux</td>
                            <td id="an_program_sante">NR</td>
                        </tr>
                    </tbody>
                </table>

                <div class="projets-passes-sante">
                    <h5>Projets passés de réalisation de latrines</h5>
                    <table class="mb-4 table-element" border="3">
                        <tr>
                            <th>Intitulé du projet</th>
                            <th>Opérateur</th>
                            <th>Période</th>
                            <th>Nombre de cabines réalisées</th>
                            <th>Nombre de cabines réhabilitées</th>
                        </tr>
                        <tbody>
                            <tr>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                                <td>NR</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h5>Accès à l'hygiène de la Formation sanitaire</h5>
                <table class="mb-4 table-element" border="3">
                    <tbody>
                        <tr>
                            <td>Nombre de dispositif de lave-mains en place</td>
                            <td id="nb_dlm_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Accès à un service d'hygiène de base (installation destinée au lavage des
                                mains muni d'eau et savon)</td>
                            <td id="nb_dlm_complet_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Accès à un service d'hygiène limité (installation destinée au lavage des
                                mains dépourvu d'eau et/ou savon)</td>
                            <td id="nb_dlm_incomplet_sante">NR</td>
                        </tr>
                        <tr>
                            <td>Aucun service (aucune installation destinée au lavage des mains)</td>
                            <td id="nb_sans_dlm_sante">NR</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        </div>
        </div>
        
    </div>
    <!-- /.col -->
</div>

</div>
@endsection
@push('script')
<script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/html2pdf.bundle.min.js') }}"></script>
<script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/dist/js/functions.js') }}"></script>
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
        /* Styles communs - SANS !important */
        body {
            font-family: "Arial", sans-serif;
            font-size: 18px;
            line-height: 1.4;
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
        
        /* Styles de base pour les titres - SANS !important */
        h4 {
            text-align: center;
            margin: 25px 0;
            font-size: 22px;
            color: #333;
            font-weight: bold;
        }
        
        h5 {
            background-color: #00A95C;
            color: white;
            padding: 12px;
            margin: 30px 0 20px 0;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
        
        h6 {
            background-color: #FFD700;
            color: #000;
            padding: 10px;
            margin: 25px 0 15px 0;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
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
        
        /* Styles d'impression - AVEC !important pour surcharger */
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
            
            /* STYLES DE TITRES POUR L'IMPRESSION - AVEC !important */
            h4 {
                text-align: center !important;
                font-size: 26px !important; /* Augmenté à 26px */
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
                font-size: 24px !important; /* Augmenté à 24px */
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
                font-size: 22px !important; /* Augmenté à 22px */
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
        
        <div class="print-footer no-print">
            <p>Document généré par le Système PIAM - Direction de l'Assainissement</p>
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
                
                // Créer un div temporaire pour le contenu PDF
                const tempDiv = document.createElement('div');
                tempDiv.style.position = 'absolute';
                tempDiv.style.left = '-9999px';
                tempDiv.style.top = '0';
                tempDiv.style.width = '794px';
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
                
                // Ajouter un en-tête au contenu
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
            
            // Assurer que les titres sont visibles
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


        // Parse the number to ensure it's treated as a number
        number = parseFloat(number);
        // Parse the int to ensure it's treated as a int
        decimals = parseInt(decimals);

        // Validate inputs
        if (isNaN(number) || !isFinite(number)) {
            number = 0;
        }


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


        // Parse the number to ensure it's treated as a number
        number = parseFloat(number);
        // Parse the int to ensure it's treated as a int
        decimals = parseInt(decimals);


        // Validate inputs
        if (isNaN(number) || !isFinite(number)) {
            number = 0;
        }


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

        $('#content').hide();


        //Initialize Select2 Elements
        $('.wilaya').select2()
        $('.moughata').select2()
        $('.commune').select2()
        $('.localite').select2()
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
        $('#wilaya').on('change', function () {

            var idWilaya = this.value;

            $("#moughata").html('');
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

                            .id + '">' + value.intitule_fr + '</option>');

                    });


                }

            });

        });


        $('#commune').on('change', function () {

            var idMoughata = $("#moughata").val();
            var idWilaya = $("#wilaya").val();
            var idCommune = this.value;
            $("#localite").html('');

            $.ajax({

                url: "{{ url('admin/api/fetch-localites') }}",

                type: "POST",

                data: {

                    wilaya_id: idWilaya,
                    moughata_id: idMoughata,
                    commune_id: idCommune,
                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (res) {

                    $('#localite').html('<option value="">-- Select Localite --</option>');

                    $.each(res.localites, function (key, value) {

                        $("#localite").append('<option value="' + value

                            .id + '">' + value.intitule_fr + '</option>');

                    });

                }

            });


        });

        $('#localite').on('change', function () {

            var idMoughata = $("#moughata").val();
            var idWilaya = $("#wilaya").val();
            var idCommune = $("#commune").val();
            var idLocalite = this.value;
            $('#content').show();

            $.ajax({

                url: "{{ url('admin/reporting/fetch_reporting_localite_api') }}",

                type: "POST",

                data: {

                    wilaya_id: idWilaya,
                    moughata_id: idMoughata,
                    commune_id: idCommune,
                    localite_id: idLocalite,
                    _token: '{{ csrf_token() }}'

                },

                dataType: 'json',

                success: function (res) {


                    if (res.info_ecoles.length == 0) {
                        $('#infrastructure_ecole').show();
                    } else {
                        $('#infrastructure_ecole').show();
                        $('#infrastructure_ecole .ecole-container').remove(); // Supprimer les anciens contenus

                        // Parcourir toutes les écoles avec each()
                        $.each(res.info_ecoles, function (index, ecole) {
                            const ecoleHtml = `
                                <div class="ecole-container mb-5">
                                    <h6>École ${index + 1}</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nom de l'école</td>
                                                <td>${ecole.intitule_infra_publ || 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Code MEN</td>
                                                <td>${formatNumberReport(ecole.code_men)}</td>
                                            </tr>
                                            <tr>
                                                <td>Date du dernier inventaire</td>
                                                <td>${ecole.date_op ? moment(ecole.date_op, "YYYY-MM-DD").format("DD/MM/YYYY") : 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Source d'approvisionnement en eau</td>
                                                <td>${ecole.approv_eau || 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Distance du site</td>
                                                <td>${formatNumberReport(ecole.dist_approv_eau)} m</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <h6>Situation de l'accès à l'assainissement de l'école</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Réponse</th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>Nombre d'élèves (garçons)</td>
                                                <td>${formatNumberReport(ecole.effectif_garcon)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre d'élèves (filles)</td>
                                                <td>${formatNumberReport(ecole.effectif_fille)}</td>
                                            </tr>
                                            <tr>
                                                <td>Effectif total</td>
                                                <td>${formatNumberReport(ecole.effectif)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de blocs de latrines</td>
                                                <td>${formatNumberReport(ecole.nbr_bloc)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de cabines existantes</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_exist)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de cabines fonctionnelles</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_fonct)}</td>
                                            </tr>
                                            <tr>
                                                <td>Ratio (Nombre d'élèves par cabine)</td>
                                                <td>${formatNumberReport(ecole.effectif / ecole.nbr_cabine_fonct)}</td>
                                            </tr>
                                            <tr>
                                                <td>Besoins satisfaits</td>
                                                <td>${formatNumberReport(ecole.besoin_latr_satisf)}</td>
                                            </tr>
                                            <tr>
                                                <td>Cabines filles/garçons bien distinctes</td>
                                                <td>${formatNumberReport(ecole.dist_cab_hom_fem)}</td>
                                            </tr>
                                            <tr>
                                                <td>Cabines filles permettant une bonne gestion de l'hygiène menstruelle</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_fem_hyg)}</td>
                                            </tr>
                                            <tr>
                                                <td>Au moins une cabine accessible aux PMR</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_pmr)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <h6>Besoins en assainissement de l'école</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nb de cabines à réaliser</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_a_const)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nb de cabines à réhabiliter</td>
                                                <td>${formatNumberReport(ecole.nbr_cabine_a_rehab)}</td>
                                            </tr>
                                            <tr>
                                                <td>Programmation des travaux</td>
                                                <td>${ecole.an_program !== 0 ? ecole.an_program : 'NR'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="projets-passes-ecole">
                                        ${genererProjetsPasses(ecole)}
                                    </div>
                                    
                                    <h6>Accès à l'hygiène de l'école</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nombre de dispositif de lave-mains en place</td>
                                                <td>${formatNumberReport(ecole.nb_dlm)}</td>
                                            </tr>
                                            <tr>
                                                <td>Accès à un service d'hygiène de base</td>
                                                <td>${formatNumberReport(ecole.nb_dlm_complet)}</td>
                                            </tr>
                                            <tr>
                                                <td>Accès à un service d'hygiène limité</td>
                                                <td>${formatNumberReport(ecole.nb_dlm_incomplet)}</td>
                                            </tr>
                                            <tr>
                                                <td>Aucun service</td>
                                                <td>${formatNumberReport(ecole.nb_sans_dlm)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            $('#infrastructure_ecole').append(ecoleHtml);
                        });
                    }

                    if (res.info_santes.length == 0) {
                        $('#infrastructure_sante').show();
                    } else {
                        $('#infrastructure_sante').show();
                        $('#infrastructure_sante .sante-container').remove(); // Supprimer les anciens contenus

                        // Parcourir toutes les formations sanitaires avec each()
                        $.each(res.info_santes, function (index, sante) {
                            const santeHtml = `
                                <div class="sante-container mb-5">
                                    <h6>Formation sanitaire ${index + 1}</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nom de la Formation sanitaire</td>
                                                <td>${sante.intitule_infra_publ || 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Date du dernier inventaire</td>
                                                <td>${sante.date_op ? moment(sante.date_op, "YYYY-MM-DD").format("DD/MM/YYYY") : 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Source d'approvisionnement en eau</td>
                                                <td>${sante.approv_eau || 'NR'}</td>
                                            </tr>
                                            <tr>
                                                <td>Distance du site</td>
                                                <td>${formatNumberReport(sante.dist_approv_eau)} m</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <h6>Situation de l'accès à l'assainissement de la Formation sanitaire</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tr>
                                            <th>Indicateur</th>
                                            <th>Réponse</th>
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>Nombre de blocs de latrines</td>
                                                <td>${formatNumberReport(sante.nbr_bloc)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de cabines existantes</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_exist)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nombre de cabines fonctionnelles</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_fonct)}</td>
                                            </tr>
                                            <tr>
                                                <td>Besoins satisfaits</td>
                                                <td>${formatNumberReport(sante.besoin_latr_satisf)}</td>
                                            </tr>
                                            <tr>
                                                <td>Cabines filles/garçons bien distinctes</td>
                                                <td>${formatNumberReport(sante.dist_cab_hom_fem)}</td>
                                            </tr>
                                            <tr>
                                                <td>Cabines filles permettant une bonne gestion de l'hygiène menstruelle</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_fem_hyg)}</td>
                                            </tr>
                                            <tr>
                                                <td>Au moins une cabine accessible aux PMR</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_pmr)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <h6>Besoins en assainissement de la Formation sanitaire</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nb de cabines à réaliser</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_a_const)}</td>
                                            </tr>
                                            <tr>
                                                <td>Nb de cabines à réhabiliter</td>
                                                <td>${formatNumberReport(sante.nbr_cabine_a_rehab)}</td>
                                            </tr>
                                            <tr>
                                                <td>Programmation des travaux</td>
                                                <td>${sante.an_program !== 0 ? sante.an_program : 'NR'}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="projets-passes-sante">
                                        ${genererProjetsPasses(sante)}
                                    </div>
                                    
                                    <h6>Accès à l'hygiène de la Formation sanitaire</h6>
                                    <table class="mb-4 table-element" border="3">
                                        <tbody>
                                            <tr>
                                                <td>Nombre de dispositif de lave-mains en place</td>
                                                <td>${formatNumberReport(sante.nb_dlm)}</td>
                                            </tr>
                                            <tr>
                                                <td>Accès à un service d'hygiène de base</td>
                                                <td>${formatNumberReport(sante.nb_dlm_complet)}</td>
                                            </tr>
                                            <tr>
                                                <td>Accès à un service d'hygiène limité</td>
                                                <td>${formatNumberReport(sante.nb_dlm_incomplet)}</td>
                                            </tr>
                                            <tr>
                                                <td>Aucun service</td>
                                                <td>${formatNumberReport(sante.nb_sans_dlm)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            `;

                            $('#infrastructure_sante').append(santeHtml);
                        });
                    }

                    // Fonction helper pour générer les projets passés
                    function genererProjetsPasses(etablissement) {
                        let projetsHtml = '';
                        for (const key in etablissement) {
                            const element = etablissement[key];
                            if (typeof element === 'object' && element !== null) {
                                projetsHtml += `<h6>Projets passés de réalisation de latrines</h6>`;
                                projetsHtml += `
                                    <table class="mb-4 table-element" border="3">
                                        <tr>
                                            <th>Intitulé du projet</th>
                                            <th>Opérateur</th>
                                            <th>Période</th>
                                            <th>Nombre de cabines réalisées</th>
                                            <th>Nombre de cabines réhabilitées</th>    
                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td>${element.intitule_resume || 'NR'}</td>
                                                <td>${element.intitule || 'NR'}</td>
                                                <td>${element.year || 'NR'}</td>
                                                <td>${formatNumberReport(element.nbr_cabine_a_const)}</td>
                                                <td>${formatNumberReport(element.nbr_cabine_a_rehab)}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                `;
                            }
                        }
                        return projetsHtml || '<p>Aucun projet passé</p>';
                    }



                    const info_localite = document.getElementById(
                        "info_localite");
                    if (info_localite) {
                        info_localite.textContent = res
                            .info.intitule;
                    }
                    const info_code_ANSADE = document.getElementById(
                        "info_code_ANSADE");
                    if (info_code_ANSADE) {
                        info_code_ANSADE.textContent = res
                            .info.code_ansade;
                    }

                    const rapport = document.getElementById(
                        "rapport");
                    if (rapport) {
                        rapport.textContent = 'Rapport de la localité de ' + res.info.intitule;
                    }
                    const info_commune = document.getElementById(
                        "info_commune");
                    if (info_commune) {
                        info_commune.textContent = res
                            .info.commune;
                    }
                    const info_moughata = document.getElementById(
                        "info_moughata");
                    if (info_moughata) {
                        info_moughata.textContent = res
                            .info.moughata;
                    }
                    const info_wilaya = document.getElementById(
                        "info_wilaya");
                    if (info_wilaya) {
                        info_wilaya.textContent = res
                            .info.wilaya;
                    }
                    const hygiene_aucun_service = document.getElementById(
                        "hygiene_aucun_service");
                    if (hygiene_aucun_service) {
                        hygiene_aucun_service.textContent = formatNumberReport(res
                            .nbr_menages_sans_dlm);
                    }

                    const hygiene_limite = document.getElementById("hygiene_limite");
                    if (hygiene_limite) {
                        hygiene_limite.textContent = formatNumberReport(res
                            .nbr_menages_dlm_incomplet);
                    }
                    const hygiene_base = document.getElementById("hygiene_base");
                    if (hygiene_base) {
                        hygiene_base.textContent = formatNumberReport(res
                            .nbr_menages_dlm_complet);
                    }
                    const hygiene_DLM = document.getElementById("hygiene_DLM");
                    if (hygiene_DLM) {
                        hygiene_DLM.textContent = formatTaux(res
                            .nbr_menages_dlm * 100 / res

                                .nb_menages_comptabilises);
                    }

                    const dal = document.getElementById("dal");
                    if (dal) {
                        dal.textContent = formatTaux(res.nbr_menages_dal
                            * 100 / res
                                .nb_menages_comptabilises);
                    }

                    const non_amelior = document.getElementById("non_amelior");
                    if (non_amelior) {
                        non_amelior.textContent = formatTaux(res.non_amelior
                            * 100 / res
                                .nb_menages_comptabilises);
                    }

                    const limite = document.getElementById("limite");
                    if (limite) {
                        limite.textContent = formatNumberReport(res
                            .limite * 100 / res
                                .nb_menages_comptabilises);
                    }
                    const basique = document.getElementById("basique");
                    let basique_value = 100 - (res
                        .limite * 100 / res
                            .nb_menages_comptabilises) - (res.securise
                                * 100 / res
                                    .nb_menages_comptabilises) - (res.non_amelior
                                        * 100 / res
                                            .nb_menages_comptabilises) - (res.nbr_menages_dal
                                                * 100 / res
                                                    .nb_menages_comptabilises);
                    if (basique) {
                        basique.textContent = formatTaux(basique_value);
                    }
                    const securise = document.getElementById("securise");
                    if (securise) {
                        securise.textContent = formatTaux(res.securise
                            * 100 / res
                                .nb_menages_comptabilises);
                    }

                    const auto_finance = document.getElementById("auto_finance");
                    if (auto_finance) {
                        auto_finance.textContent = formatTaux(res
                            .auto_finance);
                    }

                    const nb_menages_inv = document.getElementById("nb_menages_inv");
                    if (nb_menages_inv) {
                        nb_menages_inv.textContent = formatNumberReport(res
                            .nb_menages_inv);
                    }
                    const investi = document.getElementById("investi");
                    if (investi) {
                        investi.textContent = formatNumberReport(res
                            .investi);
                    }

                    const investi_menages = document.getElementById("investi_menages");
                    if (investi_menages) {
                        investi_menages.textContent = formatNumberReport(res
                            .investi_menages);
                    }
                    const nombreAvecSeparateur = formatNumberReport(res
                        .investi_menages);


                    const latrines_entrainde = document.getElementById(
                        "latrines_entrainde");
                    if (latrines_entrainde) {
                        latrines_entrainde.textContent = formatTaux(res
                            .latrines_entrainde);
                    }
                    const latrines_subv = document.getElementById("latrines_subv");
                    if (latrines_subv) {
                        latrines_subv.textContent = formatTaux(res
                            .latrines_subv);
                    }




                    $('#projets_atcp').html('');
                    const projets_atcp = res.projets_atcp;

                    const mergedMap = new Map();

                    projets_atcp.forEach((item) => {
                        const key = `${item.intitule_resume}-${item.intitule}`;
                        if (mergedMap.has(key)) {
                            // If key exists, update the year range
                            const existingItem = mergedMap.get(key);
                            existingItem.year = `${existingItem.year}-${item.year}`;
                        } else {
                            // Otherwise, add a new entry
                            mergedMap.set(key, {
                                ...item
                            });
                        }
                    });

                    // Convert the map values to an array
                    const projetsAtcp = Array.from(mergedMap.values());



                    $.each(projetsAtcp, function (key,
                        value) {

                        $('#projets_atcp').append(
                            `<tr><td>` + value.intitule_resume + `</td><td>` +
                            value.intitule + `</td><td>` +
                            value.year + `</td>
                                
                                </tr>`
                        );
                    })

                    $('#operations').html('');
                    var nb_menages_comptabilises = 0;
                    var nb_tot_latr_fam = 0;
                    $.each(res.operations, function (key,
                        value) {

                        if (value.intitule === 'Etat des lieux') {
                            nb_menages_comptabilises = value
                                .nb_menages_comptabilises;
                            nb_tot_latr_fam = value
                                .nb_tot_latr_fam;
                        }
                        if (value.intitule === 'Etat des lieux') {
                            $('#operations').append(
                                `<tr><td>` + value.intitule + `</td><td>` +
                                moment(value.date_op, "YYYY-MM-DD").format(
                                    "DD/MM/YYYY") + `</td>
                                <td>` + value.nb_tot_pop + `</td>
                                <td>` + value.nb_menages_comptabilises + `</td>
                                <td>` + value.nb_tot_latr_fam + `</td>
                                <td>` + formatTaux(value.nb_tot_latr_fam * 100 /
                                        nb_menages_comptabilises) +
                                `</td>
                                <td bgcolor="gray"></td>
                                <td>` + formatNumberReport(value.nbr_menages_dal) + `</td>
                                <td>` + formatTaux(value.nbr_menages_dal * 100 /
                                    nb_menages_comptabilises) + `</td>
                                </tr>`
                            );
                        } else if (value
                            .intitule === 'Dernier suivi') {
                            $('#operations').append(
                                `<tr><td>` + value.intitule + `</td><td>` +
                                moment(value.date_op, "YYYY-MM-DD").format(
                                    "DD/MM/YYYY") + `</td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td>` + formatNumberReport(value.nb_tot_latr_fam) + `</td>
                                <td>` + formatTaux(value.nb_tot_latr_fam * 100 /
                                        nb_menages_comptabilises) +
                                `</td>
                                <td>` + formatNumberReport(value.nb_tot_latr_fam - nb_tot_latr_fam) + `</td>
                                <td>` + formatNumberReport(value.nbr_menages_dal) + `</td>
                                <td>` + formatTaux(value.nbr_menages_dal * 100 /
                                    nb_menages_comptabilises) + `</td>
                                </tr>`
                            );
                        } else {

                            $('#operations').append(
                                `<tr><td>` + value.intitule + `</td><td>` +
                                moment(value.date_op, "YYYY-MM-DD").format(
                                    "DD/MM/YYYY") + `</td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                <td bgcolor="gray"></td>
                                </tr>`
                            );
                        }

                    })
                    const approv_eau = document.getElementById("approv_eau");
                    if (approv_eau) {
                        approv_eau.textContent = res
                            .approv;
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

@endpush
