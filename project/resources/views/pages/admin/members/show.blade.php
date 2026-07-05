@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>
                    <i class="bi bi-person-vcard text-primary"></i>
                    Detail Member
                </h4>
                <p class="text-muted mb-0">
                    Informasi lengkap akun dan profil member.
                </p>
            </div>
            <a href="/admin/members" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
        <div class="card shadow border-0">
            <div class="card-header bg-white py-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                        style="width:70px;height:70px;font-size:28px;font-weight:bold;">
                        <span id="avatar-initial">M</span>
                    </div>
                    <div class="ms-3 ml-3">
                        <h4 class="mb-1" id="full_name">-</h4>
                        <span class="badge badge-success">
                            Member
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h5 class="text-primary mb-3">
                    <i class="bi bi-shield-lock"></i>
                    Informasi Akun
                </h5>
                <table class="table table-borderless">
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
                </table>
                <hr>                
                <h5 class="text-primary mb-3">
                    <i class="bi bi-person-lines-fill"></i>
                    Profil Pribadi
                </h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="220">Nama Depan</th>
                        <td id="first_name"></td>
                    </tr>
                    <tr>
                        <th>Nama Tengah</th>
                        <td id="middle_name"></td>
                    </tr>
                    <tr>
                        <th>Nama Belakang</th>
                        <td id="last_name"></td>
                    </tr>
                    <tr>
                        <th>Jenis Kelamin</th>
                        <td id="gender"></td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td id="date_of_birth"></td>
                    </tr>
                    <tr>
                        <th>Wilayah</th>
                        <td id="district"></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td id="address"></td>
                    </tr>
                </table>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="#" id="btn-edit" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i>
                    Edit Member
                </a>
                <button class="btn btn-secondary" onclick="window.history.back()">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let path = window.location.pathname.split('/');
            let username = path[path.length - 2];
            $("#btn-edit").attr("href", `/admin/members/${username}/edit`);
            $.ajax({
                url: `/api/admin/members/${username}/detail`,
                method: "GET",
                success: function (response) {
                    let member = response.member;
                    let user = member.user;

                    let fullname =
                        member.first_name + " " +
                        (member.middle_name ?? "") + " " +
                        member.last_name;

                    fullname = fullname.replace(/\s+/g, ' ').trim();

                    $("#avatar-initial").text(
                        member.first_name.charAt(0).toUpperCase()
                    );

                    $("#full_name").text(fullname);
                    $("#username").text(member.username);
                    $("#email").text(user ? user.email : "-");
                    $("#phone_number").text(user?.phone_number ?? "-");
                    $("#first_name").text(member.first_name);
                    $("#middle_name").text(member.middle_name ?? "-");
                    $("#last_name").text(member.last_name);
                    $("#gender").text(member.gender == "male"? "Laki-laki": "Perempuan");
                    $("#date_of_birth").text(member.date_of_birth);
                    if (member.district) {
                        $("#district").text(
                            member.district.city.name +
                            " - " +
                            member.district.name
                        );
                    }
                    $("#address").text(member.address);
                },
                error: function () {
                    alert("Gagal mengambil data member.");
                }
            });
        });
    </script>
@endsection