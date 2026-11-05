@extends('layouts.admin')
@section('title')
    @lang('sidebar.validation') - @lang('sidebar.menages')
@endsection
@section('contentheader')
    @lang('sidebar.validation') - @lang('sidebar.menages')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports.menages') }}"> @lang('sidebar.validation') - @lang('sidebar.menages') </a>
@endsection
@section('contentheaderactive')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">
    <style>
        #detail-table th { width: 45%; font-weight: 600; }
    </style>
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title card_title_center">@lang('general.filter')</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="wilaya" class="form-control filtre">
                            <option value="">-- Wilaya --</option>
                            @foreach ($wilayas as $item)
                                <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="moughata" class="form-control filtre" disabled></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="commune" class="form-control filtre" disabled></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <select id="localite" class="form-control filtre" disabled></select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <select id="operation" class="form-control filtre">
                            <option value="">-- Opération --</option>
                            <option value="2">Etat des lieux</option>
                            <option value="4">Dernier suivi</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <select id="statut" class="form-control filtre">
                            <option value="">-- Statut --</option>
                            <option value="a_valider">@lang('general.invalid')</option>
                            <option value="valide">@lang('general.valid')</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control" id="from_to" placeholder="Période" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title card_title_center">@lang('sidebar.validation') - @lang('sidebar.menages')</h3>
        </div>
        <div class="card-body">
            <div class="mb-2">
                <button type="button" class="btn btn-success" id="bulk-validate-btn" disabled>
                    <i class="fas fa-check"></i> @lang('general.validate_selection')
                </button>
                <button type="button" class="btn btn-danger" id="bulk-reject-btn" disabled>
                    <i class="fas fa-times"></i> @lang('general.reject_selection')
                </button>
                <span id="selected-count" class="ml-2 text-muted"></span>
                <button type="button" class="btn btn-primary float-right" id="add-menage-btn">
                    <i class="fas fa-plus"></i> Ajouter un ménage
                </button>
                <p class="text-muted small mb-0 mt-2" style="clear:both">
                    Les ménages d'une même localité / opération / projet (app mobile et web) sont fusionnés
                    automatiquement en une fiche localité (Gest. Validations, fiches localité).
                </p>
            </div>
            <div class="table-responsive">
                <table id="data" class="table table-bordered table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="check-all"></th>
                            <th>Wilaya</th>
                            <th>Moughataa</th>
                            <th>Commune</th>
                            <th>Localité</th>
                            <th>Chef de famille</th>
                            <th>Opération</th>
                            <th>Agent</th>
                            <th>Personnes</th>
                            <th>Latrine</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Validation / rejet en masse --}}
    <div class="modal fade" id="bulkModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bulk-modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="bulk-form">
                    <div class="modal-body">
                        <p><span id="bulk-selected-count">0</span> @lang('general.rows_selected')</p>
                        <input type="hidden" id="bulk-status">
                        <div class="form-group">
                            <label for="bulk-remarque">@lang('validate.remarques')</label>
                            <textarea class="form-control" id="bulk-remarque"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Saisie web d'un ménage --}}
    <div class="modal fade" id="addModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ajouter un ménage</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="add-form">
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="add-errors"></div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Wilaya *</label>
                                <select id="add-wilaya" class="form-control">
                                    <option value="">--</option>
                                    @foreach ($wilayas as $item)
                                        <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Moughataa *</label>
                                <select id="add-moughata" class="form-control" disabled></select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Commune *</label>
                                <select id="add-commune" class="form-control" disabled></select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Localité *</label>
                                <select name="localite_id" id="add-localite" class="form-control" disabled></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Opération *</label>
                                <select name="operation_id" class="form-control">
                                    <option value="2">Etat des lieux</option>
                                    <option value="4">Dernier suivi</option>
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Projet *</label>
                                <select name="projet_id" class="form-control">
                                    <option value="">--</option>
                                    @foreach ($projets as $item)
                                        <option value="{{ $item->id }}">{{ $item->intitule_resume_fr ?: $item->intitule_resume }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Opérateur *</label>
                                <select name="operateur_id" class="form-control">
                                    <option value="">--</option>
                                    @foreach ($operateurs as $item)
                                        <option value="{{ $item->id }}">{{ $item->intitule_fr ?: $item->intitule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Date</label>
                                <input type="date" name="date_op" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nom du père de famille</label>
                            <input type="text" name="nom_chef_menage" class="form-control" maxlength="191">
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Hommes</label>
                                <input type="number" min="0" name="nbr_h_c" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Femmes</label>
                                <input type="number" min="0" name="nbr_f_c" class="form-control" value="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Enfants &lt; 5 ans</label>
                                <input type="number" min="0" name="nbr_enfant_inf_5_ans" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Existence de latrine</label>
                                <select name="latr_fam" id="add-latr-fam" class="form-control">
                                    <option value="1">Oui</option>
                                    <option value="0">Non</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group add-latrine">
                                <label>Latrine améliorée</label>
                                <select name="latr_amel" id="add-latr-amel" class="form-control">
                                    <option value="1">Améliorée</option>
                                    <option value="0">Non améliorée</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group add-latrine">
                                <label>Endommagée (dernier hivernage)</label>
                                <select name="ltr_endommag" class="form-control">
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                        </div>
                        <div class="row add-amel">
                            <div class="col-md-6 form-group">
                                <label>Vidange</label>
                                <select name="vidange" class="form-control">
                                    <option value="">Jamais vidangée / NSP</option>
                                    <option value="adeq">Dans une autre fosse (adéquate)</option>
                                    <option value="inadeq">Dans la nature (inadéquate)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nombre de ménages partageant la latrine</label>
                                <input type="number" min="0" name="menages_partage" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="row add-no-latrine" style="display:none">
                            <div class="col-md-6 form-group">
                                <label>Utilise la latrine améliorée du voisin</label>
                                <select name="latr_amelior_voisin" class="form-control">
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Défécation à l'air libre</label>
                                <select name="men_dal" class="form-control">
                                    <option value="0">Non</option>
                                    <option value="1">Oui</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dispositif de lavage des mains</label>
                            <select name="dlm" class="form-control">
                                <option value="DLM Complet">Muni d'eau et savon</option>
                                <option value="DLM Incomplet">Dépourvu d'eau et/ou savon</option>
                                <option value="Sans DLM">Aucun dispositif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Détail d'une fiche --}}
    <div class="modal fade" id="detailModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fiche ménage <span id="detail-id"></span></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-bordered" id="detail-table"><tbody></tbody></table>
                    <div class="form-group">
                        <label for="detail-remarque">@lang('validate.remarques')</label>
                        <textarea class="form-control" id="detail-remarque"></textarea>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <div>
                        <button type="button" class="btn btn-danger detail-action" data-status="0">
                            <i class="fas fa-times"></i> Rejeter
                        </button>
                        <button type="button" class="btn btn-success detail-action" data-status="1">
                            <i class="fas fa-check"></i> Valider
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>
@endpush

@push('custom')
<script>
$(function () {
    var csrf = '{{ csrf_token() }}';
    var bulkUrl = "{{ route('admin.reports.menages.bulkUpdate') }}";
    var showUrl = "{{ url('admin/reports/menages') }}/";

    var table = $('#data').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.reports.menages.data') }}",
            type: 'POST',
            data: function (d) {
                d._token = csrf;
                d.wilaya_id = $('#wilaya').val();
                d.moughata_id = $('#moughata').val();
                d.commune_id = $('#commune').val();
                d.localite_id = $('#localite').val();
                d.operation_id = $('#operation').val();
                d.statut = $('#statut').val();
                var fromTo = $('#from_to').val();
                if (fromTo) {
                    var dates = fromTo.split(' - ');
                    d.from = dates[0];
                    d.to = dates[1];
                }
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'wilaya' },
            { data: 'moughata' },
            { data: 'commune' },
            { data: 'localite' },
            { data: 'chef' },
            { data: 'operation' },
            { data: 'agent' },
            { data: 'personnes', orderable: false },
            { data: 'latrine', orderable: false },
            { data: 'date' },
            {
                data: 'confirmed',
                render: function (val, type, row) {
                    var badge = val
                        ? '<span class="badge badge-success">@lang("general.valid")</span>'
                        : '<span class="badge badge-danger">@lang("general.invalid")</span>';
                    return row.remarque ? badge + '<br><small class="text-muted">' + row.remarque + '</small>' : badge;
                }
            },
            { data: 'action', orderable: false, searchable: false }
        ],
        pageLength: 50,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Tous']],
        order: [[10, 'desc']],
        createdRow: function (row, data) {
            $(row).addClass(data.confirmed ? 'table-success' : 'table-danger');
        },
        drawCallback: function () {
            $('#check-all').prop('checked', false);
            updateBulkButtons();
        }
    });

    function selectedIds() {
        return $('#data tbody .row-checkbox:checked').map(function () { return this.value; }).get();
    }

    function updateBulkButtons() {
        var count = selectedIds().length;
        $('#bulk-validate-btn, #bulk-reject-btn').prop('disabled', count === 0);
        $('#selected-count').text(count > 0 ? count + ' @lang("general.rows_selected")' : '');
    }

    function sendStatus(ids, status, remarque, done) {
        $.post(bulkUrl, { _token: csrf, ids: ids, status: status, remarque: remarque }, function () {
            table.ajax.reload(null, false);
            if (done) done();
        }, 'json').fail(function (xhr) {
            alert((xhr.responseJSON && xhr.responseJSON.message) || 'Erreur');
        });
    }

    $('#check-all').on('change', function () {
        $('#data tbody .row-checkbox').prop('checked', this.checked);
        updateBulkButtons();
    });
    $('#data tbody').on('change', '.row-checkbox', function () {
        if (!this.checked) $('#check-all').prop('checked', false);
        updateBulkButtons();
    });

    function openBulkModal(status) {
        var ids = selectedIds();
        if (!ids.length) return;
        $('#bulk-status').val(status);
        $('#bulk-selected-count').text(ids.length);
        $('#bulk-modal-title').text(status == 1 ? '@lang("general.validate_selection")' : '@lang("general.reject_selection")');
        $('#bulk-remarque').val('');
        $('#bulkModal').modal('show');
    }
    $('#bulk-validate-btn').on('click', function () { openBulkModal(1); });
    $('#bulk-reject-btn').on('click', function () { openBulkModal(0); });

    $('#bulk-form').on('submit', function (e) {
        e.preventDefault();
        sendStatus(selectedIds(), $('#bulk-status').val(), $('#bulk-remarque').val(), function () {
            $('#bulkModal').modal('hide');
        });
    });

    // Détail d'une fiche + validation unitaire
    var currentId = null;
    $('#data tbody').on('click', '.btn-detail', function () {
        currentId = $(this).data('id');
        $.getJSON(showUrl + currentId, function (res) {
            var esc = function (v) { return $('<div>').text(v === null || v === undefined ? '' : v).html(); };
            var html = '';
            $.each(res.entete, function (label, value) {
                html += '<tr class="table-light"><th>' + esc(label) + '</th><td>' + esc(value) + '</td></tr>';
            });
            $.each(res.fields, function (i, f) {
                html += '<tr><th>' + esc(f.label) + '</th><td>' + esc(f.value) + '</td></tr>';
            });
            html += '<tr><th>Statut</th><td>' + (res.confirmed
                ? '<span class="badge badge-success">@lang("general.valid")</span>'
                : '<span class="badge badge-danger">@lang("general.invalid")</span>') + '</td></tr>';
            $('#detail-id').text('#' + res.id);
            $('#detail-table tbody').html(html);
            $('#detail-remarque').val(res.remarque || '');
            $('#detailModal').modal('show');
        });
    });
    $('.detail-action').on('click', function () {
        if (!currentId) return;
        sendStatus([currentId], $(this).data('status'), $('#detail-remarque').val(), function () {
            $('#detailModal').modal('hide');
        });
    });

    // Saisie web d'un ménage
    var storeUrl = "{{ route('admin.reports.menages.store') }}";
    function toggleLatrineFields() {
        var has = $('#add-latr-fam').val() === '1';
        var amel = has && $('#add-latr-amel').val() === '1';
        $('.add-latrine').toggle(has);
        $('.add-amel').toggle(amel);
        $('.add-no-latrine').toggle(!has);
    }
    $('#add-latr-fam, #add-latr-amel').on('change', toggleLatrineFields);
    $('#add-menage-btn').on('click', function () {
        $('#add-errors').addClass('d-none').empty();
        toggleLatrineFields();
        $('#addModal').modal('show');
    });
    function addFill($select, items) {
        $select.html('<option value="">--</option>');
        $.each(items || [], function (i, v) {
            $select.append($('<option>').val(v.id).text(v.intitule_fr || v.intitule));
        });
        $select.prop('disabled', false);
    }
    $('#add-wilaya').on('change', function () {
        $('#add-moughata, #add-commune, #add-localite').empty().prop('disabled', true);
        if (this.value) {
            $.post("{{ url('admin/api/fetch-moughatas') }}", { _token: csrf, wilaya_id: this.value }, function (res) {
                addFill($('#add-moughata'), res.moughatas);
            }, 'json');
        }
    });
    $('#add-moughata').on('change', function () {
        $('#add-commune, #add-localite').empty().prop('disabled', true);
        if (this.value) {
            $.post("{{ url('admin/api/fetch-communes') }}", { _token: csrf, moughata_id: this.value }, function (res) {
                addFill($('#add-commune'), res.communes);
            }, 'json');
        }
    });
    $('#add-commune').on('change', function () {
        $('#add-localite').empty().prop('disabled', true);
        if (this.value) {
            $.post("{{ url('admin/api/fetch-localites') }}", {
                _token: csrf, wilaya_id: $('#add-wilaya').val(), moughata_id: $('#add-moughata').val(), commune_id: this.value
            }, function (res) {
                addFill($('#add-localite'), res.localites);
            }, 'json');
        }
    });
    $('#add-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $(this).find('[type=submit]').prop('disabled', true);
        $.post(storeUrl, $(this).serialize() + '&_token=' + encodeURIComponent(csrf), function () {
            $('#addModal').modal('hide');
            $('#add-form')[0].reset();
            table.ajax.reload(null, false);
        }, 'json').fail(function (xhr) {
            var r = xhr.responseJSON || {};
            var msgs = r.errors ? $.map(r.errors, function (m) { return m; }) : [r.message || 'Erreur'];
            $('#add-errors').removeClass('d-none').html($.map(msgs, function (m) {
                return $('<div>').text(m).html();
            }).join('<br>'));
        }).always(function () { $btn.prop('disabled', false); });
    });

    // Suppression (doublon / erreur) : la fiche localité fusionnée est recalculée.
    $('#data tbody').on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        if (!confirm('Supprimer cette fiche ménage ? La fiche localité sera recalculée.')) return;
        $.ajax({ url: showUrl + id, type: 'DELETE', data: { _token: csrf }, dataType: 'json' })
            .done(function () { table.ajax.reload(null, false); })
            .fail(function (xhr) { alert((xhr.responseJSON && xhr.responseJSON.message) || 'Erreur'); });
    });

    // Filtres
    $('.filtre').select2({ width: '100%' });
    $('#from_to').daterangepicker({ locale: { format: 'YYYY-MM-DD' }, autoUpdateInput: false });
    $('#from_to').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        table.ajax.reload();
    }).on('cancel.daterangepicker', function () {
        $(this).val('');
        table.ajax.reload();
    });

    function fill($select, placeholder, items, labelKey) {
        $select.html('<option value="">' + placeholder + '</option>');
        $.each(items || [], function (i, v) {
            $select.append($('<option>').val(v.id).text(v[labelKey] || v.intitule));
        });
        $select.prop('disabled', false).trigger('change.select2');
    }
    function reset($selects) {
        $selects.empty().prop('disabled', true).trigger('change.select2');
    }

    $('#wilaya').on('change', function () {
        reset($('#moughata, #commune, #localite'));
        if (this.value) {
            $.post("{{ url('admin/api/fetch-moughatas') }}", { _token: csrf, wilaya_id: this.value }, function (res) {
                fill($('#moughata'), '-- Moughataa --', res.moughatas, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#moughata').on('change', function () {
        reset($('#commune, #localite'));
        if (this.value) {
            $.post("{{ url('admin/api/fetch-communes') }}", { _token: csrf, moughata_id: this.value }, function (res) {
                fill($('#commune'), '-- Commune --', res.communes, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#commune').on('change', function () {
        reset($('#localite'));
        if (this.value) {
            $.post("{{ url('admin/api/fetch-localites') }}", {
                _token: csrf, wilaya_id: $('#wilaya').val(), moughata_id: $('#moughata').val(), commune_id: this.value
            }, function (res) {
                fill($('#localite'), '-- Localité --', res.localites, 'intitule_fr');
            }, 'json');
        }
        table.ajax.reload();
    });
    $('#localite, #operation, #statut').on('change', function () { table.ajax.reload(); });
});
</script>
@endpush
