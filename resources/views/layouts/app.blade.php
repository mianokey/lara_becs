<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- jQuery (required by Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" crossorigin="anonymous"></script>

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

    <style>
.ts-dropdown {
    z-index: 9999 !important;
}
</style>


    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script>
        // Configure Toastr defaults
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "tapToDismiss": true
        };
    </script>
    @vite('resources/js/app.js')
</head>

<body>
    <div id="app" class="flex min-h-screen">
        @auth
        <x-sidebar :user="Auth::user()" />
        @endauth
        <main class="flex-1 py-2 px-2 bg-gray-50">
            @yield('content')
        </main>
    </div>
    @if(auth()->check())
    <script>
        window.Laravel = {
            userId: {{ auth()->id() }},
            csrfToken: "{{ csrf_token() }}"
        };

        document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tom-select').forEach((select) => {
        new TomSelect(select, {
            create: false,
            sortField: 'text',
            dropdownParent: 'body',
        });
    });
});

    </script>

    @endif
    @yield('scripts')
</body>

</html>