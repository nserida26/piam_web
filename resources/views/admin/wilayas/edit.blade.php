@extends('layouts.admin')
@section('title')
    @lang('sidebar.wilayas')
@endsection
@section('contentheader')
    @lang('sidebar.wilayas')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/wilayas') }}"> @lang('sidebar.wilayas') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.wilayas')
@endsection

@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Edit wilaya #{{ $wilaya->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/wilayas') }}" title="Back"><button class="btn btn-warning btn-sm"><i
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

                        <form method="POST" action="{{ url('/admin/wilayas/' . $wilaya->id) }}" accept-charset="UTF-8"
                            class="form-horizontal" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            @include ('admin.wilayas.form', ['formMode' => 'edit'])

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
