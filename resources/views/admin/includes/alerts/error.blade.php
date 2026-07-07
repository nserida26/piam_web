@if (Session::has('errors'))
    <div class="alert alert-danger" role="alert">
        {{ Session::get('errors') }}
    </div>
@endif
