@extends('layouts.admin')
@section('title') @lang('coordinateur.validation') @endsection
@section('contentheader') @lang('coordinateur.validation') @endsection
@section('contentheaderlink')
    @if ($routes['supervise'])
        <a href="{{ route('supervision.coordinateurs.index') }}">Supervision des coordinateurs</a>
    @else
        <a href="{{ route('coordinateur.dashboard') }}">@lang('coordinateur.space')</a>
    @endif
@endsection
@section('contentheaderactive') @lang('coordinateur.validation') @endsection

@php
    $statuts = ['attente', 'valide_coord', 'rejete', 'valide_admin', 'tous'];
    $badge = function ($r) {
        if ($r->confirmed) return ['success', __('coordinateur.statut_valide_admin')];
        if ($r->statut_coordinateur === 'valide') return ['primary', __('coordinateur.statut_valide_coord')];
        if ($r->statut_coordinateur === 'rejete') return ['danger', __('coordinateur.statut_rejete')];
        return ['warning', __('coordinateur.statut_attente')];
    };
    $editable = in_array($statut, ['attente', 'valide_coord', 'rejete', 'tous']);
@endphp

@section('content')
@include('admin.coordinateur.partials.supervision_banner')
<ul class="nav nav-tabs mb-3">
    @foreach (['localite', 'infrastructure'] as $t)
        <li class="nav-item">
            <a class="nav-link {{ $type === $t ? 'active' : '' }}"
                href="{{ route($routes['prefix'] . 'validation', $routes['params'] + ['type' => $t, 'statut' => $statut]) }}">@lang('coordinateur.type_' . $t)</a>
        </li>
    @endforeach
</ul>

<div class="card">
    <div class="card-header">
        <form method="get" class="form-inline">
            <input type="hidden" name="type" value="{{ $type }}">
            <select name="statut" class="form-control form-control-sm mr-2 mb-1">
                @foreach ($statuts as $s)
                    <option value="{{ $s }}" {{ $statut === $s ? 'selected' : '' }}>@lang('coordinateur.statut_' . $s)</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm mr-2 mb-1"
                style="min-width:260px" placeholder="{{ __('coordinateur.search') }}">
            <button class="btn btn-sm btn-secondary mb-1"><i class="fas fa-filter"></i> @lang('coordinateur.filter')</button>
        </form>
    </div>

    <form method="post" action="{{ route($routes['prefix'] . 'validation.store', $routes['params']) }}" id="validation-form">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="action" id="validation-action">

        <div class="card-body table-responsive p-0">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:30px">@if($editable)<input type="checkbox" id="check-all" title="{{ __('coordinateur.select_all') }}">@endif</th>
                        <th>@lang('coordinateur.col_localite')</th>
                        @if ($type === 'infrastructure')<th>@lang('coordinateur.col_infrastructure')</th>@endif
                        <th>@lang('coordinateur.col_commune')</th>
                        <th>@lang('coordinateur.col_operation')</th>
                        <th>@lang('coordinateur.col_projet')</th>
                        <th>@lang('coordinateur.col_facilitateur')</th>
                        <th>@lang('coordinateur.col_date')</th>
                        <th>@lang('coordinateur.statut')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $r)
                        @php [$color, $label] = $badge($r); @endphp
                        <tr>
                            <td>
                                @unless ($r->confirmed)
                                    <input type="checkbox" class="row-check" name="ids[]" value="{{ $r->id }}">
                                @endunless
                            </td>
                            <td>{{ $r->localite }} @if($r->code_ansade)<small class="text-muted">({{ $r->code_ansade }})</small>@endif</td>
                            @if ($type === 'infrastructure')<td>{{ $r->infrastructure }}</td>@endif
                            <td>{{ $r->commune }}</td>
                            <td>{{ $r->operation }}</td>
                            <td>{{ $r->projet }}</td>
                            <td>{{ $r->facilitateur }}</td>
                            <td class="text-nowrap">{{ $r->date_op }}</td>
                            <td>
                                <span class="badge badge-{{ $color }}">{{ $label }}</span>
                                @if ($r->coordinateur_remarque)
                                    <br><small class="text-muted">{{ $r->coordinateur_remarque }}</small>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-info btn-detail"
                                    data-url="{{ route($routes['prefix'] . 'validation.detail', $routes['params'] + ['type' => $type, 'id' => $r->id]) }}">
                                    <i class="fas fa-eye"></i> @lang('coordinateur.detail')
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center text-muted p-4">@lang('coordinateur.no_reports')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($editable && $reports->count())
            <div class="card-footer">
                <div class="form-group mb-2">
                    <textarea name="remarque" class="form-control form-control-sm" rows="2"
                        placeholder="{{ __('coordinateur.remarque') }}">{{ old('remarque') }}</textarea>
                </div>
                <button type="button" class="btn btn-success btn-sm" data-action="valide">
                    <i class="fas fa-check"></i> @lang('coordinateur.btn_valider')
                </button>
                <button type="button" class="btn btn-danger btn-sm" data-action="rejete">
                    <i class="fas fa-times"></i> @lang('coordinateur.btn_rejeter')
                </button>
                <span class="text-muted small ml-2">@lang('coordinateur.help_validation')</span>
            </div>
        @endif
    </form>

    <div class="p-2">{{ $reports->links('pagination::bootstrap-4') }}</div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="modal fade" id="detail-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('coordinateur.detail')</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div id="detail-media" class="mb-3"></div>
                <table class="table table-sm mb-0"><tbody id="detail-body"></tbody></table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom')
@include('admin.partials.report_media')
<script>
$(function () {
    $('#check-all').on('change', function () { $('.row-check').prop('checked', this.checked); });

    $('[data-action]').on('click', function () {
        var action = $(this).data('action');
        if (!$('.row-check:checked').length) { alert(@json(__('coordinateur.select_all'))); return; }
        if (action === 'valide' && !confirm(@json(__('coordinateur.confirm_valider')))) return;
        $('#validation-action').val(action);
        $('#validation-form').submit();
    });

    $('.btn-detail').on('click', function () {
        var body = $('#detail-body').html('<tr><td>…</td></tr>');
        $('#detail-media').empty();
        $('#detail-modal').modal('show');
        $.getJSON($(this).data('url'), function (res) {
            body.empty();
            renderMedia($('#detail-media'), res.photos || [], res.gps || []);
            $.each(res.fields, function (k, v) {
                if (typeof v === 'object') v = JSON.stringify(v);
                body.append($('<tr>').append($('<th class="text-nowrap">').text(k.replace(/_/g, ' '))).append($('<td>').text(v)));
            });
        });
    });
});
</script>
@endpush
