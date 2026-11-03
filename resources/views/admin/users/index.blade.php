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
    @include('admin.includes.alerts.success')
    @include('admin.includes.alerts.warning')
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
                        <th> @lang('user.status')</th>

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
                            <td>
                                @if ($user->isActive())
                                    <span class="badge badge-success">@lang('user.status_active')</span>
                                @else
                                    <span class="badge badge-secondary">@lang('user.status_disabled')</span>
                                @endif
                            </td>
                            <td><img src="{{ asset('assets/admin/imgs/' . $user->photo) }}" alt=""
                                    style="width:60px;border-radius:5px" class="table-avatar"></td>
                            <td>

                                <div class="button-group">


                                    <a href="{{ route('admin.users.show', $user->id) }}"><i class="fa fa-eye text-success"
                                            style="display:inline-block;margin-right:10px"></i></a>



                                    <a href="{{ route('admin.users.edit', $user->id) }}"><i
                                            class="fas fa-edit text-info"></i></a>

                                    <form action="{{ route('admin.users.toggle_status', $user->id) }}"
                                        method="post" style="display: inline;border:0">
                                        @csrf
                                        @if ($user->isActive())
                                            <button class="text-warning toggle-status" type="submit" style="border:0;background:none"
                                                title="@lang('user.disable')" data-confirm="@lang('user.disable_confirm')">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        @else
                                            <button class="text-success" type="submit" style="border:0;background:none"
                                                title="@lang('user.enable')">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                    </form>


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
                        <th> @lang('user.status')</th>

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
    <script src="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
@endpush
@push('custom')
    <script>
        $('#datatable').DataTable();
        $(document).on('click', '.toggle-status', function(e) {
            if (!confirm($(this).data('confirm'))) e.preventDefault();
        });
        $(document).on('click', '.delete', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');

            // Sans SweetAlert (fichier absent), on garde une confirmation native.
            if (typeof Swal === 'undefined') {
                if (confirm(@json(__('user.delete_confirm')))) form.submit();
                return;
            }

            Swal.fire({
                title: @json(__('user.delete_confirm')),
                text: @json(__('user.delete_confirm_text')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: @json(__('user.delete_yes')),
                cancelButtonText: @json(__('user.delete_no'))
            }).then(function (result) {
                if (result.value || result.isConfirmed) form.submit();
            });
        });
    </script>
@endpush
