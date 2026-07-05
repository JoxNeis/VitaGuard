<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VitaGuards</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- GANTI BARIS INI -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            margin: 0;
        }

        .carousel-custom .carousel-item {
            height: 100vh;
        }

        .carousel-custom img {
            height: 100%;
            object-fit: cover;
        }

        .container {
            margin-top: 24px;
        }

        /* Biar navbar sm carousel ga overlap */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 10;

        }

        .navbar-brand {
            color: #000000;
        }

        .navbar {
            transition: all 0.8s ease;
        }

        .navbar.scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <!-- Change navbar style class when scrolled -->
    @if(Request::is('/', 'home'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navbar = document.getElementById("navbar");

            window.addEventListener("scroll", function() {
                if (window.scrollY > 10) {
                    navbar.classList.add("scrolled", "navbar-light", "bg-white");
                    navbar.classList.remove("navbar-dark", "bg-transparent");
                } else {
                    navbar.classList.remove("scrolled", "navbar-light", "bg-white");
                    navbar.classList.add("navbar-dark", "bg-transparent");
                }
            });
        });
    </script>
    @endif

    @auth
        @include('layouts.navbar.member')
    @endauth

    @guest
        @include('layouts.navbar.guest')
    @endguest

    @yield('content')
    @yield('scripts')

    @stack('modal')
</body>

</html>