@extends('layouts.admin')
@section('title') Notifications @endsection
@section('contentheader') Mes notifications @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.dashboard') }}">@lang('sidebar.dashboard')</a>
@endsection
@section('contentheaderactive') Notifications @endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $notifications->whereNull('lu_le')->count() }} non lue(s)</span>
        @if ($notifications->whereNull('lu_le')->count())
            <form method="post" action="{{ route('admin.notifications.read') }}" class="mb-0">
                @csrf
                <button class="btn btn-sm btn-outline-primary"><i class="fas fa-check-double"></i> Tout marquer comme lu</button>
            </form>
        @endif
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            @forelse ($notifications as $n)
                <li class="list-group-item {{ $n->lu_le ? '' : 'list-group-item-warning' }}">
                    <div class="d-flex justify-content-between">
                        <strong><i class="fas fa-times-circle text-danger"></i> {{ $n->titre }}</strong>
                        <small class="text-muted">{{ substr($n->created_at, 0, 16) }}</small>
                    </div>
                    <div style="white-space: pre-line">{{ $n->message }}</div>
                </li>
            @empty
                <li class="list-group-item text-center text-muted p-4">Aucune notification.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
