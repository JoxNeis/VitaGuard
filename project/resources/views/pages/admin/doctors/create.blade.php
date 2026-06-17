@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-person-plus-fill text-primary"></i> Add New Doctor</h4>
                <p class="text-muted mb-0">Silakan isi formulir di bawah ini dengan lengkap sesuai dengan skema data rumah
                    sakit.</p>
            </div>
            <a href="/admin/doctors" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-add-doctor">
                    @csrf

                    <h5 class="mb-3 text-primary font-weight-bold"><i class="bi bi-shield-lock"></i> 1. Account Credentials
                    </h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="username" class="font-weight-bold">Username <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" maxlength="50" required
                                placeholder="Masukkan username unik (contoh: budi.santoso)">
                            <small class="form-text text-muted">Akan digunakan sebagai Primary Key unik di database.</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="password" class="font-weight-bold">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6" required
                                placeholder="Minimal 6 karakter kombinasi">
                            <small class="form-text text-muted">Kata sandi awal untuk akun dokter masuk ke sistem.</small>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary font-weight-bold"><i class="bi bi-person-badge"></i> 2. Personal
                        Identities</h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="dokter@rumah-sakit.com">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label for="phone_number" class="font-weight-bold">Phone Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required
                                    placeholder="08123456789">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 form-group mb-3">
                            <label for="prefix_name">Prefix Name</label>
                            <input type="text" class="form-control" id="prefix_name" name="prefix_name" maxlength="20"
                                placeholder="dr. / Prof.">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100"
                                required placeholder="Budi">
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="100"
                                placeholder="Eka">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100" required
                                placeholder="Santoso">
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label for="suffix_name">Suffix Name</label>
                            <input type="text" class="form-control" id="suffix_name" name="suffix_name" maxlength="100"
                                placeholder="Sp.A / M.Kes">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="date_of_birth" class="font-weight-bold">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="col-md-8 form-group mb-3">
                            <label for="address" class="font-weight-bold">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" maxlength="255" required
                                placeholder="Masukkan alamat lengkap rumah / domisili">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary font-weight-bold"><i class="bi bi-hospital"></i> 3. Assignment &
                        Specialties</h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="district_id" class="font-weight-bold">District <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="district_id" name="district_id" required>
                                <option value="" disabled selected>-- Memuat Data Wilayah... --</option>
                            </select>
                            <small class="form-text text-muted">Pilih wilayah penempatan penugasan dokter.</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="specialties" class="font-weight-bold">Doctor Specialties <span
                                    class="text-danger">*</span></label>
                            <select multiple class="form-control custom-select" id="specialties" name="specialties[]"
                                required style="height: 110px;">
                            </select>
                            <small class="form-text text-muted">Tahan tombol <b>CTRL</b> (Windows) atau <b>CMD</b> (Mac)
                                untuk memilih lebih dari 1.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-light mr-2 px-4"
                            onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Save Doctor Data
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
            function initCreateForm() {
                $.ajax({
                    url: '/api/admin/doctors/create-data',
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {                            
                            let districtSelect = $('#district_id');
                            districtSelect.empty();
                            districtSelect.append('<option value="" disabled selected>-- Select District ID --</option>');

                            response.districts.forEach(function (district) {
                                districtSelect.append(`<option value="${district.id}">${district.name || district.names}</option>`);
                            });

                            //option specialties
                            let specialtiesSelect = $('#specialties');
                            specialtiesSelect.empty();

                            response.specialties.forEach(function (specialty) {
                                specialtiesSelect.append(`<option value="${specialty.id}">${specialty.name}</option>`);
                            });
                        }
                    },
                    error: function () {
                        alert('Gagal mengambil data referensi wilayah atau spesialisasi dari server.');
                    }
                });
            }            
            initCreateForm();

            $('#form-add-doctor').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                btnSubmit.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving Data...').prop('disabled', true);
                $('.form-control').removeClass('is-invalid');

                let formData = $(this).serialize();

                $.ajax({
                    url: '/api/admin/doctors/store',
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            alert('Success! ' + response.message);
                            window.location.href = '/admin/doctors';
                        }
                    },
                    error: function (xhr) {
                        btnSubmit.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.username) {
                                alert('Validation Error: ' + errors.username[0]);
                                $('#username').addClass('is-invalid').focus();
                            } else {
                                alert('Validation Error: Pastikan seluruh inputan wajib bertanda (*) terisi dengan benar.');
                            }
                        } else {
                            alert('Server Error: Gagal memproses penyimpanan ke database. Hubungi Developer.');
                        }
                    }
                });
            });

        });
    </script>
@endsection