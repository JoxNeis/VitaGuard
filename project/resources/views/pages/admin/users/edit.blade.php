@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-pencil-square text-primary"></i> Edit Data User</h4>
                <p class="text-muted mb-0">Perbarui kredensial, role, dan status aktif pengguna.</p>
            </div>
            <a href="/admin/users" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-edit-user">
                    @csrf

                    <h5 class="mb-3 text-primary font-weight-bold"><i class="bi bi-shield-lock"></i> Informasi Akun</h5>
                    <hr class="mt-0 mb-4">

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="username" class="font-weight-bold">Username <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="username" name="username" readonly>
                            <small class="text-muted">Username tidak dapat diubah.</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="100" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="password_hashed" class="font-weight-bold">Password Baru</label>
                            <input type="password" class="form-control" id="password_hashed" name="password_hashed"
                                minlength="8" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="phone_number" class="font-weight-bold">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" maxlength="20">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary font-weight-bold"><i class="bi bi-gear-fill"></i> Pengaturan Akses
                    </h5>
                    <hr class="mt-0 mb-4">

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="role" class="font-weight-bold">Role (Hak Akses) <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="role" name="role" required>
                                <option value="" disabled>-- Pilih Role --</option>
                                <option value="member">Member (Pasien)</option>
                                <option value="doctor">Doctor</option>
                                <option value="facility_admin">Facility Admin</option>
                                <option value="admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="status" class="font-weight-bold">Status Akun <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="status" name="status" required>
                                <option value="" disabled>-- Pilih Status --</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended (Ditangguhkan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-4">
                        <button type="button" class="btn btn-light mr-2 px-4" onclick="window.history.back()">Batal</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let pathSegments = window.location.pathname.split('/');
            let targetUsername = pathSegments[pathSegments.length - 2];

            function loadUserData() {
                $.ajax({
                    url: `/api/admin/users/${targetUsername}/edit-data`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            let user = response.user;

                            $('#username').val(user.username);
                            $('#email').val(user.email);
                            $('#phone_number').val(user.phone_number);
                            $('#role').val(user.role);
                            $('#status').val(user.status);
                        }
                    },
                    error: function () {
                        alert("Gagal mengambil data user dari server.");
                    }
                });
            }

            loadUserData();

            $('#form-edit-user').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop('disabled', true);
                $('.form-control').removeClass('is-invalid');

                let formData = new FormData($('#form-edit-user')[0]);

                $.ajax({
                    url: `/api/admin/users/${targetUsername}/update`,
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            alert('Sukses! ' + response.message);
                            $(location).attr('href', '/admin/users');
                        }
                    },
                    error: function (xhr) {
                        btnSubmit.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let errorMessage = 'Gagal validasi:\n';

                            $.each(errors, function (field, messages) {
                                errorMessage += `- ${messages[0]}\n`;
                                $(`#${field}`).addClass('is-invalid');
                            });

                            alert(errorMessage);
                        } else {
                            alert('Server Error: Gagal memperbarui data user.');
                        }
                    }
                });
            });

        });
    </script>
@endsection