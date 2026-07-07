@extends('layouts.admin')
@section('title')
    @lang('sidebar.validation')
@endsection
@section('contentheader')
    @lang('sidebar.validation')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.reports') }}">  @lang('sidebar.validation') </a>
@endsection
@section('contentheaderactive')
    
@endsection
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
          href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title card_title_center">Filtring
                    </h2>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">

                                <select name="operation" id="operation" class="form-control operation">
                                    <option value="">-- Select Operation --</option>
                                    @foreach($operations as $operation)
                                        <option value="{{ $operation->id }}">{{ $operation->intitule }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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

                    </div>


                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center">Rapports</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="data" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>Wilaya</th>
                            <th>Moughata</th>
                            <th>Commune</th>
                            <th>Localite</th>
                            <th>Code ANSADE</th>
                            <th>Population ANSADE</th>
                            <th>Operation</th>
                            <th>Operateur</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>


                        </tbody>
                        <tfoot>
                        <tr>
                            <th>Wilaya</th>
                            <th>Moughata</th>
                            <th>Commune</th>
                            <th>Localite</th>
                            <th>Code ANSADE</th>
                            <th>Population ANSADE</th>
                            <th>Operation</th>
                            <th>Operateur</th>
                            <th>Date</th>
                        </tr>
                        </tfoot>
                    </table>
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


        $('#data thead th').each(function () {


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

            initComplete: function () {

                var r = $('#data thead tr');

                r.find('th').each(function () {

                    $(this).css('padding', 8);

                });

                $('#data thead').append(r);

                $('#search_0').css('text-align', 'center');

            }

        });

        table.columns().eq(0).each(function (colIdx) {

            $('input', table.column(colIdx).header()).on('keyup change', function () {

                table

                    .column(colIdx)

                    .search(this.value)

                    .draw();

            });

        });


        $('#data tbody')

            .on('mouseover', 'td', function () {

                var colIdx = table.cell(this).index().column;

                if (colIdx !== lastIdx) {

                    $(table.cells().nodes()).removeClass('highlight');

                    $(table.column(colIdx).nodes()).addClass('highlight');

                }

            })

            .on('mouseleave', function () {

                $(table.cells().nodes()).removeClass('highlight');

            });
    </script>
    <script>
        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.wilaya').select2()
            $('.moughata').select2()
            $('.commune').select2()

            $('.operation').select2()

        })
    </script>
    <script>
        $(document).ready(function () {

            $('#operation').on('change', function () {

                var idOperation = this.value;
                $.ajax({

                    url: "{{ url('admin/reports/fetch-operation-localite-report') }}",

                    type: "POST",

                    data: {

                        operation_id: idOperation,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (res) {


                        $.each(res, function (key, value) {

                            console.log(res.length)
                        });

                    }

                });

            });
            $('#wilaya').on('change', function () {

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

                    success: function (result) {

                        $('#moughata').html(
                            '<option value="">-- Select Moughata --</option>');

                        $.each(result.moughatas, function (key, value) {

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

                    success: function (res) {


                        $.each(res, function (key, value) {

                            console.log(res.length)
                        });

                    }

                });

            });

            $('#moughata').on('change', function () {

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

                    success: function (res) {
                        console.log(res);
                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function (key, value) {

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

                    success: function (res) {


                        $.each(res, function (key, value) {
                            console.log(res.length)
                        });

                    }

                });

            });

            $('#commune').on('change', function () {

                var idCommune = this.value;
                var idMoughata = $("#moughata").val();
                var idWilaya = $("#wilaya").val();

                $.ajax({

                    url: "{{ url('admin/reports/fetch-commune-localite-report') }}",

                    type: "POST",

                    data: {
                        commune_id : idCommune,
                        moughata_id: idMoughata,
                        wilaya_id: idWilaya,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function (res) {


                        $.each(res, function (key, value) {
                            console.log(res.length)
                        });

                    }

                });

            });


        });
    </script>
@endpush
