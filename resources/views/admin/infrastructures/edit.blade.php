@extends('layouts.admin')
@section('title')
    @lang('sidebar.infrastructures')
@endsection
@section('contentheader')
    @lang('sidebar.infrastructures')
@endsection
@section('contentheaderlink')
    <a href="{{ url('admin/infrastructures') }}"> @lang('sidebar.infrastructures') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.infrastructures')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Edit Infrastructure #{{ $infrastructure->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/infrastructures') }}" title="Back"><button
                                class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i>
                                Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/infrastructures/' . $infrastructure->id) }}"
                            accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            @include ('admin.infrastructures.form', ['formMode' => 'edit'])

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('assets/admin/plugins/select2/js/select2.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.wilaya').select2()
            $('.moughata').select2()
            $('.commune').select2() 
            $('.localite').select2()

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

                        $('#commune').html('<option value="">-- Select Commune --</option>');

                        $.each(res.communes, function(key, value) {

                            $("#commune").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                    }

                });

            });
            $('#commune').on('change', function() {

                var idCommune = this.value;
                var idWilaya = $("#wilaya").val();
                var idMoughata = $("#moughata").val();
                $.ajax({

                    url: "{{ url('admin/api/fetch-localites') }}",

                    type: "POST",

                    data: {

                        moughata_id: idMoughata,
                        commune_id: idCommune,
                        wilaya_id: idWilaya,
                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(res) {

                        $('#localite').html('<option value="">-- Select localite --</option>');

                        $.each(res.localites, function(key, value) {

                            $("#localite").append('<option value="' + value

                                .id + '">' + value.intitule_fr + '</option>');

                        });

                    }

                });

            });
        })
    </script>
@endpush
