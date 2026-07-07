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
            

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Useroperateur</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/user-operateur/create') }}" class="btn btn-success btn-sm" title="Add New UserOperateur">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/user-operateur') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br/>
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date Valid</th>
                            
                                        <th>Projet</th>
                                        <th>Operateur</th>
                                        <th>Operation</th>
                                        <th>User</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($useroperateur as $item)
                                    <tr>
                                        
                                        <td>{{ $item->date_valid }}</td>
                                        <td>{{ $item->intitule_resume }}</td>
                                        <td>{{ $item->operateur }}</td>
                                        <td>{{ $item->operation }}</td>
                                        <td>{{ $item->username }} </td>
                                        <td>
                                            <a href="{{ url('/admin/user-operateur/' . $item->id) }}" title="View UserOperateur"><button class="btn btn-info btn-sm"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                            

                                            <form method="POST" action="{{ url('/admin/user-operateur' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete UserOperateur" onclick="return confirm(&quot;Confirm delete?&quot;)"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
