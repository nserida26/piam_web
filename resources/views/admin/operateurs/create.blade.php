@extends('layouts.admin')
@section('title')
    @lang('sidebar.operators')
@endsection
@section('contentheader')
    @lang('sidebar.operators')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/operateurs') }}"> @lang('sidebar.operators') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.operators')
@endsection
@section('content')
    <div class="container">
        <div class="row">



        <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Create New Operateur</div>
                    <div class="card-body">
                        <a href="{{ url('admin/operateurs') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('admin/operateurs') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('admin.operateurs.form', ['formMode' => 'create'])

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
