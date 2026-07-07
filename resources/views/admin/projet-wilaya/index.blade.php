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

@section('content')
    <div class="container">
        <div class="row">


            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Projetwilaya</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/projet-wilaya/create') }}" class="btn btn-success btn-sm"
                            title="Add New ProjetWilaya">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/projet-wilaya') }}" accept-charset="UTF-8"
                            class="form-inline my-2 my-lg-0 float-right" role="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Search..."
                                    value="{{ request('search') }}">
                                <span class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </form>

                        <br />
                        <br /> 
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        
                                        <th>Wilaya </th>
                                        <th>Projet </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projetwilaya as $item)
                                        <tr>

                                            <td>{{ $item->intitule }}</td>
                                            <td>{{ $item->intitule_resume }}</td>
                                            <td>
                                                <a href="{{ url('/admin/projet-wilaya/' . $item->id) }}"
                                                    title="View ProjetWilaya"><button class="btn btn-info btn-sm"><i
                                                            class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                                <a href="{{ url('/admin/projet-wilaya/' . $item->id . '/edit') }}"
                                                    title="Edit ProjetWilaya"><button class="btn btn-primary btn-sm"><i
                                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        Edit</button></a>

                                                <form method="POST"
                                                    action="{{ url('/admin/projet-wilaya' . '/' . $item->id) }}"
                                                    accept-charset="UTF-8" style="display:inline">
                                                    {{ method_field('DELETE') }}
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Delete ProjetWilaya"
                                                        onclick="return confirm(&quot;Confirm delete?&quot;)"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
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
