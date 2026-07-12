@extends('layouts.auth')

@section('content')
    <div class="d-lg-flex half">
        <div class="bg order-1 order-md-2"
            style="background-image: url('{{ asset('assets/loginTemplate/doctor.webp') }}');"></div>
        <div class="contents order-2 order-md-1">

            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-8">
                        <h3>Register to <strong>VitaGuard</strong></h3>
                        <p class="mb-4">Create your member account here!</p>

                        {{-- Step indicator --}}
                        <div class="reg-steps mb-4">
                            <div class="reg-step active" data-step-indicator="1">
                                <div class="reg-step-circle">1</div>
                                <div class="reg-step-label">Account</div>
                            </div>
                            <div class="reg-step-line"></div>
                            <div class="reg-step" data-step-indicator="2">
                                <div class="reg-step-circle">2</div>
                                <div class="reg-step-label">Personal Info</div>
                            </div>
                        </div>

                        <form id="register-form" novalidate>
                            @csrf

                            {{-- ================= STEP 1 ================= --}}
                            <div class="reg-panel" data-step="1">

                                <div class="form-group first">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" class="form-control"
                                        placeholder="Input your username" id="username" autofocus required>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Input your email"
                                        id="email" required>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control"
                                        placeholder="Input your phone number (optional)" id="phone_number">
                                </div>

                                <div class="form-group mt-3">
                                    <label for="password">Password</label>
                                    <div class="reg-password-wrap">
                                        <input type="password" name="password" class="form-control"
                                            placeholder="Input your password" id="password" minlength="8" required>
                                        <button type="button" class="reg-toggle-eye" data-target="password"
                                            aria-label="Show password">
                                            <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                                <path fill="currentColor"
                                                    d="M12 5c-5.5 0-9.5 4-11 7 1.5 3 5.5 7 11 7s9.5-4 11-7c-1.5-3-5.5-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-2a3 3 0 100-6 3 3 0 000 6z" />
                                            </svg>
                                            <svg class="icon-eye-slash" viewBox="0 0 24 24" width="18" height="18"
                                                style="display:none">
                                                <path fill="currentColor"
                                                    d="M3.3 2.3L2 3.6l3.2 3.2C3.4 8.2 1.9 9.9 1 12c1.5 3 5.5 7 11 7 1.9 0 3.6-.5 5.1-1.3l3 3 1.3-1.3L3.3 2.3zM12 17c-4.1 0-7.3-2.9-9-5 .8-1.2 2-2.6 3.6-3.7l2 2A5 5 0 0012 16c.4 0 .8 0 1.1-.1l1.6 1.6c-.9.3-1.8.5-2.7.5zm.9-8.9l4.9 4.9c.1-.3.2-.6.2-1a5 5 0 00-5.1-5c-.3 0-.6.1-1 .2z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="reg-strength-bar mt-2">
                                        <div class="reg-strength-fill" id="strength-fill"></div>
                                    </div>
                                    <small class="reg-strength-text" id="strength-text">Minimum 8 characters</small>
                                </div>

                                <div class="form-group last mt-3">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <div class="reg-password-wrap">
                                        <input type="password" name="password_confirmation" class="form-control"
                                            placeholder="Repeat your password" id="password_confirmation" required>
                                        <button type="button" class="reg-toggle-eye" data-target="password_confirmation"
                                            aria-label="Show password">
                                            <svg class="icon-eye" viewBox="0 0 24 24" width="18" height="18">
                                                <path fill="currentColor"
                                                    d="M12 5c-5.5 0-9.5 4-11 7 1.5 3 5.5 7 11 7s9.5-4 11-7c-1.5-3-5.5-7-11-7zm0 12a5 5 0 110-10 5 5 0 010 10zm0-2a3 3 0 100-6 3 3 0 000 6z" />
                                            </svg>
                                            <svg class="icon-eye-slash" viewBox="0 0 24 24" width="18" height="18"
                                                style="display:none">
                                                <path fill="currentColor"
                                                    d="M3.3 2.3L2 3.6l3.2 3.2C3.4 8.2 1.9 9.9 1 12c1.5 3 5.5 7 11 7 1.9 0 3.6-.5 5.1-1.3l3 3 1.3-1.3L3.3 2.3zM12 17c-4.1 0-7.3-2.9-9-5 .8-1.2 2-2.6 3.6-3.7l2 2A5 5 0 0012 16c.4 0 .8 0 1.1-.1l1.6 1.6c-.9.3-1.8.5-2.7.5zm.9-8.9l4.9 4.9c.1-.3.2-.6.2-1a5 5 0 00-5.1-5c-.3 0-.6.1-1 .2z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <small class="reg-match-text" id="match-text"></small>
                                </div>

                                <button type="button" class="btn btn-block btn-primary mt-4" id="btn-next">Next</button>
                            </div>

                            {{-- ================= STEP 2 ================= --}}
                            <div class="reg-panel" data-step="2" style="display:none;">

                                <div class="row">
                                    <div class="col-md-4 form-group first">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" class="form-control" placeholder="First name"
                                            id="first_name" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" class="form-control"
                                            placeholder="Middle name (optional)" id="middle_name">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Last name"
                                            id="last_name" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group mt-3">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="" selected disabled>Select gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group mt-3">
                                        <label for="date_of_birth">Date of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control" id="date_of_birth"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group mt-3">
                                    <label for="address">Address</label>
                                    <textarea name="address" class="form-control" id="address" rows="2" required></textarea>
                                </div>

                                <div class="form-group last mb-3 mt-3">
                                    <label for="district_id">District</label>
                                    <select name="district_id" id="district_id" class="form-control" required>
                                        <option value="" selected disabled>Loading districts...</option>
                                    </select>
                                </div>

                                <div class="reg-btn-row mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-back">Back</button>
                                    <input type="submit" value="Register" class="btn btn-primary" id="btn-submit">
                                </div>
                            </div>

                        </form>

                        <p class="text-center mt-3">Already have an account? <a href="/login"
                                class="text-primary text-decoration-none">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .reg-steps {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reg-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            opacity: .5;
            transition: opacity .25s ease;
        }

        .reg-step.active,
        .reg-step.completed {
            opacity: 1;
        }

        .reg-step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid var(--bs-primary, #0d6efd);
            color: var(--bs-primary, #0d6efd);
            background: #fff;
            transition: background .25s ease, color .25s ease;
        }

        .reg-step.completed .reg-step-circle {
            background: var(--bs-primary, #0d6efd);
            color: #fff;
        }

        .reg-step-label {
            font-size: 12px;
            color: #555;
        }

        .reg-step-line {
            width: 60px;
            height: 2px;
            background: #dcdcdc;
            margin: 0 10px 20px;
        }

        .reg-panel {
            animation: reg-fade-in .3s ease;
        }

        @keyframes reg-fade-in {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reg-password-wrap {
            position: relative;
        }

        .reg-password-wrap input {
            padding-right: 40px;
        }

        .reg-toggle-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            color: #888;
            cursor: pointer;
            line-height: 0;
        }

        .reg-toggle-eye:hover {
            color: #333;
        }

        .reg-strength-bar {
            width: 100%;
            height: 5px;
            background: #eee;
            border-radius: 3px;
            overflow: hidden;
        }

        .reg-strength-fill {
            height: 100%;
            width: 0%;
            background: #dc3545;
            transition: width .25s ease, background .25s ease;
        }

        .reg-strength-text {
            display: block;
            margin-top: 4px;
            color: #888;
            font-size: 12.5px;
        }

        .reg-match-text {
            display: block;
            margin-top: 6px;
            font-size: 12.5px;
        }

        .reg-match-text.ok {
            color: #198754;
        }

        .reg-match-text.bad {
            color: #dc3545;
        }

        .reg-btn-row {
            display: flex;
            gap: 10px;
        }

        .reg-btn-row .btn {
            flex: 1;
        }

        .validation-error {
            display: block;
            margin-top: 4px;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('js/HttpService.js') }}"></script>
    <script>
        $(document).ready(function () {

            const step1Fields = ['username', 'email', 'phone_number', 'password', 'password_confirmation'];
            const step2Fields = ['first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'address', 'district_id'];

            loadDistricts();
            bindPasswordToggle();
            bindPasswordStrength();
            bindPasswordMatch();

            // ---------- STEP NAVIGATION ----------
            $('#btn-next').on('click', function () {
                if (!validatePanel(1)) return;
                goToStep(2);
            });

            $('#btn-back').on('click', function () {
                goToStep(1);
            });

            function goToStep(step) {
                $('.reg-panel').hide();
                $('.reg-panel[data-step="' + step + '"]').show();

                $('.reg-step').removeClass('active completed');
                $('.reg-step[data-step-indicator="' + step + '"]').addClass('active');
                if (step === 2) {
                    $('.reg-step[data-step-indicator="1"]').addClass('completed');
                }
            }

            function validatePanel(step) {
                let valid = true;
                $('.reg-panel[data-step="' + step + '"] [required]').each(function () {
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        valid = false;
                        return false;
                    }
                });
                return valid;
            }

            // ---------- PASSWORD SHOW/HIDE ----------
            function bindPasswordToggle() {
                $('.reg-toggle-eye').on('click', function () {
                    const targetId = $(this).data('target');
                    const $input = $('#' + targetId);
                    const isPassword = $input.attr('type') === 'password';

                    $input.attr('type', isPassword ? 'text' : 'password');
                    $(this).find('.icon-eye').toggle(!isPassword);
                    $(this).find('.icon-eye-slash').toggle(isPassword);
                });
            }

            // ---------- PASSWORD STRENGTH ----------
            function bindPasswordStrength() {
                $('#password').on('input', function () {
                    const val = $(this).val();
                    let score = 0;

                    if (val.length >= 8) score++;
                    if (/[A-Z]/.test(val)) score++;
                    if (/[0-9]/.test(val)) score++;
                    if (/[^A-Za-z0-9]/.test(val)) score++;

                    const levels = [
                        { width: '0%', color: '#dc3545', text: 'Minimum 8 characters' },
                        { width: '25%', color: '#dc3545', text: 'Weak password' },
                        { width: '55%', color: '#fd7e14', text: 'Medium password' },
                        { width: '80%', color: '#ffc107', text: 'Good password' },
                        { width: '100%', color: '#198754', text: 'Strong password' },
                    ];

                    const level = val.length === 0 ? levels[0] : levels[score];
                    $('#strength-fill').css({ width: level.width, background: level.color });
                    $('#strength-text').text(level.text);

                    checkMatch();
                });
            }

            // ---------- PASSWORD MATCH ----------
            function bindPasswordMatch() {
                $('#password_confirmation').on('input', checkMatch);
            }

            function checkMatch() {
                const pass = $('#password').val();
                const confirm = $('#password_confirmation').val();
                const $text = $('#match-text');

                if (confirm.length === 0) {
                    $text.text('').removeClass('ok bad');
                    return;
                }

                if (pass === confirm) {
                    $text.text('Passwords match').removeClass('bad').addClass('ok');
                } else {
                    $text.text('Passwords do not match').removeClass('ok').addClass('bad');
                }
            }

            // ---------- LOAD DISTRICTS ----------
            function loadDistricts() {
                $.ajax({
                    url: "/api/fetch-districts",
                    method: "GET",
                    success: function (response) {
                        const $district = $('#district_id');
                        $district.empty().append('<option value="" selected disabled>Select district</option>');

                        response.districts.forEach(function (district) {
                            $district.append(
                                $('<option>', { value: district.id, text: district.name })
                            );
                        });
                    },
                    error: function () {
                        $('#district_id').empty().append('<option value="" selected disabled>Failed to load districts</option>');
                    }
                });
            }

            // ---------- SUBMIT ----------
            $('#register-form').on('submit', function (e) {
                e.preventDefault();

                if (!validatePanel(2)) return;

                const formData = {
                    username: $('#username').val(),
                    email: $('#email').val(),
                    phone_number: $('#phone_number').val(),
                    password: $('#password').val(),
                    password_confirmation: $('#password_confirmation').val(),
                    first_name: $('#first_name').val(),
                    middle_name: $('#middle_name').val(),
                    last_name: $('#last_name').val(),
                    gender: $('#gender').val(),
                    date_of_birth: $('#date_of_birth').val(),
                    address: $('#address').val(),
                    district_id: $('#district_id').val(),
                };

                $('.validation-error').remove();
                $('#btn-submit').prop('disabled', true).val('Registering...');

                HttpService.post(
                    "/api/auth/register",
                    formData,

                    function (response) {
                        alert('Registrasi berhasil! Silakan login.');
                        window.location.href = response.redirect_url;
                    },

                    function (error) {
                        $('#btn-submit').prop('disabled', false).val('Register');

                        if (error.status === 422) {
                            let validationErrors = error.responseJSON.errors;
                            let hasStep1Error = Object.keys(validationErrors).some(f => step1Fields.includes(f));

                            if (hasStep1Error) {
                                goToStep(1);
                            }

                            Object.keys(validationErrors).forEach(function (field) {
                                const $field = $('#' + field);
                                if ($field.length) {
                                    $field.after(
                                        '<span class="text-danger validation-error" style="font-size: 14px;"><strong>' +
                                        validationErrors[field][0] + '</strong></span>'
                                    );
                                }
                            });
                        } else {
                            alert('Registrasi Gagal: ' + (error.responseJSON?.message || 'Terjadi kesalahan pada sistem.'));
                        }
                    }
                );
            });

        });
    </script>
@endsection