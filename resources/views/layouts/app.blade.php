<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'DOSANGTAO') }}</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}"> <!-- Sidebar CSS -->

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/sidebar.js') }}" defer></script> <!-- Sidebar JavaScript -->

        <!-- CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">      
        <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet"> -->

    </head>
    <body class="font-sans antialiased">
        <div class="flex navigation">
            @include('layouts.navigation')
        </div>
        <div class="min-h-screen bg-gray-100">
            <div class="wrapper d-flex"> <!-- Wrapper for sidebar and content -->
                    @include('components.sidebar') <!-- Sidebar component -->
                <div class="content expanded"> <!-- Content wrapper adjusts based on sidebar -->
                    <main>
                        @yield('content') <!-- Dynamic page content -->
                    </main>
                </div>
            </div>
        </div>


        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

        <script>
            $(document).ready(function() {
                $('.toggle-sidebar').on('click', function() {
                    $('.sidebar, .content').toggleClass('expanded');
                });
            });
        </script>

        @stack('scripts')
        
    </body>
</html>
