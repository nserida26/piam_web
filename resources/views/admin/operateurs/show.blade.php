@extends('layouts.admin')
@section('title')
    @lang('sidebar.operators')
@endsection
@section('contentheader')
    @lang('sidebar.operators')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/operateurs') }}"> @lang('sidebar.operators') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.operators')
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3>{{ $operateur->intitule }}  / {{ $projet->intitule_resume }} </h3></div>
                    <div class="card-body">

                        <a href="{{ url('admin/operateurs') }}">
                            <button class="btn btn-warning btn-sm">
                                <i class="fa fa-arrow-left"></i> {{ __('marche.back') }}
                            </button>
                        </a>

                        <a href="{{ url('admin/operateurs/' . $operateur->id . '/edit') }}">
                            <button class="btn btn-primary btn-sm">
                                <i class="fa fa-pencil-square-o"></i> {{ __('marche.edit') }}
                            </button>
                        </a>

                        <br/><br/>

                        <!-- DONNÉES FACTORISABLES EN TÊTE -->
                        <!-- Localisation & Financement factorisés -->
                        {{--<div class="row mb-4">
                            <!-- Informations de l'opérateur -->
                            <div class="col-md-6">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('marche.operator_info') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>{{ __('marche.title') }}:</th>
                                                <td>{{ $operateur->intitule }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('marche.title_fr') }}:</th>
                                                <td>{{ $operateur->intitule_fr }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('marche.type_struct') }}:</th>
                                                <td>{{ $operateur->type_struct }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Projets liés à cet opérateur (si existants) -->
                            @php
                                $projets = collect();
                                $wilayas = collect();
                                $partenaires = collect();
                                
                                foreach($operateur->marches as $marche) {
                                    if($marche->projet) {
                                        $projets->push($marche->projet);
                                    }
                                    if($marche->wilayas) {
                                        $wilayas = $wilayas->merge($marche->wilayas)->unique('id');
                                    }
                                    if($marche->partenaires) {
                                        $partenaires = $partenaires->merge($marche->partenaires)->unique('id');
                                    }
                                }
                            @endphp

                            @if($projets->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('marche.associated_projects') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($projets->unique('id') as $projet)
                                            <div class="mb-2">
                                                <strong>{{ $projet->intitule_resume ?? $projet->intitule_resume_fr ?? 'N/A' }}</strong><br>
                                                
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        
                        <div class="row mb-4">
                            <!-- Wilayas -->
                            @if($wilayas->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('marche.all_targeted_wilayas') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($wilayas as $wilaya)
                                            <span class="badge bg-primary mr-1 mb-1">
                                                {{ $wilaya->intitule ?? $wilaya->intitule_fr ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Partenaires financiers -->
                            @if($partenaires->isNotEmpty())
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ __('marche.all_financial_partners') }}</h3>
                                    </div>
                                    <div class="card-body">
                                        @foreach($partenaires as $partenaire)
                                            <span class="badge bg-success mr-1 mb-1">
                                                {{ $partenaire->intitule ?? $partenaire->intitule_fr ?? 'N/A' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>--}}

                        <!-- DÉTAILS DES MARCHÉS -->
                        @foreach($operateur->marches as $marche)
                            <h3 class="mt-4">{{ __('marche.market_details') }} : {{ __('marche.market_number') }} {{ empty($marche->num)  ? 'NR' : $marche->num  }}</h3>

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Informations générales du marché -->
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ __('marche.general_info') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>{{ __('marche.project') }}:</th>
                                                    <td>{{ $marche->projet->intitule_resume ?? $marche->projet->intitule_resume_fr ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.operator') }}:</th>
                                                    <td>{{ $marche->operateur->intitule ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.period') }}:</th>
                                                    <td>{{ $marche->projet->periode ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    
                                                    <th>{{ __('marche.market_number') }}:</th>
                                                    <td>{{ $marche->num ?? 'N/A' }}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th>{{ __('marche.lot') }}:</th>
                                                    <td>{{ $marche->lot ?? 'N/A' }}</td>
                                                </tr>                                                

                                                <tr>
                                                    <th>{{ __('marche.signature_date') }}:</th>
                                                    <td>
                                                        @if($marche->date_sign)
                                                            {{ \Carbon\Carbon::parse($marche->date_sign)->format('d/m/Y') }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.duration_months') }}:</th>
                                                    <td>{{ $marche->duree ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.amount_mru') }}:</th>
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

                                    <!-- Indicateurs spécifiques au marché -->
                                    <div class="card card-success">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ __('marche.indicators') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>{{ __('marche.locations_to_trigger') }}:</th>
                                                    <td>{{ $marche->nb_loc_adecl ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.locations_to_monitor') }}:</th>
                                                    <td>{{ $marche->nb_loc_asuiv_postfdal ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.family_latrines') }}:</th>
                                                    <td>{{ $marche->nb_latfam_asubv ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.cabins_to_build') }}:</th>
                                                    <td>{{ $marche->nb_cab_a_const ?? 0 }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.cabins_to_rehabilitate') }}:</th>
                                                    <td>{{ $marche->nb_cab_a_rehab ?? 0 }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Détails du projet spécifique -->
                                    @if($marche->projet)
                                    {{--<div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ __('marche.project_details') }} </h3>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>{{ __('marche.project') }}:</th>
                                                    <td>{{ $marche->projet->intitule_resume ?? $marche->projet->intitule_resume_fr ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.full_title') }}:</th>
                                                    <td>{{ $marche->projet->intitule_complet ?? $marche->projet->intitule_complet_fr ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.period') }}:</th>
                                                    <td>{{ $marche->projet->periode ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.target_environment') }}:</th>
                                                    <td>{{ $marche->projet->milieu_cible ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>{{ __('marche.code') }}:</th>
                                                    <td>{{ $marche->projet->code ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>--}}
                                    @endif

                                    <!-- Wilayas et partenaires spécifiques à ce marché -->
                                    <div class="card card-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ __('marche.location_financing') }} ({{ __('marche.for_this_market') }})</h3>
                                        </div>
                                        <div class="card-body">
                                            <h5>{{ __('marche.targeted_wilayas') }}:</h5>
                                            @if($marche->wilayas && $marche->wilayas->count() > 0)
                                                @foreach($marche->wilayas as $wilaya)
                                                    <span class="badge bg-primary mr-1 mb-1">
                                                        {{ $wilaya->intitule ?? $wilaya->intitule_fr ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">{{ __('marche.no_wilaya') }}</span>
                                            @endif

                                            <h5 class="mt-3">{{ __('marche.financial_partners') }}:</h5>
                                            @if($marche->partenaires && $marche->partenaires->count() > 0)
                                                @foreach($marche->partenaires as $partenaire)
                                                    <span class="badge bg-success mr-1 mb-1">
                                                        {{ $partenaire->intitule ?? $partenaire->intitule_fr ?? 'N/A' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">{{ __('marche.no_partner') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection