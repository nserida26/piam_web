@extends('layouts.admin')
@section('title') @lang('user.users') @endsection
@section('contentheader') @lang('user.users') @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.users.index') }}"> @lang('sidebar.users') </a>
@endsection
@section('contentheaderactive') @lang('user.users') @endsection

@section('content')
<div class="row">
    <div class="col-md-10 col-sm-offset-1">
        <div class="card">
            <div class="card-header">@lang('user.user_info')</div>
            <div class="card-body">
                <form class="form-horizontal">

                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">@lang('user.name')</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $user->username }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">@lang('user.email')</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">@lang('user.roles_label')</label>
                        <div class="col-sm-10">
                            <ul>
                                @foreach ($roles as $role)
                                    <li>{{ $role->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">@lang('user.photo')</label>
                        <div class="col-sm-10">
                            <img src="{{ asset('assets/admin/imgs/' . $user->photo) }}" id="photo"
                                style="margin-bottom: 8px;height:100px;border-radius:5px">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary" style="margin-top:10px">@lang('crud.back')</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
