@extends('layouts.admin')
@section('title')
    @lang('user.users')
@endsection
@section('contentheader')
    @lang('user.users')
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.users.index') }}"> @lang('sidebar.users') </a>
@endsection
@section('contentheaderactive')
    @lang('user.users')
@endsection
@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="card">
        <div class="card-header">

            <div class="card-tools">

                <a class="btn btn-primary btn-sm" href="{{ route('admin.users.create') }}"> <i class="fa fa-plus"></i>
                    @lang('user.add')</a>


                <a class="btn btn-primary btn-sm" href="{{ url('admin/users/roles/create') }}"> <i class="fa fa-plus"></i>
                    @lang('user.add_role')</a>


            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table class="table table-bordered table-striped" id="datatable">
                <thead>
                    <tr>

                        <th> @lang('user.name')</th>
                        <th> @lang('user.email')</th>
                        <th> @lang('user.roles')</th>

                        <th> @lang('user.photo')</th>
                        <th> @lang('user.actions')</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($users as $user)
                        <tr>

                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>

                            <td>
                                <ul class="list">
                                    @foreach ($user->roles as $role)
                                        <li class="list-item">{{ ucfirst($role->name) }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td><img src="{{ asset('assets/admin/imgs/' . $user->photo) }}" alt=""
                                    style="width:60px;border-radius:5px" class="table-avatar"></td>
                            <td>

                                <div class="button-group">


                                    <a href="{{ route('admin.users.show', $user->id) }}"><i class="fa fa-eye text-success"
                                            style="display:inline-block;margin-right:10px"></i></a>



                                    <a href="{{ route('admin.users.edit', $user->id) }}"><i
                                            class="fas fa-edit text-info"></i></a>



                                    <form id="delete-form" action="{{ route('admin.users.destroy', $user->id) }}"
                                        method="post" style="display: inline;border:0">

                                        @csrf
                                        @method('DELETE')

                                        <button class="text-danger delete" type="submit" style="border:0;background:none">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                </div>

                            </td>
                        </tr>
                    @endforeach



                </tbody>

                <tfoot>
                    <tr>

                        <th> @lang('user.name')</th>
                        <th> @lang('user.email')</th>
                        <th> @lang('user.roles')</th>

                        <th> @lang('user.photo')</th>
                        <th> @lang('user.actions')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection
@push('script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $('#datatable').DataTable();
        $(document).on('click', '.delete', function(e) {

            var form = $(this).parents('form:first');

            var confirmed = false;

            e.preventDefault();

            swal({
                title: 'Are you sure want to delete?',
                text: "Onec Delete, This will be permanently delete!",
                icon: "warning",
                buttons: true,
                dangerMode: true
            }).then((willDelete) => {
                if (willDelete) {
                    // window.location.href = link;
                    confirmed = true;

                    form.submit();

                } else {
                    swal("Safe Data!");
                }
            });
        });
    </script>
@endpush
