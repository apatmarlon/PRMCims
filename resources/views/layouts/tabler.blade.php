
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
    <!-- SweetAlert CSS (optional) -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">

    <!-- SweetAlert JS -->
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet"/>

    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
        body {
            font-feature-settings: "cv03", "cv04", "cv11";
            background: url('{{ asset('assets/img/ldn.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            background-attachment: fixed;
            }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('{{ asset('assets/img/ldn.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            /*filter: blur(50px); increased blur strength */
            opacity: 0.69; /* 85% visible */
            z-index: -1; /* stays behind all content */
            }
            .product-suggestions {
                max-height: 240px;      /* ≈ 5 items */
                overflow-y: auto;
                scroll-behavior: smooth;
            }
            .product-suggestions .list-group-item:hover {
                background-color: #f8f9fa;
            }
         .product-hover-details-overflow {
                display: none;
                position: absolute; /* allow it to overflow outside li */
                top: 50%;
                left: 100%;        /* start just after the li */
                transform: translateY(-50%);
                white-space: nowrap; /* prevent wrapping */
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                padding: 5px 10px;
                border-radius: 4px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                z-index: 3000;
            }

            /* Show on hover */
            .list-group-item.position-relative:hover .product-hover-details-overflow {
                display: block;
            }
                    
    </style>

    <!-- Custom CSS for specific page.  -->
    @stack('page-styles')
    @livewireStyles
</head>
    <body>

        <div class="page">

            @include('layouts.body.header')

            @include('layouts.body.navbar')

            <div class="page-wrapper">
                <div>
                    @yield('content')
                </div>

                @include('layouts.body.footer')
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

        <!-- Tabler Core -->
        <script src="{{ asset('dist/js/tabler.min.js') }}" defer></script>
        {{--- Page Scripts ---}}
        @stack('page-scripts')
{{-- SweetAlert Success Message --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: @json(session('success')),
        showConfirmButton: false, // This removes the button
        timer: 2000,              // Auto-closes after 2 seconds
        timerProgressBar: true
    });
</script>
@endif

{{-- SweetAlert Error Message --}}
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        showConfirmButton: false, // This removes the button
        timer: 2000,           
    });
</script>
@endif
        @livewireScripts
        @yield('scripts')
        
    </body>
</html>
