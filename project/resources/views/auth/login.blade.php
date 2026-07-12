@extends('layouts.auth')

@section('content')
    <div class="d-lg-flex half">
        <div class="bg order-1 order-md-2"
            style="background-image: url('{{ asset('assets/loginTemplate/doctor.webp') }}');"></div>
        <div class="contents order-2 order-md-1">

            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                        <h3>Login to <strong>VitaGuard</strong></h3>
                        <p class="mb-4">Login to explore more healthy facts and needs here!</p>

                        <form id="login-form">
                            @csrf
                            <div class="form-group first">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Input your username"
                                    id="username" autofocus required>

                            </div>

                            <div class="form-group last mb-3 mt-3">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Input your password" id="password" required>

                                @error('password')
                                    <span class="text-danger" style="font-size: 14px;">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <input type="submit" value="Log In" class="btn btn-block btn-primary mt-4">
                        </form>

                        <p class="text-center mt-3">Don't have any account? <a href="/register"
                                class="text-primary text-decoration-none">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/HttpService.js') }}"></script>
    <script>
        $(document).ready(function () {

            $('#login-form').on('submit', function (e) {                
                e.preventDefault();
                
                const formData = {
                    username: $('#username').val(),
                    password: $('#password').val(),
                    device_name: 'web-browser'
                };
                
                $('.validation-error').remove();
                
                HttpService.post(
                    "/api/auth/login",
                    formData,
                    
                    function (response) {
                        window.location.href = response.redirect_url;
                    },
                    
                    function (error) {
                        if (error.status === 422) {
                            let validationErrors = error.responseJSON.errors;

                            if (validationErrors.username) {
                                $('#username').after('<span class="text-danger validation-error" style="font-size: 14px;"><strong>' + validationErrors.username[0] + '</strong></span>');
                            }
                            if (validationErrors.password) {
                                $('#password').after('<span class="text-danger validation-error" style="font-size: 14px;"><strong>' + validationErrors.password[0] + '</strong></span>');
                            }
                        }
                        else {
                            alert('Login Gagal: ' + (error.responseJSON?.message || 'Terjadi kesalahan pada sistem.'));
                        }
                    }
                );
            });

        });
    </script>
@endsection