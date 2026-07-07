@extends('layouts.admin')
@section('title') @lang('user.profile') @endsection
@section('contentheader') @lang('user.profile') @endsection
@section('contentheaderlink')
    <a href="{{ route('admin.dashboard') }}"> @lang('sidebar.dashboard') </a>
@endsection
@section('contentheaderactive') @lang('user.profile') @endsection

@section('content')
<div class="row">

    <div class="col-lg-4 order-lg-2">
        <div class="card shadow mb-4">
            <div class="card-profile-image mt-4">
                <div class="text-center">
                    <img src="/assets/admin/imgs/{{ Auth::user()->photo }}"
                        class="rounded-circle avatar font-weight-bold" alt="User Image" />
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <h5 class="font-weight-bold">{{ Auth::user()->username }}</h5>
                            <p>@lang('user.administrator')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">@lang('user.my_account')</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">

                    <h6 class="heading-small text-muted mb-4">@lang('user.user_info')</h6>

                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">@lang('user.name') <span class="small text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control" name="name"
                                        placeholder="{{ __('user.name') }}"
                                        value="{{ old('name', Auth::user()->username) }}">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="last_name">@lang('user.last_name')</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name"
                                        placeholder="{{ __('user.last_name') }}"
                                        value="{{ old('last_name', Auth::user()->username) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="email">@lang('user.email_address') <span class="small text-danger">*</span></label>
                                    <input type="email" id="email" class="form-control" name="email"
                                        placeholder="{{ __('login.email') }}"
                                        value="{{ old('email', Auth::user()->email) }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="current_password">@lang('user.current_password')</label>
                                    <input type="password" id="current_password" class="form-control"
                                        name="current_password" placeholder="{{ __('user.current_password') }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="new_password">@lang('user.new_password')</label>
                                    <input type="password" id="new_password" class="form-control" name="new_password"
                                        placeholder="{{ __('user.new_password') }}">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="confirm_password">@lang('user.confirm_password')</label>
                                    <input type="password" id="confirm_password" class="form-control"
                                        name="password_confirmation" placeholder="{{ __('user.confirm_password') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" class="btn btn-primary">@lang('user.save_changes')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
