@extends('layouts.admin')
@section('title') Réorganisation territoriale — Communes @endsection
@section('contentheader') Réorganisation territoriale @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/reorganisation/communes') }}">Communes → Moughataa</a>
@endsection
@section('contentheaderactive') Communes → Moughataa @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Affecter des communes à une moughataa</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Recherchez des communes (par wilaya/moughataa actuels et/ou par nom), sélectionnez-en une ou
                    plusieurs, choisissez la moughataa de destination, puis validez. Les localités des communes
                    déplacées suivent automatiquement.
                </p>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Wilaya</label>
                        <select id="f_wilaya" class="form-control">
                            <option value="">-- Toutes --</option>
                            @foreach ($wilayas as $w)
                                <option value="{{ $w->id }}">{{ $w->intitule }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Moughataa actuelle</label>
                        <select id="f_moughata" class="form-control">
                            <option value="">-- Toutes --</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Nom de la commune</label>
                        <input type="text" id="f_q" class="form-control" placeholder="Rechercher...">
                    </div>
                </div>
                <button type="button" id="btn-search" class="btn btn-primary btn-sm mb-3">
                    <i class="fas fa-search"></i> Rechercher
                </button>
                <span id="search-hint" class="text-muted ml-2"></span>

                <form method="POST" action="{{ route('admin.reorganisation.communes.assign') }}" id="assign-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="results-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:30px"><input type="checkbox" id="check-all"></th>
                                    <th>Commune</th>
                                    <th>Code</th>
                                    <th>Wilaya</th>
                                    <th>Moughataa actuelle</th>
                                </tr>
                            </thead>
                            <tbody id="results-body">
                                <tr><td colspan="5" class="text-center text-muted">Lancez une recherche pour afficher des communes.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <hr>
                    <h5>Moughataa de destination</h5>
                    <div class="row align-items-end">
                        <div class="col-md-4 form-group">
                            <label>Wilaya</label>
                            <select id="t_wilaya" class="form-control">
                                <option value="">-- Choisir --</option>
                                @foreach ($wilayas as $w)
                                    <option value="{{ $w->id }}">{{ $w->intitule }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Moughataa</label>
                            <select name="moughata_id" id="t_moughata" class="form-control" required>
                                <option value="">-- Choisir --</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Affecter la sélection (<span id="selected-count">0</span>)
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('custom')
<script>
$(function () {
    function loadMoughatas(wilayaId, target) {
        $(target).html('<option value="">-- Toutes --</option>');
        if (!wilayaId) { return; }
        $.get("{{ url('admin/localites/get-moughatas') }}/" + wilayaId, function (result) {
            $.each(result.moughatas, function (k, v) {
                $(target).append('<option value="' + v.id + '">' + v.intitule + '</option>');
            });
        }, 'json');
    }

    $('#f_wilaya').on('change', function () { loadMoughatas(this.value, '#f_moughata'); });
    $('#t_wilaya').on('change', function () { loadMoughatas(this.value, '#t_moughata'); });

    function updateSelectedCount() {
        $('#selected-count').text($('#results-body .row-checkbox:checked').length);
    }

    $('#check-all').on('change', function () {
        $('#results-body .row-checkbox').prop('checked', this.checked);
        updateSelectedCount();
    });

    $('#results-body').on('change', '.row-checkbox', function () {
        if (!this.checked) { $('#check-all').prop('checked', false); }
        updateSelectedCount();
    });

    $('#btn-search').on('click', function () {
        $.get("{{ route('admin.reorganisation.communes.search') }}", {
            wilaya_id: $('#f_wilaya').val(),
            moughata_id: $('#f_moughata').val(),
            q: $('#f_q').val(),
        }, function (res) {
            $('#check-all').prop('checked', false);
            $('#search-hint').text(res.message || (res.data.length + ' résultat(s)'));
            var rows = '';
            $.each(res.data, function (i, c) {
                rows += '<tr>'
                    + '<td><input type="checkbox" class="row-checkbox" name="commune_ids[]" value="' + c.id + '"></td>'
                    + '<td>' + c.intitule + '</td>'
                    + '<td>' + c.code + '</td>'
                    + '<td>' + c.wilaya + '</td>'
                    + '<td>' + c.moughata + '</td>'
                    + '</tr>';
            });
            $('#results-body').html(rows || '<tr><td colspan="5" class="text-center text-muted">Aucun résultat.</td></tr>');
            updateSelectedCount();
        }, 'json');
    });

    $('#assign-form').on('submit', function (e) {
        if ($('#results-body .row-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Sélectionnez au moins une commune.');
        }
    });
});
</script>
@endpush
