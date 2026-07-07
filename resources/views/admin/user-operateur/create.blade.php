@extends('layouts.admin')
@section('title')
    @lang('sidebar.user_operateur')
@endsection
@section('contentheader')
    @lang('sidebar.user_operateur')
@endsection
@section('contentheaderlink')
    <a href="{{ url('admin/operateurs') }}"> @lang('sidebar.user_operateur') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.user_operateur')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    
@endpush
@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Create New UserOperateur</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/user-operateur') }}" title="Back"><button
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

                        <form method="POST" action="{{ url('/admin/user-operateur') }}" accept-charset="UTF-8"
                            class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('admin.user-operateur.form', ['formMode' => 'create'])

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
            $('.projet').select2()
            $('.operateur').select2()
            $('.operation').select2()
            $('.user').select2()


            $('#projet').on('change', function() {

                var idProjet = this.value;

                $("#operateur").html('');
                $.ajax({

                    url: "{{ url('admin/api/fetch-operateurs2') }}",

                    type: "POST",

                    data: {

                        projet_id: idProjet,

                        _token: '{{ csrf_token() }}'

                    },

                    dataType: 'json',

                    success: function(result) {

                        $('#operateur').html(
                            '<option value="">-- Select operateur --</option>');


                        $.each(result.operateurs, function(key, value) {

                            $("#operateur").append('<option value="' + value

                                .id + '">' + value.intitule + '</option>');

                        });


                    }

                });


            });

        })
    </script>
@endpush
