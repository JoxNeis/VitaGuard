@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-pencil-square text-info"></i> Edit Doctor Data</h4>
                <p class="text-muted mb-0">Mengubah informasi profil dari Dokter: <b class="text-dark"
                        id="txt-username-title"></b></p>
            </div>
            <a href="/admin/doctors" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-edit-doctor">
                    @csrf

                    <h5 class="mb-3 text-info font-weight-bold"><i class="bi bi-shield-lock"></i> 1. Account Credentials
                    </h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">Username</label>
                            <input type="text" class="form-control bg-light" id="username" readonly>
                            <small class="form-text text-muted">Primary Key / Username tidak dapat diubah.</small>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="password" class="font-weight-bold">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6"
                                placeholder="Kosongkan jika tidak ingin diubah">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-info font-weight-bold"><i class="bi bi-person-badge"></i> 2. Personal
                        Identities</h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="col-md-2 form-group mb-3">
                            <label for="prefix_name">Prefix Name</label>
                            <input type="text" class="form-control" id="prefix_name" name="prefix_name">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label for="middle_name">Middle Name</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="col-md-2 form-group mb-3">
                            <label for="suffix_name">Suffix Name</label>
                            <input type="text" class="form-control" id="suffix_name" name="suffix_name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="date_of_birth" class="font-weight-bold">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="phone_number" class="font-weight-bold">Phone Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="address" class="font-weight-bold">Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-info font-weight-bold"><i class="bi bi-hospital"></i> 3. Assignment &
                        Specialties</h5>
                    <hr class="mt-0 mb-3">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="district_id" class="font-weight-bold">District <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="district_id" name="district_id" required>
                                <option value="" disabled>-- Memuat Data --</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="specialties" class="font-weight-bold">Doctor Specialties <span
                                    class="text-danger">*</span></label>
                            <select multiple class="form-control custom-select" id="specialties" name="specialties[]"
                                required style="height: 110px;">
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-light mr-2 px-4"
                            onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-info text-white px-4" id="btn-submit">
                            <i class="bi bi-check-circle"></i> Update Doctor Data
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

            // Mengambil parameter username langsung dari segmen URL segmentasi web
            let pathSegments = window.location.pathname.split('/');
            let username = pathSegments[pathSegments.length - 2];
            
            function loadDoctorData() {
                $.ajax({
                    url: `/api/admin/doctors/${username}/edit-data`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            let doctor = response.doctor;
                            
                            $('#txt-username-title').text(doctor.username);
                            $('#username').val(doctor.username);
                            $('#email').val(doctor.user?.email || '-');
                            $('#phone_number').val(doctor.user?.phone_number || '-');
                            $('#prefix_name').val(doctor.prefix_name);
                            $('#first_name').val(doctor.first_name);
                            $('#middle_name').val(doctor.middle_name);
                            $('#last_name').val(doctor.last_name);
                            $('#suffix_name').val(doctor.suffix_name);
                            $('#date_of_birth').val(doctor.date_of_birth);
                            $('#address').val(doctor.address);
                            
                            let districtSelect = $('#district_id').empty();
                            response.districts.forEach(d => {
                                let selected = (d.id == doctor.district_id) ? 'selected' : '';
                                districtSelect.append(`<option value="${d.id}" ${selected}>${d.name || d.names}</option>`);
                            });
                            
                            let currentSpecs = doctor.specialties.map(s => s.specialty_id);
                            
                            let specialtiesSelect = $('#specialties').empty();
                            response.specialties.forEach(s => {
                                let selected = currentSpecs.includes(s.id) ? 'selected' : '';
                                specialtiesSelect.append(`<option value="${s.id}" ${selected}>${s.name}</option>`);
                            });
                        }
                    },
                    error: function () {
                        alert('Gagal mengambil data detail dokter dari server.');
                    }
                });
            }

            loadDoctorData();
            
            $('#form-edit-doctor').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();
                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Updating...').prop('disabled', true);

                let formData = $(this).serialize();

                $.ajax({
                    url: `/api/admin/doctors/${username}/update`,
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
                            alert('Validation Error: Pastikan format email unik dan input data wajib terisi.');
                        } else {
                            alert('Server Error: Gagal menyimpan pembaruan ke database.');
                        }
                    }
                });
            });
        });
    </script>
@endsection