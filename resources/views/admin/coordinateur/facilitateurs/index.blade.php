@extends('layouts.admin')
@section('title') @lang('coordinateur.facilitateurs') @endsection
@section('contentheader') @lang('coordinateur.facilitateurs') @endsection
@section('contentheaderlink')
    <a href="{{ route('coordinateur.dashboard') }}">@lang('coordinateur.space')</a>
@endsection
@section('contentheaderactive') @lang('coordinateur.facilitateurs') @endsection

@section('content')
@include('admin.includes.alerts.success')
<div class="card">
    <div class="card-header">
        <div class="card-tools">
            <a class="btn btn-primary btn-sm" href="{{ route('coordinateur.facilitateurs.create') }}">
                <i class="fa fa-plus"></i> @lang('coordinateur.new_facilitateur')
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>@lang('coordinateur.name')</th>
                    <th>@lang('coordinateur.email')</th>
                    <th>@lang('coordinateur.localites')</th>
                    <th>@lang('coordinateur.projets')</th>
                    <th>@lang('coordinateur.operateurs')</th>
                    <th>@lang('user.status')</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facilitateurs as $f)
                    <tr>
                        <td>{{ $f->username }}</td>
                        <td>{{ $f->email }}</td>
                        <td>
                            @foreach ($f->localites as $l)
                                <span class="badge badge-success">{{ $l->intitule }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($f->projets as $p)
                                <span class="badge badge-info">{{ $p->intitule_resume }}</span>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($f->operateursAutorises as $o)
                                <span class="badge badge-secondary">{{ $o->intitule }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if ($f->isActive())
                                <span class="badge badge-success">@lang('user.status_active')</span>
                            @else
                                <span class="badge badge-secondary">@lang('user.status_disabled')</span>
                            @endif
                        </td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('coordinateur.facilitateurs.edit', $f->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i> @lang('coordinateur.edit')
                            </a>
                            <form action="{{ route('coordinateur.facilitateurs.toggle_status', $f->id) }}" method="post" class="d-inline">
                                @csrf
                                @if ($f->isActive())
                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                        onclick="return confirm(@json(__('user.disable_confirm')))">
                                        <i class="fas fa-user-slash"></i> @lang('user.disable')
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-user-check"></i> @lang('user.enable')
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted p-4">@lang('coordinateur.none')</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
