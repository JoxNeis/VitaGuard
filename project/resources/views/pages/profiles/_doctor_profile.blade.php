@extends('layouts.navbar.admin')

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center flex-shrink-0"
                            style="width:70px;height:70px;font-size:28px;font-weight:bold;">
                            <span id="avatar-initial">P</span>
                        </div>

                        <div class="ms-3 flex-grow-1">
                            <h5 class="fw-bold mb-1" id="doctor-fullname">Memuat...</h5>
                            <span class="badge bg-success">Dokter</span>

                            <p class="text-muted small mt-2 mb-2" id="doctor-username">@username</p>

                            <div>
                                <h5 class="text-warning mb-0" id="doctor-rating">0.00 ★</h5>
                                <small class="text-muted" id="doctor-rating-count">0 Reviews</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="bi bi-person-lines-fill text-success me-2"></i>Data Dokter</h5>
                    </div>

                    <div class="card-body">
                        <form id="form-update-profile">
                            <h6 class="text-muted mb-3">Informasi Akun</h6>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control bg-light" id="input-email" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="input-phone">
                                </div>
                            </div>

                            <hr>

                            <h6 class="text-muted mb-3 mt-4">Informasi Pribadi</h6>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Gelar Depan</label>
                                    <input type="text" class="form-control" id="input-prefix">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Nama Depan</label>
                                    <input type="text" class="form-control" id="input-first-name">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Nama Tengah</label>
                                    <input type="text" class="form-control" id="input-middle-name">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Nama Belakang</label>
                                    <input type="text" class="form-control" id="input-last-name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gelar Belakang</label>
                                    <input type="text" class="form-control" id="input-suffix">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="input-dob">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" id="input-address" rows="3"></textarea>
                            </div>

                            <input type="hidden" id="input-district-id">

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-success px-4" id="btn-save-profile">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $.ajax({
                url: '/api/profile/fetch',
                method: 'GET',
                success: function (response) {
                    if (!response.success) return;

                    let user = response.user;
                    let data = response.profileData;

                    let fullName = [
                        data.prefix_name,
                        data.first_name,
                        data.middle_name,
                        data.last_name,
                        data.suffix_name
                    ].filter(Boolean).join(' ');

                    $('#doctor-fullname').text(fullName);
                    $('#doctor-username').text('@' + user.username);
                    $('#doctor-rating').text(parseFloat(data.rating_avg ?? 0).toFixed(2) + ' ★');
                    $('#doctor-rating-count').text(data.rating_count + ' Reviews');

                    $('#input-email').val(user.email);
                    $('#input-phone').val(user.phone_number);

                    $('#input-prefix').val(data.prefix_name);
                    $('#input-first-name').val(data.first_name);
                    $('#input-middle-name').val(data.middle_name);
                    $('#input-last-name').val(data.last_name);
                    $('#input-suffix').val(data.suffix_name);

                    $('#input-dob').val(data.date_of_birth);
                    $('#input-address').val(data.address);
                    $('#input-district-id').val(data.district_id);
                },
                error: function () {
                    alert('Gagal memuat data profil dokter.');
                }
            });

            $('#btn-save-profile').click(function () {

                $.ajax({
                    url: '/api/profile/update',
                    method: 'POST',

                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    
                    data: {
                        email: $('#input-email').val(),
                        phone_number: $('#input-phone').val(),

                        prefix_name: $('#input-prefix').val(),
                        first_name: $('#input-first-name').val(),
                        middle_name: $('#input-middle-name').val(),
                        last_name: $('#input-last-name').val(),
                        suffix_name: $('#input-suffix').val(),

                        date_of_birth: $('#input-dob').val(),

                        address: $('#input-address').val(),
                        district_id: $('#input-district-id').val()
                    },

                    success: function (response) {
                        alert(response.message);
                    },

                    error: function (xhr) {
                        console.log(xhr.responseJSON);
                    }
                });

            });
        });
    </script>
@endsection