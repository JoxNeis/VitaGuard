<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('loginTemplate/fonts/icomoon/style.css') }}">
    <link rel="stylesheet" href="{{ asset('loginTemplate/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('loginTemplate/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('loginTemplate/css/style.css') }}">

    <title>VitaGuard - Login</title>
</head>

<body>

    <div class="d-lg-flex half">
        <div class="bg order-1 order-md-2" style="background-image: url('{{ asset('loginTemplate/images/doctor.webp') }}');"></div>
        <div class="contents order-2 order-md-1">

            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                        <h3>Login to <strong>VitaGuard</strong></h3>
                        <p class="mb-4">Login to explore more healthy facts and needs here!</p>
                        <form action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="form-group first">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Input your username" id="username" autofocus required>
                                <!-- display error message when failed to login -->
                                @error('username')
                                <span class="text-danger" style="font-size: 14px;">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group last mb-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Input your password" id="password" required>
                            </div>

                            <!-- <div class="d-flex mb-3 align-items-center">
                                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                                    <input type="checkbox" checked="checked" />
                                    <div class="control__indicator"></div>
                                </label>
                                <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>
                            </div> -->

                            <input type="submit" value="Log In" class="btn btn-block btn-primary">

                        </form>
                        <p class="text-center mt-3">Don't have any account? <a href="/register" class="text-primary text-decoration-none">Register</a></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('loginTemplate/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('loginTemplate/js/popper.min.js') }}"></script>
    <script src="{{ asset('loginTemplate/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('loginTemplate/js/main.js') }}"></script>
</body>

</html>