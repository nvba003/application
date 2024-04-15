<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel chào') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg width='64px' height='64px' stroke-width='1' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg' color='%230062ff'><path d='M10 9H8M2 12C2 17.5228 6.47715 22 12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12Z' stroke='%230062ff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'></path><path d='M16.5 14.5C16.5 14.5 15 16.5 12 16.5C9 16.5 7.5 14.5 7.5 14.5' stroke='%230062ff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'></path><path d='M15.5 9C15.2239 9 15 8.77614 15 8.5C15 8.22386 15.2239 8 15.5 8C15.7761 8 16 8.22386 16 8.5C16 8.77614 15.7761 9 15.5 9Z' fill='%230062ff' stroke='%230062ff' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'></path></svg>">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}"> <!-- Sidebar CSS -->

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/sidebar.js') }}" defer></script> <!-- Sidebar JavaScript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <!-- CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="flex"> <!-- Wrapper for sidebar and content -->
                @include('components.sidebar') <!-- Sidebar component -->

                <div class="content w-full"> <!-- Content wrapper -->
                <div class="navigation"> <!-- Navigation bar -->
                    @include('layouts.navigation') <!-- Include your navigation view -->
                </div>
                    <!-- Page Content -->
                    <main>
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
        <!-- Bootstrap Select CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" rel="stylesheet">
        <!-- Bootstrap Select JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

        <!-- Địa điểm cho các scripts tùy chỉnh từ view con -->
        @stack('scripts')
        
    </body>
</html>
