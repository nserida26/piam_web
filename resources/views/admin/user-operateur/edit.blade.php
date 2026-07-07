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

@section('content')
    <div class="container">
        <div class="row">
           
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">Edit UserOperateur #{{ $useroperateur->id }}</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/user-operateur') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        @if ($errors->any())
                            <ul class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ url('/admin/user-operateur/' . $useroperateur->id) }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            {{ csrf_field() }}

                            @include ('admin.user-operateur.form', ['formMode' => 'edit'])

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
