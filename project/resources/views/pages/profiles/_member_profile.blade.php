```blade
@extends('layouts.navbar.main')

@section('content')
    <div class="container py-4">
        <div class="mb-4">
            <h2 class="fw-bold">Profil Saya</h2>
            <p class="text-muted mb-0">Kelola informasi akun dan data pribadi Anda.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex justify-content-center align-items-center mb-3"
                            style="width:90px;height:90px;font-size:36px;font-weight:bold;">
                            <span id="avatar-initial">M</span>
                        </div>

                        <h4 class="fw-bold mb-1" id="member-fullname">Memuat...</h4>

                        <span class="badge bg-primary mb-3">
                            Pasien / Member
                        </span>

                        <p class="text-muted mb-3" id="member-username">@username</p>

                        <hr>

                        <div class="text-start">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Status</span>
                                <span class="fw-semibold" id="member-status">-</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Login Terakhir</span>
                                <span class="fw-semibold text-end" id="member-last-login">-</span>
                            </div>

                            <div class="d-grid">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-grid w-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>
                            Data Diri Pasien
                        </h5>
                    </div>

                    <div class="card-body p-4">
                        <form id="form-update-profile">
                            <h6 class="text-muted mb-3">Informasi Akun</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control bg-light" id="input-username" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control bg-light" id="input-email" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" class="form-control" id="input-phone">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status Akun</label>
                                    <input type="text" class="form-control bg-light" id="input-status" readonly>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h6 class="text-muted mb-3">Informasi Pribadi</h6>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nama Depan</label>
                                    <input type="text" class="form-control" id="input-first-name">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nama Tengah</label>
                                    <input type="text" class="form-control" id="input-middle-name">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nama Belakang</label>
                                    <input type="text" class="form-control" id="input-last-name">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="input-gender">
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" id="input-dob">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" rows="4" id="input-address"></textarea>
                            </div>

                            <input type="hidden" id="input-district-id">

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary px-4" id="btn-save-profile">
                                    <i class="bi bi-floppy me-2"></i>
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

                    const user = response.user;
                    const data = response.profileData;

                    const fullName = [
                        data.first_name,
                        data.middle_name,
                        data.last_name
                    ].filter(Boolean).join(' ');

                    $('#avatar-initial').text(data.first_name.charAt(0).toUpperCase());
                    $('#member-fullname').text(fullName);
                    $('#member-username').text('@' + user.username);
                    $('#member-status').text(user.status.charAt(0).toUpperCase() + user.status.slice(1));

                    if (user.last_login_at) {
                        $('#member-last-login').text(new Date(user.last_login_at).toLocaleString('id-ID'));
                    } else {
                        $('#member-last-login').text('Belum pernah');
                    }

                    $('#input-username').val(user.username);
                    $('#input-email').val(user.email);
                    $('#input-phone').val(user.phone_number);
                    $('#input-status').val(user.status);

                    $('#input-first-name').val(data.first_name);
                    $('#input-middle-name').val(data.middle_name);
                    $('#input-last-name').val(data.last_name);
                    $('#input-gender').val(data.gender);
                    $('#input-dob').val(data.date_of_birth);
                    $('#input-address').val(data.address);
                    $('#input-district-id').val(data.district_id);
                },
                error: function () {
                    alert('Gagal memuat data profil pasien.');
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

                        first_name: $('#input-first-name').val(),
                        middle_name: $('#input-middle-name').val(),
                        last_name: $('#input-last-name').val(),

                        gender: $('#input-gender').val(),
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
```