@extends('admin.imports.index')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .step-badge {
            width: 28px; height: 28px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px; margin-right: 8px;
        }
        .stat-card {
            border-radius: 10px; text-align: center; padding: 16px 8px;
            cursor: pointer; transition: opacity .15s;
        }
        .stat-card:hover { opacity: .85; }
        .stat-card.active-filter { outline: 3px solid rgba(0,0,0,.35); }
        .stat-card .stat-num { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-lbl { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
        #preview-panel { display: none; }
        .row-new     { background-color: #d4edda !important; }
        .row-update  { background-color: #d1ecf1 !important; }
        .row-error   { background-color: #f8d7da !important; }
        .row-warning { background-color: #fff3cd !important; }
        .badge-new     { background-color: #28a745; }
        .badge-update  { background-color: #17a2b8; }
        .badge-error   { background-color: #dc3545; }
        .badge-warning { background-color: #ffc107; color:#333; }
        .icon-circle { width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto; }
        .card-outline { transition: all .3s; }
        .card-outline:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        #preview-table-wrapper { max-height: 520px; overflow-y: auto; }
        #filter-btns .btn { font-size: 12px; }
    </style>
@endpush

@section('import')

{{-- ── STEP 1 : UPLOAD FORM ────────────────────────────────────────────── --}}
<div class="card card-default" id="upload-card">
    <div class="card-header">
        <h3 class="card-title">
            <span class="step-badge bg-primary text-white">1</span>
            Sélectionner le fichier
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('general.operation')</label>
                    <select class="form-control select2" name="operation_id" id="import-operation">
                        @foreach ($operations as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>@lang('general.wilaya')</label>
                    <select class="form-control select2" name="wilaya_id" id="import-wilaya">
                        @foreach ($wilayas as $item)
                            <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>@lang('general.file')</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="import-file" accept=".xlsx,.xls">
                            <label class="custom-file-label" id="file-label">Choisir un fichier…</label>
                        </div>
                    </div>
                    <small class="text-muted">Formats : xlsx, xls — Max 200 Mo</small>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" id="btn-analyze" class="btn btn-primary float-right" disabled>
            <i class="fas fa-search mr-1"></i> Analyser le fichier
        </button>
    </div>
</div>

{{-- ── LOADING INDICATOR ───────────────────────────────────────────────── --}}
<div id="loading-indicator" style="display:none;" class="text-center py-4">
    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;"></div>
    <p class="mt-3 text-muted">Analyse en cours, veuillez patienter…</p>
</div>

{{-- ── STEP 2 : PREVIEW PANEL ──────────────────────────────────────────── --}}
<div id="preview-panel">

    {{-- Stats row (clickable to filter) --}}
    <div class="row mb-3" id="stats-row"></div>

    {{-- Preview table --}}
    <div class="card card-default">
        <div class="card-header d-flex align-items-center flex-wrap" style="gap:8px;">
            <h3 class="card-title mb-0">
                <span class="step-badge bg-success text-white">2</span>
                Aperçu des données
                <small class="text-muted ml-2">(<span id="visible-count">0</span> / <span id="total-count">0</span> lignes)</small>
            </h3>
            <div class="ml-auto d-flex align-items-center" style="gap:6px;">
                <div class="btn-group btn-group-sm" id="filter-btns">
                    <button class="btn btn-secondary active" data-filter="all">Tous</button>
                    <button class="btn btn-success"          data-filter="new">Nouveaux</button>
                    <button class="btn btn-info"             data-filter="update">Mises à jour</button>
                    <button class="btn btn-danger"           data-filter="error">Erreurs</button>
                    <button class="btn btn-warning text-dark" data-filter="warning">Avertissements</button>
                </div>
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="preview-table-wrapper">
                <table class="table table-sm table-bordered mb-0" id="preview-table">
                    <thead class="thead-light"><tr id="preview-thead"></tr></thead>
                    <tbody id="preview-tbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Errors list --}}
    <div id="errors-card" class="card card-danger" style="display:none;">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-1"></i> Erreurs détectées</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead><tr><th style="width:80px">Ligne</th><th>Motif</th></tr></thead>
                <tbody id="errors-tbody"></tbody>
            </table>
        </div>
    </div>

    {{-- Action buttons --}}
    <div class="card card-default">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted" id="preview-summary"></p>
                </div>
                <div class="col-md-6 text-right">
                    <button type="button" id="btn-reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo mr-1"></i> Choisir un autre fichier
                    </button>
                    <button type="button" id="btn-confirm" class="btn btn-success">
                        <i class="fas fa-upload mr-1"></i> Confirmer l'import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="confirm-form" action="{{ route('admin.imports.reports.confirm') }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="temp_path"    id="conf-temp-path">
        <input type="hidden" name="operation_id" id="conf-operation">
        <input type="hidden" name="wilaya_id"    id="conf-wilaya">
    </form>
</div>

{{-- ── TEMPLATE DOWNLOADS ──────────────────────────────────────────────── --}}
<div class="card card-default mt-4">
    <div class="card-header">
        <h3 class="card-title">@lang('general.template_canvases')</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            @php
            $templates = [
                ['label'=>__('general.declenchement'),      'file'=>'dec.xlsx',          'icon'=>'fa-play',          'color'=>'primary'],
                ['label'=>__('general.etat_des_lieux'),     'file'=>'etat.xlsx',         'icon'=>'fa-clipboard-list','color'=>'success'],
                ['label'=>__('general.certification_fdal'), 'file'=>'fdal.xlsx',         'icon'=>'fa-certificate',   'color'=>'info'],
                ['label'=>__('general.dernier_suivi'),      'file'=>'dernier.xlsx',      'icon'=>'fa-chart-line',    'color'=>'warning'],
                ['label'=>__('general.inventaire'),         'file'=>'inventaire.xlsx',   'icon'=>'fa-boxes',         'color'=>'danger'],
                ['label'=>__('general.programmation'),      'file'=>'programmation.xlsx','icon'=>'fa-calendar-alt',  'color'=>'secondary'],
                ['label'=>__('general.realisation'),        'file'=>'realisation.xlsx',  'icon'=>'fa-hard-hat',      'color'=>'dark'],
            ];
            @endphp
            @foreach($templates as $t)
            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-{{ $t['color'] }} h-100">
                    <div class="card-body text-center">
                        <div class="icon-circle bg-{{ $t['color'] }} mb-3">
                            <i class="fas {{ $t['icon'] }} fa-2x text-white"></i>
                        </div>
                        <h6 class="font-weight-bold">{{ $t['label'] }}</h6>
                        <a href="/storage/templates/{{ $t['file'] }}" download
                           class="btn btn-sm btn-outline-{{ $t['color'] }}">
                            <i class="fas fa-download mr-1"></i> @lang('general.download')
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="{{ asset('assets/admin/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(function () {
    $('.select2').select2();

    var allRows      = [];
    var activeFilter = 'all';

    // ── File selection ────────────────────────────────────────────────────
    $('#import-file').on('change', function () {
        var name = $(this).val().split('\\').pop();
        $('#file-label').text(name || 'Choisir un fichier…');
        $('#btn-analyze').prop('disabled', !name);
    });

    // ── Analyze ───────────────────────────────────────────────────────────
    $('#btn-analyze').on('click', function () {
        var file = $('#import-file')[0].files[0];
        if (!file) return;

        var fd = new FormData();
        fd.append('_token', '{{ csrf_token() }}');
        fd.append('operation_id', $('#import-operation').val());
        fd.append('wilaya_id',    $('#import-wilaya').val());
        fd.append('file', file);

        $('#upload-card').hide();
        $('#preview-panel').hide();
        $('#loading-indicator').show();

        $.ajax({
            url: '{{ route("admin.imports.reports.preview") }}',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (data) {
                $('#loading-indicator').hide();
                if (!data.success) {
                    alert('Erreur : ' + (data.error || 'Impossible d\'analyser le fichier.'));
                    $('#upload-card').show();
                    return;
                }
                renderPreview(data);
            },
            error: function (xhr) {
                $('#loading-indicator').hide();
                $('#upload-card').show();
                var msg = xhr.responseJSON
                    ? (xhr.responseJSON.error || xhr.responseJSON.message)
                    : 'Erreur serveur.';
                alert('Erreur : ' + msg);
            }
        });
    });

    // ── Reset ─────────────────────────────────────────────────────────────
    $('#btn-reset').on('click', function () {
        $('#preview-panel').hide();
        $('#upload-card').show();
        $('#import-file').val('');
        $('#file-label').text('Choisir un fichier…');
        $('#btn-analyze').prop('disabled', true);
        allRows = [];
        activeFilter = 'all';
    });

    // ── Confirm ───────────────────────────────────────────────────────────
    $('#btn-confirm').on('click', function () {
        if (!confirm('Confirmer l\'import du rapport ?')) return;
        $('#confirm-form').submit();
    });

    // ── Filter buttons ────────────────────────────────────────────────────
    $('#filter-btns').on('click', '[data-filter]', function () {
        applyFilter($(this).data('filter'));
    });

    // ── Stat cards (clickable) ────────────────────────────────────────────
    $('#stats-row').on('click', '[data-filter-stat]', function () {
        applyFilter($(this).data('filter-stat'));
    });

    // ── RENDER PREVIEW ────────────────────────────────────────────────────
    function renderPreview(data) {
        var s = data.stats;
        allRows = data.preview_rows || [];
        activeFilter = 'all';

        // Stats cards (clickable)
        var cards = [
            { num: s.total,   lbl: 'Total',            bg: 'bg-secondary',          filter: 'all'     },
            { num: s.new,     lbl: 'À importer',        bg: 'bg-success',            filter: 'new'     },
            { num: s.update,  lbl: 'Mises à jour',      bg: 'bg-info',               filter: 'update'  },
            { num: s.error,   lbl: 'Erreurs',           bg: 'bg-danger',             filter: 'error'   },
            { num: s.warning, lbl: 'Avertissements',    bg: 'bg-warning text-dark',  filter: 'warning' },
        ];
        var statsHtml = '';
        cards.forEach(function (c) {
            statsHtml += '<div class="col">' +
                '<div class="stat-card ' + c.bg + ' text-white" data-filter-stat="' + c.filter + '" title="Filtrer : ' + c.lbl + '">' +
                '<div class="stat-num">' + c.num + '</div>' +
                '<div class="stat-lbl">' + c.lbl + '</div>' +
                '</div></div>';
        });
        $('#stats-row').html(statsHtml);

        // Table headers
        var thHtml = '<th style="width:60px">Ligne</th><th style="width:110px">Statut</th>';
        (data.headers || []).forEach(function (h) {
            thHtml += '<th>' + esc(h) + '</th>';
        });
        $('#preview-thead').html(thHtml);

        // Errors list
        if (data.all_errors && data.all_errors.length > 0) {
            var errHtml = '';
            data.all_errors.forEach(function (e) {
                errHtml += '<tr><td>' + e.row + '</td><td>' + esc(e.reason) + '</td></tr>';
            });
            $('#errors-tbody').html(errHtml);
            $('#errors-card').show();
        } else {
            $('#errors-card').hide();
        }

        // Summary
        var canImport = s.new > 0 || s.update > 0;
        var txt = s.total + ' lignes analysées — ' + s.new + ' à importer';
        if (s.error   > 0) txt += ', <strong class="text-danger">' + s.error   + ' erreurs</strong>';
        if (s.warning > 0) txt += ', <span class="text-warning">' + s.warning + ' avertissements</span>';
        $('#preview-summary').html(txt + '. Les lignes en erreur seront ignorées.');
        $('#btn-confirm').prop('disabled', !canImport);

        // Confirm form
        $('#conf-temp-path').val(data.temp_path);
        $('#conf-operation').val($('#import-operation').val());
        $('#conf-wilaya').val($('#import-wilaya').val());

        $('#total-count').text(s.total);
        applyFilter('all');
        $('#preview-panel').show();
    }

    // ── FILTER + RENDER ROWS ──────────────────────────────────────────────
    function applyFilter(filter) {
        activeFilter = filter;

        $('#filter-btns [data-filter]').removeClass('active');
        $('#filter-btns [data-filter="' + filter + '"]').addClass('active');

        $('#stats-row [data-filter-stat]').removeClass('active-filter');
        $('#stats-row [data-filter-stat="' + filter + '"]').addClass('active-filter');

        var rows = filter === 'all'
            ? allRows
            : allRows.filter(function (r) { return r.status === filter; });

        $('#visible-count').text(rows.length);
        renderRows(rows);
    }

    function renderRows(rows) {
        var html = '';
        rows.forEach(function (row) {
            var badge  = '<span class="badge badge-' + row.status + '">' + statusLabel(row.status) + '</span>';
            var reason = row.reason ? '<br><small class="text-muted">' + esc(row.reason) + '</small>' : '';
            var cells  = '';
            Object.values(row.data || {}).forEach(function (v) {
                cells += '<td>' + (v !== null && v !== undefined ? esc(String(v)) : '') + '</td>';
            });
            html += '<tr class="row-' + row.status + '">' +
                '<td>' + row.row + '</td>' +
                '<td>' + badge + reason + '</td>' +
                cells + '</tr>';
        });
        $('#preview-tbody').html(html);
    }

    function statusLabel(s) {
        return { new: 'Nouveau', update: 'Màj', error: 'Erreur', warning: 'Attention' }[s] || s;
    }

    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});
</script>
@endpush
