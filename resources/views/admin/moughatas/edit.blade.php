@extends('layouts.admin')
@section('title')
    @lang('sidebar.moughatas')
@endsection
@section('contentheader')
    @lang('sidebar.moughatas')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/moughatas') }}"> @lang('sidebar.moughatas') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.moughatas')
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/select2/css/select2.min.css') }}">
@endpush
@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Edit Moughata #{{ $moughatum->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/moughatas') }}" title="Back"><button class="btn btn-warning btn-sm"><i
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

                        <form method="POST" action="{{ url('/admin/moughatas/' . $moughatum->id) }}" accept-charset="UTF-8"
                            class="form-horizontal" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            @include ('admin.moughatas.form', ['formMode' => 'edit'])

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
        })
    </script>
@endpush