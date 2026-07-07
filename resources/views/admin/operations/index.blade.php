@extends('layouts.admin')
@section('title')
    @lang('sidebar.operations')
@endsection
@section('contentheader')
    @lang('sidebar.operations')
@endsection
@section('contentheaderlink')
<a href="{{ url('admin/operations') }}"> @lang('sidebar.operations') </a>
@endsection
@section('contentheaderactive')
    @lang('sidebar.operations')
@endsection
@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Operations</div>
                    <div class="card-body">
                        <a href="{{ url('/admin/operations/create') }}" class="btn btn-success btn-sm"
                            title="Add New Operation">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>

                        <form method="GET" action="{{ url('/admin/operations') }}" accept-charset="UTF-8"
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
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Intitule</th>
                                        <th>Intitule Fr</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->intitule }}</td>
                                            <td>{{ $item->intitule_fr }}</td>
                                            <td>
                                                <a href="{{ url('/admin/operations/' . $item->id) }}"
                                                    title="View Operation"><button class="btn btn-info btn-sm"><i
                                                            class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                                <a href="{{ url('/admin/operations/' . $item->id . '/edit') }}"
                                                    title="Edit Operation"><button class="btn btn-primary btn-sm"><i
                                                            class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                        Edit</button></a>

                                                <form method="POST"
                                                    action="{{ url('/admin/operations' . '/' . $item->id) }}"
                                                    accept-charset="UTF-8" style="display:inline">
                                                    {{ method_field('DELETE') }}
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        title="Delete Operation"
                                                        onclick="return confirm(&quot;Confirm delete?&quot;)"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination-wrapper"> {!! $operations->appends(['search' => Request::get('search')])->render() !!} </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
