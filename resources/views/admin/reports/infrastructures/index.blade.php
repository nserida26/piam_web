@extends('layouts.admin')
@section('title')
    @lang('sidebar.validation') - @lang('sidebar.institution')
@endsection
@section('contentheader')
    @lang('sidebar.validation') - @lang('sidebar.institution')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports.infrastructures') }}"> @lang('sidebar.validation') - @lang('sidebar.institution') </a>
@endsection
@section('contentheaderactive')
@endsection
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-scroller/css/scroller.bootstrap4.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.css') }}">

    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet"
        href="{{ asset('assets/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <style>
        #loading-spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            margin-top: -25px;
            margin-left: -25px;
            z-index: 999;
        }

        .hidden {
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
@section('content')
    <div class="card">
        <div class="card-header">
            <h2 class="card-title card_title_center">@lang('general.filter')
            </h2>

        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="wilaya" id="wilaya" class="form-control wilaya">
                            <option value="">-- Select Wilaya --</option>
                            @foreach ($wilayas as $item)
                                <option value="{{ $item->id }}">{{ $item->intitule }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="moughata" id="moughata" class="form-control moughata">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="commune" id="commune" class="form-control commune">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="localite" id="localite" class="form-control localite">
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="operation" id="operation" class="form-control operation">
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="projet" id="projet" class="form-control projet">

                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">

                        <select name="operateur" id="operateur" class="form-control operateur">


                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control float-right" id="from_to">
                        </div>

                        <!-- /.input group -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="loading-spinner" class="hidden"></div>


    <div class="row" id="content">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center">@lang('sidebar.validation')</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="mb-2">
                        <button type="button" class="btn btn-success" id="bulk-validate-btn" disabled>
                            <i class="fas fa-check"></i> @lang('general.validate_selection')
                        </button>
                        <button type="button" class="btn btn-danger" id="bulk-reject-btn" disabled>
                            <i class="fas fa-times"></i> @lang('general.reject_selection')
                        </button>
                        <span id="selected-count" class="ml-2 text-muted"></span>
                    </div>
                    <div class="table-responsive">
                        <table id="data" class="table table-bordered table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="check-all"></th>
                                    <th>Wilaya</th>
                                    <th>Moughata</th>
                                    <th>Commune</th>
                                    <th>Localite</th>
                                    <th>Operation</th>
                                    <th>Projet</th>
                                    <th>Operateur</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th id="actions">#</th>
                                </tr>
                            </thead>
                            <tbody id="localite_reports">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Wilaya</th>
                                    <th>Moughata</th>
                                    <th>Commune</th>
                                    <th>Localite</th>
                                    <th>Operation</th>
                                    <th>Projet</th>
                                    <th>Operateur</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>#</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bulk-modal-title">@lang('general.validate_selection')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><span id="bulk-selected-count">0</span> @lang('general.rows_selected')</p>
                    <form id="bulk-form">
                        <input type="hidden" id="bulk-status" name="status">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="bulk-remarque">Remarque</label>
                                    <textarea class="form-control" name="remarque" id="bulk-remarque"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>
                    </form>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

    </div>
        <!-- /.modal -->
    @endsection


    @push('script')
        <!-- DataTables  & Plugins -->
        <script src="{{ asset('assets/admin/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

        <script src="{{ asset('assets/admin/plugins/datatables-scroller/js/scroller.bootstrap4.min.js') }}"></script>

        <!-- Select2 -->
        <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
        <!-- date-range-picker -->
        <script src="{{ asset('assets/admin/plugins/daterangepicker/daterangepicker.js') }}"></script>

        <!-- Tempusdominus Bootstrap 4 -->
        <script src="{{ asset('assets/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}">
        </script>
    @endpush
    @push('custom')
        <script>
            var infraTable;
            var EDIT_BASE  = "{{ url('admin/reports/infrastructures/edit') }}";
            var DEL_BASE   = "{{ url('admin/reports/infrastructures/destroy') }}";
            var BULK_URL   = "{{ url('admin/reports/infrastructures/bulk-update') }}";
            var CSRF_TOKEN = '{{ csrf_token() }}';
            var lastFetch  = { url: null, data: null };

            function loadReports(res) {
                infraTable.clear().draw();
                $.each(res, function(key, value) {
                    var editUrl   = EDIT_BASE  + '/' + value.id;
                    var deleteUrl = DEL_BASE   + '/' + value.id;
                    var confirmed = !!value.confirmed;
                    var statusBadge = confirmed
                        ? '<span class="badge badge-success">@lang("general.valid")</span>'
                        : '<span class="badge badge-danger">@lang("general.invalid")</span>';
                    var date_op = value.date_op ? moment(value.date_op).format('DD/MM/YYYY') : '';
                    var checkbox = '<input type="checkbox" class="row-checkbox" value="' + value.id + '">';
                    var actions =
                        '<div class="btn-group" role="group">' +
                        '<a href="' + editUrl + '" class="btn btn-sm btn-info" title="@lang("general.edit")"><i class="fas fa-edit"></i></a>' +
                        '<form action="' + deleteUrl + '" method="POST" style="display:inline;" onsubmit="return confirm(\'@lang("general.delete_confirmation")\');">' +
                        '<input type="hidden" name="_method" value="DELETE">' +
                        '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                        '<button type="submit" class="btn btn-sm btn-danger" title="@lang("general.delete")"><i class="fa fa-trash"></i></button>' +
                        '</form></div>';

                    var row = infraTable.row.add([
                        checkbox, value.wilaya, value.moughata, value.commune, value.localite,
                        value.operation, value.projet, value.operateur,
                        date_op, statusBadge, actions
                    ]).draw();
                    $(row.node()).addClass(confirmed ? 'table-success' : 'table-danger');
                });
                $('#check-all').prop('checked', false);
                updateBulkButtons();
            }

            function fetchReports(url, data) {
                lastFetch = { url: url, data: data };
                post(url, data, loadReports);
            }

            function updateBulkButtons() {
                var count = $('.row-checkbox:checked').length;
                $('#bulk-validate-btn, #bulk-reject-btn').prop('disabled', count === 0);
                $('#selected-count').text(count > 0 ? count + ' @lang("general.rows_selected")' : '');
            }

            function post(url, data, onSuccess) {
                showLoadingSpinner();
                data._token = CSRF_TOKEN;
                $.ajax({
                    url: url, type: 'POST', data: data, dataType: 'json',
                    success: function(res) { hideLoadingSpinner(); onSuccess(res); }
                });
            }

            $(document).ready(function() {
                $('#content').hide();
                infraTable = $('#data').DataTable({
                    columnDefs: [
                        { targets: 0, orderable: false, searchable: false },
                        { targets: -1, orderable: false, searchable: false }
                    ]
                });

                // Per-column search inputs (footer), excluding the checkbox and actions columns
                $('#data tfoot th').each(function() {
                    var title = $(this).text().trim();
                    if (title === '#') {
                        $(this).html('');
                        return;
                    }
                    $(this).html('<input type="text" class="form-control form-control-sm" placeholder="Search ' + title + '" />');
                });

                infraTable.columns().every(function() {
                    var column = this;
                    $('input', column.footer()).on('keyup change clear', function() {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });

                $('#check-all').on('change', function() {
                    $('#data tbody .row-checkbox').prop('checked', this.checked);
                    updateBulkButtons();
                });

                $('#data tbody').on('change', '.row-checkbox', function() {
                    if (!this.checked) {
                        $('#check-all').prop('checked', false);
                    }
                    updateBulkButtons();
                });

                function openBulkModal(status) {
                    var ids = $('.row-checkbox:checked').map(function() { return this.value; }).get();
                    if (ids.length === 0) { return; }
                    $('#bulk-status').val(status);
                    $('#bulk-selected-count').text(ids.length);
                    $('#bulk-modal-title').text(status == 1 ? '@lang("general.validate_selection")' : '@lang("general.reject_selection")');
                    $('#bulk-remarque').val('');
                    $('#myModal').modal('show');
                }

                $('#bulk-validate-btn').on('click', function() { openBulkModal(1); });
                $('#bulk-reject-btn').on('click', function() { openBulkModal(0); });

                $('#bulk-form').on('submit', function(e) {
                    e.preventDefault();
                    var ids = $('.row-checkbox:checked').map(function() { return this.value; }).get();
                    if (ids.length === 0) { $('#myModal').modal('hide'); return; }
                    post(BULK_URL, { ids: ids, status: $('#bulk-status').val(), remarque: $('#bulk-remarque').val() }, function() {
                        $('#myModal').modal('hide');
                        if (lastFetch.url) { fetchReports(lastFetch.url, lastFetch.data); }
                    });
                });

                $('#from_to').daterangepicker({ locale: { format: 'Y-M-D' } });

                $('.wilaya,.moughata,.commune,.localite,.operation,.projet,.operateur').select2();
                $('#moughata,#commune,#localite,#operation,#projet,#operateur,#from_to').prop('disabled', true);

                $('#wilaya').on('change', function() {
                    var idWilaya = this.value;
                    $('#moughata,#commune,#localite,#operation,#projet,#operateur,#from_to').prop('disabled', false);
                    $('#content').show();
                    fetchReports("{{ url('admin/reports/fetch_wilaya_infrastructure_report') }}", { wilaya_id: idWilaya });
                    post("{{ url('admin/api/fetch-moughatas2') }}", { wilaya_id: idWilaya }, function(res) {
                        $('#moughata').html('<option value="">-- Select Moughata --</option>');
                        $.each(res.moughatas, function(k, v) { $("#moughata").append('<option value="'+v.id+'">'+v.intitule_fr+'</option>'); });
                    });
                });

                $('#moughata').on('change', function() {
                    var idMoughata = this.value, idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_moughata_infrastructure_report') }}", { wilaya_id: idWilaya, moughata_id: idMoughata });
                    post("{{ url('admin/api/fetch-communes2') }}", { moughata_id: idMoughata }, function(res) {
                        $('#commune').html('<option value="">-- Select Commune --</option>');
                        $.each(res.communes, function(k, v) { $("#commune").append('<option value="'+v.id+'">'+v.intitule_fr+'</option>'); });
                    });
                });

                $('#commune').on('change', function() {
                    var idCommune = this.value, idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_commune_infrastructure_report') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune });
                    post("{{ url('admin/api/fetch-localites2') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune }, function(res) {
                        $('#localite').html('<option value="">-- Select Localite --</option>');
                        $.each(res.localites, function(k, v) { $("#localite").append('<option value="'+v.id+'">'+v.intitule_fr+'</option>'); });
                    });
                });

                $('#localite').on('change', function() {
                    var idLocalite = this.value, idCommune = $("#commune").val(), idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_localite_infrastructure_report') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite });
                    post("{{ url('admin/api/fetch-operations2') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite }, function(res) {
                        $('#operation').html('<option value="">-- Select Operation --</option>');
                        $.each(res.operations, function(k, v) { $("#operation").append('<option value="'+v.id+'">'+v.intitule+'</option>'); });
                    });
                });

                $('#operation').on('change', function() {
                    var idOperation = this.value, idLocalite = $("#localite").val(), idCommune = $("#commune").val(), idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_operation_infrastructure_report') }}", { operation_id: idOperation, wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite });
                    post("{{ url('admin/api/fetch-projets2') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite, operation_id: idOperation }, function(res) {
                        $('#projet').html('<option value="">-- Select Projet --</option>');
                        $.each(res.projets, function(k, v) { $("#projet").append('<option value="'+v.id+'">'+v.intitule_resume+'</option>'); });
                    });
                });

                $('#projet').on('change', function() {
                    var idProjet = this.value, idOperation = $("#operation").val(), idLocalite = $("#localite").val(), idCommune = $("#commune").val(), idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_projet_infrastructure_report') }}", { projet_id: idProjet, operation_id: idOperation, wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite });
                    post("{{ url('admin/api/fetch-operateurs3') }}", { wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite, operation_id: idOperation, projet_id: idProjet }, function(res) {
                        $('#operateur').html('<option value="">-- Select Operateur --</option>');
                        $.each(res.operateurs, function(k, v) { $("#operateur").append('<option value="'+v.id+'">'+v.intitule+'</option>'); });
                    });
                });

                $('#operateur').on('change', function() {
                    var idOperateur = this.value, idProjet = $("#projet").val(), idOperation = $("#operation").val(), idLocalite = $("#localite").val(), idCommune = $("#commune").val(), idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_operateur_infrastructure_report') }}", { operateur_id: idOperateur, projet_id: idProjet, operation_id: idOperation, wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite });
                });

                $('#from_to').on('change', function() {
                    var parts = this.value.split(' - ');
                    var idOperateur = $("#operateur").val(), idProjet = $("#projet").val(), idOperation = $("#operation").val(), idLocalite = $("#localite").val(), idCommune = $("#commune").val(), idMoughata = $("#moughata").val(), idWilaya = $("#wilaya").val();
                    fetchReports("{{ url('admin/reports/fetch_date_infrastructure_report') }}", { from: parts[0], to: parts[1], operateur_id: idOperateur, projet_id: idProjet, operation_id: idOperation, wilaya_id: idWilaya, moughata_id: idMoughata, commune_id: idCommune, localite_id: idLocalite });
                });
            });

            function showLoadingSpinner() { $("#loading-spinner").removeClass("hidden"); }
            function hideLoadingSpinner() { $("#loading-spinner").addClass("hidden"); }
        </script>
    @endpush