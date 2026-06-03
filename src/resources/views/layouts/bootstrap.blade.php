@php
    use App\Enums\Role;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Welcome')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-light">
    @auth
        @if(auth()->user()->hasRole(Role::ADMIN->value))
            <nav class="navbar navbar-dark bg-dark px-3">
                <a class="navbar-brand" href="{{ route('admin.tickets.page') }}">Admin Panel</a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="btn btn-danger btn-sm">Logout</button>
                </form>
            </nav>
        @endif
    @endauth

    <div class="container mt-4">

        @yield('content')

    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/app.js')

    @stack('scripts')

</body>

</html>
