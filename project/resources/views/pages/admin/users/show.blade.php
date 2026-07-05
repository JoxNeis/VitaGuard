@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>
                    <i class="bi bi-person-badge text-primary"></i>
                    Detail User
                </h4>
                <p class="text-muted mb-0">
                    Informasi lengkap kredensial akun dan status pengguna.
                </p>
            </div>
            <a href="/admin/users" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
        <div class="card shadow border-0">
            <div class="card-header bg-white py-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                        style="width:70px;height:70px;font-size:28px;font-weight:bold;">
                        <span id="avatar-initial">U</span>
                    </div>
                    <div class="ms-3 ml-3">
                        <h4 class="mb-1" id="header_username">-</h4>
                        <span id="header_role_badge">
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <h5 class="text-primary mb-3">
                    <i class="bi bi-shield-lock"></i>
                    Informasi Kredensial
                </h5>
                <table class="table table-borderless mb-4">
                    <tr>
                        <th width="220">Username</th>
                        <td id="username">-</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td id="email">-</td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td id="phone_number">-</td>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <td><em class="text-muted">Disembunyikan (Terenkripsi)</em></td>
                    </tr>
                </table>

                <hr>
                <h5 class="text-primary mb-3 mt-4">
                    <i class="bi bi-activity"></i>
                    Status & Aktivitas
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="220">Role / Hak Akses</th>
                        <td id="role">-</td>
                    </tr>
                    <tr>
                        <th>Status Akun</th>
                        <td id="status">-</td>
                    </tr>
                    <tr>
                        <th>Terakhir Login</th>
                        <td id="last_login_at">-</td>
                    </tr>
                    <tr>
                        <th>Tanggal Terdaftar</th>
                        <td id="created_at">-</td>
                    </tr>
                    <tr>
                        <th>Terakhir Diperbarui</th>
                        <td id="updated_at">-</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer bg-light p-3 text-right border-top-0">
                <a href="#" id="btn-edit" class="btn btn-warning shadow-sm">
                    <i class="bi bi-pencil-square"></i>
                    Edit Akun
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let path = window.location.pathname.split('/');
            let targetUsername = path[path.length - 2];

            $("#btn-edit").attr("href", `/admin/users/${targetUsername}/edit`);

            $.ajax({
                url: `/api/admin/users/${targetUsername}/detail`,
                method: "GET",
                success: function (response) {
                    if (response.success) {
                        let user = response.user;

                        $("#avatar-initial").text(user.username.charAt(0).toUpperCase());
                        $("#header_username").text(user.username);                        
                        let roleBadge = '';
                        if (user.role === 'Admin') {
                            roleBadge = '<span class="badge bg-danger">Admin</span>';
                        } else if (user.role === 'Doctor') {
                            roleBadge = '<span class="badge bg-info">Doctor</span>';
                        } else if (user.role === 'Facility_admin') {
                            roleBadge = '<span class="badge bg-warning">Facility Admin</span>';
                        } else {
                            roleBadge = '<span class="badge bg-primary">Member</span>';
                        }
                        $("#header_role_badge").html(roleBadge);

                        $("#username").text(user.username);
                        $("#email").text(user.email);
                        $("#phone_number").text(user.phone_number ?? "-");

                        let roleText = user.role.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                        $("#role").text(roleText);

                        let statusBadge = user.status === 'active'
                            ? '<span class="badge bg-success px-2 py-1">Active</span>'
                            : '<span class="badge bg-secondary px-2 py-1">Suspended</span>';
                        $("#status").html(statusBadge);

                        let formatDateTime = (dateString) => {
                            if (!dateString) return "-";
                            return new Date(dateString).toLocaleString('id-ID', {
                                year: 'numeric', month: 'long', day: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            });
                        };

                        $("#last_login_at").text(user.last_login_at ? formatDateTime(user.last_login_at) : "Belum pernah login");
                        $("#created_at").text(formatDateTime(user.created_at));
                        $("#updated_at").text(formatDateTime(user.updated_at));
                    }
                },
                error: function () {
                    alert("Gagal mengambil data user dari server.");
                }
            });
        });
    </script>
@endsection