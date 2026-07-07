@extends('layouts.admin')
@section('title') @lang('sidebar.user_operateur') @endsection
@section('contentheader') @lang('sidebar.user_operateur') @endsection
@section('contentheaderlink')
    <a href="{{ url('admin/user-operateur') }}"> @lang('sidebar.user_operateur') </a>
@endsection
@section('contentheaderactive') @lang('sidebar.user_operateur') @endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">@lang('sidebar.user_operateur') #{{ $useroperateur->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/admin/user-operateur') }}">
                        <button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left"></i> @lang('crud.back')</button>
                    </a>
                    <form method="POST" action="{{ url('admin/useroperateur/' . $useroperateur->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('{{ __('crud.confirm_delete') }}')">
                            <i class="fa fa-trash-o"></i> @lang('crud.delete')
                        </button>
                    </form>
                    <br><br>

                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr><th>ID</th><td>{{ $useroperateur->id }}</td></tr>
                                <tr><th>@lang('crud.date_valid')</th><td>{{ $useroperateur->date_valid }}</td></tr>
                                <tr><th>@lang('crud.projet')</th><td>{{ $useroperateur->intitule_resume }}</td></tr>
                                <tr><th>@lang('crud.operateur')</th><td>{{ $useroperateur->operateur }}</td></tr>
                                <tr><th>@lang('crud.operation')</th><td>{{ $useroperateur->operation }}</td></tr>
                                <tr><th>@lang('crud.user')</th><td>{{ $useroperateur->username }}</td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
