@extends('layouts.admin')
@section('title') Supervision des coordinateurs @endsection
@section('contentheader') Supervision des coordinateurs @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.dashboard') }}">@lang('sidebar.dashboard')</a>
@endsection
@section('contentheaderactive') Coordinateurs @endsection

@section('content')
<div class="card">
    <div class="card-header">
        <p class="mb-0 text-muted small">
            <i class="fas fa-info-circle"></i>
            File de validation de chaque coordinateur (rapports localité + infrastructure de son ONG non encore
            validés par l'admin). « Vérifier / valider » ouvre son espace : vous voyez ce qu'il voit et pouvez
            valider ou rejeter à sa place quand il ne le fait pas.
        </p>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-sm table-hover mb-0">
            <thead>
                <tr>
                    <th>Coordinateur</th>
                    <th>ONG</th>
                    <th>Wilaya(s)</th>
                    <th class="text-center">Facilitateurs</th>
                    <th class="text-center"><span class="badge badge-warning">En attente</span></th>
                    <th class="text-center"><span class="badge badge-primary">Validés coord.</span></th>
                    <th class="text-center"><span class="badge badge-danger">Rejetés</span></th>
                    <th>Dernière validation</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($coordinateurs as $c)
                    <tr class="{{ $c->isActive() ? '' : 'text-muted' }}">
                        <td>
                            {{ $c->username }}
                            @unless ($c->isActive())<span class="badge badge-secondary">désactivé</span>@endunless
                            <br><small class="text-muted">{{ $c->email }}</small>
                        </td>
                        <td>{{ optional($c->operateur)->intitule ?? '—' }}</td>
                        <td><small>{{ $c->wilayas->pluck('intitule')->implode(', ') ?: '—' }}</small></td>
                        <td class="text-center">{{ $c->nb_facilitateurs }}</td>
                        <td class="text-center font-weight-bold {{ $c->stats['attente'] ? 'text-warning' : '' }}">{{ $c->stats['attente'] }}</td>
                        <td class="text-center">{{ $c->stats['valide_coord'] }}</td>
                        <td class="text-center">{{ $c->stats['rejete'] }}</td>
                        <td class="text-nowrap"><small>{{ $c->stats['derniere'] ? substr($c->stats['derniere'], 0, 16) : 'jamais' }}</small></td>
                        <td class="text-nowrap">
                            @if ($c->operateur_id)
                                <a href="{{ route('supervision.coordinateurs.validation', [$c->id, 'statut' => 'attente']) }}"
                                    class="btn btn-xs btn-primary"><i class="fas fa-search"></i> Vérifier / valider</a>
                                <a href="{{ route('supervision.coordinateurs.dashboard', $c->id) }}"
                                    class="btn btn-xs btn-outline-secondary"><i class="fas fa-tachometer-alt"></i></a>
                            @else
                                <small class="text-danger">sans ONG</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted p-4">Aucun coordinateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
