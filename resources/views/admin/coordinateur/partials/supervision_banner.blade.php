@if (!empty($routes['supervise']))
    @php($sup = $routes['supervise'])
    <div class="alert alert-info d-flex flex-wrap align-items-center justify-content-between">
        <div>
            <i class="fas fa-user-shield"></i>
            <strong>Supervision</strong> — espace du coordinateur <strong>{{ $sup->username }}</strong>
            ({{ optional($sup->operateur)->intitule }}).
            Vos validations / rejets sont enregistrés à votre nom, comme
            « [Admin {{ auth()->user()->username }}] », et le facilitateur est prévenu d'un rejet.
        </div>
        <div class="mt-1">
            <a href="{{ route('supervision.coordinateurs.dashboard', $sup->id) }}" class="btn btn-sm btn-light">Tableau de bord</a>
            <a href="{{ route('supervision.coordinateurs.validation', $sup->id) }}" class="btn btn-sm btn-light">Rapports à valider</a>
            <a href="{{ route('supervision.coordinateurs.index') }}" class="btn btn-sm btn-outline-dark">Tous les coordinateurs</a>
        </div>
    </div>
@endif
