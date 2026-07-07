@extends('layouts.admin')
@section('title')
    @lang('sidebar.communes')
@endsection
@section('contentheader')
    @lang('sidebar.communes')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/communes') }}"> @lang('sidebar.communes') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.communes')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Create New Commune</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/communes') }}" title="Back"><button class="btn btn-warning btn-sm"><i
                                    class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/communes') }}" accept-charset="UTF-8"
                            class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('admin.communes.form', ['formMode' => 'create'])

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

        })
    </script>
@endpush
