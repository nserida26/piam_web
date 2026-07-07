@extends('layouts.admin')
@section('title')
    @lang('sidebar.validation') - @lang('sidebar.family')
@endsection
@section('contentheader')
    @lang('sidebar.validation') - @lang('sidebar.family')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports.localites') }}"> @lang('sidebar.validation') - @lang('sidebar.family') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.family')
@endsection
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center">Rapports</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ url('/admin/reports/localites/update') }}" method="post">
                        @csrf
                        <input type="hidden" id="id" name="id" value="{{ $report->id }}">
                        <div class="form-group">
                            <label for="wilaya">@lang('validate.wilaya') </label>
                            <input type="text" id="wilaya" class="form-control" value="{{ $report->wilaya }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="moughata">@lang('validate.moughata') </label>
                            <input type="text" id="moughata" class="form-control" value="{{ $report->moughata }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="commune">@lang('validate.commune') </label>
                            <input type="text" id="commune" class="form-control" value="{{ $report->commune }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="localite">@lang('validate.localite') </label>
                            <input type="text" id="localite" class="form-control" value="{{ $report->localite }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="operation">@lang('validate.operation') </label>
                            <input type="text" id="operation" class="form-control" value="{{ $report->operation }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="projet">@lang('validate.projet') </label>
                            <input type="text" id="projet" class="form-control" value="{{ $report->projet }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="operateur">@lang('validate.operateur') </label>
                            <input type="text" id="operateur" class="form-control" value="{{ $report->operateur }}"
                                disabled>
                        </div>
                        <div class="form-group">
                            <label for="date_op">@lang('validate.date_op') </label>
                            <input type="text" id="date_op" class="form-control" value="{{ $report->date_op }}">
                        </div>
                        <div class="form-group">
                            <label for="remarques">@lang('validate.remarques') </label>
                            <textarea id="remarques" name="remarques" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">@lang('validate.status')</label>
                            <select id="status" name="status" class="form-control custom-select">
                                <option value="1">@lang('general.ok')</option>
                                <option value="0">@lang('general.no')</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">@lang('general.submit')</button>

                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection


@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        var lastIdx = null;


        $('#data thead th').each(function() {


            var classname = $(this).index() == 2 ? 'form-control' : 'form-control';

            var title = $(this).html();

            $(this).html('<input type="text" class="' + classname + '" data-value="' + $(this).index() +

                '" placeholder=" ' + title + '" />');


        });

        var table = $('#data').DataTable({

            "processing": true,
            "scrollX": true,
            "searching": true,
            "autoWidth": false,
            "lengthChange": false,

            "stateSave": true,
            "responsive": true,


            "order": [

                [0, 'desc']

            ],


            iDisplayLength: 10,

            fixedHeader: true,

            initComplete: function() {

                var r = $('#data thead tr');

                r.find('th').each(function() {

                    $(this).css('padding', 8);

                });

                $('#data thead').append(r);

                $('#search_0').css('text-align', 'center');

            }

        });

        table.columns().eq(0).each(function(colIdx) {

            $('input', table.column(colIdx).header()).on('keyup change', function() {

                table

                    .column(colIdx)

                    .search(this.value)

                    .draw();

            });

        });


        $('#data tbody')

            .on('mouseover', 'td', function() {

                var colIdx = table.cell(this).index().column;

                if (colIdx !== lastIdx) {

                    $(table.cells().nodes()).removeClass('highlight');

                    $(table.column(colIdx).nodes()).addClass('highlight');

                }

            })

            .on('mouseleave', function() {

                $(table.cells().nodes()).removeClass('highlight');

            });
    </script>
    <script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.wilaya').select2()
            $('.moughata').select2()
            $('.commune').select2()

            $('.operation').select2()

        })
    </script>
    <script>
        $(document).ready(function() {

            $('#operation').on('change', function() {

                var idOperation = this.value;
                $.ajax({

                    url: "{{ url('admin/reports/fetch-operation-localite-report') }}",

                    type: "POST",

                    data: {

                        operation_id: idOperation,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {


                        $.each(res, function(key, value) {

                            console.log(res.length)
                        });

                    }

                });

            });
            $('#wilaya').on('change', function() {

                var idWilaya = this.value;

                $("#moughata").html('');

                $.ajax({

                    url: "{{ url('admin/api/fetch-moughatas') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function(key, value) {

                            $("#moughata").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                    }

                });
                $.ajax({

                    url: "{{ url('admin/reports/fetch-wilaya-localite-report') }}",

                    type: "POST",

                    data: {

                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {


                        $.each(res, function(key, value) {

                            console.log(res.length)
                        });

                    }

                });

            });

            $('#moughata').on('change', function() {

                var idMoughata = this.value;
                var idWilaya = $("#wilaya").val();

                $("#commune").html('');

                $.ajax({

                    url: "{{ url('admin/api/fetch-communes') }}",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {
                        console.log(res);
                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function(key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                    }

                });
                $.ajax({

                    url: "{{ url('admin/reports/fetch-moughata-localite-report') }}",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,
                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {


                        $.each(res, function(key, value) {
                            console.log(res.length)
                        });

                    }

                });

            });

            $('#commune').on('change', function() {

                var idCommune = this.value;
                var idMoughata = $("#moughata").val();
                var idWilaya = $("#wilaya").val();

                $.ajax({

                    url: "{{ url('admin/reports/fetch-commune-localite-report') }}",

                    type: "POST",

                    data: {
                        commune_id: idCommune,
                        moughata_id: idMoughata,
                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {


                        $.each(res, function(key, value) {
                            console.log(res.length)
                        });

                    }

                });

            });


        });
    </script>
@endpush
