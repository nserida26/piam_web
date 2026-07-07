@extends('layouts.admin')
@section('title')
    @lang('sidebar.wilaya_user')
@endsection
@section('contentheader')
    @lang('sidebar.wilaya_user')
@endsection
@section('contentheaderlink')
    <a href="{{ url('admin/wilayas') }}"> @lang('sidebar.wilaya_user') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.wilaya_user')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    
@endpush
@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Create New WilayaUser</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/wilaya-user') }}" title="Back"><button class="btn btn-warning btn-sm"><i
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

                        <form method="POST" action="{{ url('/admin/wilaya-user') }}" accept-charset="UTF-8"
                            class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('admin.wilaya-user.form', ['formMode' => 'create'])

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
            $('.user').select2()


        })
    </script>
@endpush
