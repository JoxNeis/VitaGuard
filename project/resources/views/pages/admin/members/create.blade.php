@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-person-plus text-primary"></i> Tambah Member Baru</h4>
                <p class="text-muted mb-0">Lengkapi data autentikasi dan profil untuk mendaftarkan pasien/member baru.</p>
            </div>
            <a href="/admin/members" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-add-member">
                    @csrf

                    <h5 class="mb-3 text-primary font-weight-bold"><i class="bi bi-shield-lock"></i> 1. Informasi Akun
                        (Login)</h5>
                    <hr class="mt-0 mb-3">

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="username" class="font-weight-bold">Username <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" maxlength="50" required
                                placeholder="Contoh: jdoe01">
                            <small class="text-muted">Digunakan untuk login. Harus unik dan tanpa spasi.</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="email" class="font-weight-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" maxlength="100" required
                                placeholder="email@contoh.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="password" class="font-weight-bold">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password_hashed" minlength="8" required
                                placeholder="Minimal 8 karakter">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="phone_number" class="font-weight-bold">Nomor Telepon</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" maxlength="20"
                                placeholder="Contoh: 081234567890">
                            <small class="text-muted">Opsional (Boleh dikosongkan).</small>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary font-weight-bold"><i class="bi bi-person-lines-fill"></i> 2. Profil
                        Pribadi</h5>
                    <hr class="mt-0 mb-3">

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="first_name" class="font-weight-bold">Nama Depan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100"
                                required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="middle_name" class="font-weight-bold">Nama Tengah</label>
                            <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="100">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="last_name" class="font-weight-bold">Nama Belakang <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100"
                                required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="gender" class="font-weight-bold">Jenis Kelamin <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="gender" name="gender" required>
                                <option value="" disabled selected>-- Pilih Gender --</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="date_of_birth" class="font-weight-bold">Tanggal Lahir <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="district_id" class="font-weight-bold">Wilayah (District) <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="district_id" name="district_id" required>
                                <option value="" disabled selected>-- Memuat Data Wilayah... --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group mb-4">
                            <label for="address" class="font-weight-bold">Alamat Lengkap <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="3" maxlength="255" required
                                placeholder="Tuliskan alamat lengkap..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-light mr-2 px-4" onclick="window.history.back()">Batal</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Data Member
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

        function loadFormData() {
                let districtSelect = $('#district_id');

                $.ajax({
                    url: '/api/admin/members/create-data',
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            districtSelect.empty();
                            districtSelect.append('<option value="" disabled selected>-- Pilih Wilayah --</option>');
                                                        
                            $.each(response.districts, function (index, district) {                                
                                let cityName = district.city ? district.city.name : 'Unknown City';
                                let districtName = district.name;
                                                                
                                let optionText = `${district.id} - ${cityName} - ${districtName}`;
                                
                                districtSelect.append(`<option value="${district.id}">${optionText}</option>`);
                            });
                        }
                    },
                    error: function () {
                        districtSelect.html('<option value="" disabled selected>Gagal memuat data wilayah</option>');
                    }
                });
            }

            loadFormData();
            $('#form-add-member').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop('disabled', true);

                $('.form-control').removeClass('is-invalid');

                let formData = new FormData($('#form-add-member')[0]);

                $.ajax({
                    url: '/api/admin/members/store',
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            alert('Sukses! ' + response.message);
                            $(location).attr('href', '/admin/members');
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
                            alert('Server Error: Gagal menyimpan data member ke database.');
                        }
                    }
                });
            });
        });
    </script>
@endsection