@extends('layouts.admin')
@section('title')
    @lang('sidebar.marches')
@endsection
@section('contentheader')
    @lang('sidebar.marches')
@endsection
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Détails du Marché</h1>
            </div>
            <div class="col-sm-6">
                <a href="{{ route('marches.index') }}" class="btn btn-secondary float-right">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Informations Générales</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>N° Marché:</th>
                                <td>{{ $marche->num ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Lot:</th>
                                <td>{{ $marche->lot ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date signature:</th>
                                <td>
                                    @if($marche->date_sign)
                                        {{ \Carbon\Carbon::parse($marche->date_sign)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Durée (mois):</th>
                                <td>{{ $marche->duree ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Montant (MRU):</th>
                                <td>
                                    @if($marche->montant)
                                        {{ number_format($marche->montant, 0, ',', ' ') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Projet & Opérateur</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Projet:</th>
                                <td>
                                    @if($marche->projet)
                                        {{ $marche->projet->intitule_resume ?? $marche->projet->intitule_resume_fr ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Opérateur:</th>
                                <td>
                                    @if($marche->operateur)
                                        {{ $marche->operateur->intitule ?? $marche->operateur->intitule_fr ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Type opérateur:</th>
                                <td>{{ $marche->operateur->type_struct ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Indicateurs</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Localités à déclencher:</th>
                                <td>{{ $marche->nb_loc_adecl ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Localités à suivre:</th>
                                <td>{{ $marche->nb_loc_asuiv_postfdal ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Latrines familiales:</th>
                                <td>{{ $marche->nb_latfam_asubv ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Cabines à réaliser:</th>
                                <td>{{ $marche->nb_cab_a_const ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Cabines à réhabiliter:</th>
                                <td>{{ $marche->nb_cab_a_rehab ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">Localisation & Financement</h3>
                    </div>
                    <div class="card-body">
                        <h5>Wilayas ciblées:</h5>
                        @if($marche->wilayas && $marche->wilayas->count() > 0)
                            @foreach($marche->wilayas as $wilaya)
                                <span class="badge bg-primary mr-1 mb-1">
                                    {{ $wilaya->intitule ?? $wilaya->intitule_fr ?? 'N/A' }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">Aucune wilaya spécifiée</span>
                        @endif

                        <h5 class="mt-3">Partenaires financiers:</h5>
                        @if($marche->partenaires && $marche->partenaires->count() > 0)
                            @foreach($marche->partenaires as $partenaire)
                                <span class="badge bg-success mr-1 mb-1">
                                    {{ $partenaire->intitule ?? $partenaire->intitule_fr ?? 'N/A' }}
                                </span>
                            @endforeach
                        @else
                            <span class="text-muted">Aucun partenaire spécifié</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Section détaillée du projet -->
        @if($marche->projet)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Détails du Projet</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Intitulé complet:</th>
                                        <td>{{ $marche->projet->intitule_complet ?? $marche->projet->intitule_complet_fr ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Période:</th>
                                        <td>{{ $marche->projet->periode ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Milieu cible:</th>
                                        <td>{{ $marche->projet->milieu_cible ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Code:</th>
                                        <td>{{ $marche->projet->code ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection