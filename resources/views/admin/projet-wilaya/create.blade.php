@extends('layouts.admin')
@section('title')
    @lang('sidebar.projet_wilaya')
@endsection
@section('contentheader')
    @lang('sidebar.projet_wilaya')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/projets') }}"> @lang('sidebar.projet_wilaya') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.projet_wilaya')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
    
@endpush
@section('content')
    <div class="container">
        <div class="row">
           

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Create New ProjetWilaya</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/projet-wilaya') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/projet-wilaya') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('admin.projet-wilaya.form', ['formMode' => 'create'])

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
            $('.projet').select2()


        })
    </script>
@endpush
