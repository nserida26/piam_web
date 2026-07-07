@extends('layouts.admin')
@section('title')
    @lang('sidebar.marches')
@endsection
@section('contentheader')
    @lang('sidebar.marches')
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Liste des marchés</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped datatable">
                    <thead>
                        <tr>
                            <th>N° Marché</th>
                            <th>Lot</th>
                            <th>Projet</th>
                            <th>Opérateur</th>
                            <th>Montant (MRU)</th>
                            <th>Wilayas</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marches as $marche)
                        <tr>
                            <td>{{ $marche->num ?? 'N/A' }}</td>
                            <td>{{ $marche->lot ?? 'N/A' }}</td>
                            <td>
                                @if($marche->projet)
                                    {{ $marche->projet->intitule_resume ?? $marche->projet->intitule_resume_fr ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($marche->operateur)
                                    {{ $marche->operateur->intitule ?? $marche->operateur->intitule_fr ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($marche->montant)
                                    {{ number_format($marche->montant, 0, ',', ' ') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($marche->wilayas && $marche->wilayas->count() > 0)
                                    @foreach($marche->wilayas as $wilaya)
                                        <span class="badge bg-secondary mr-1">
                                            {{ $wilaya->intitule ?? $wilaya->intitule_fr ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('marches.show', $marche->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection