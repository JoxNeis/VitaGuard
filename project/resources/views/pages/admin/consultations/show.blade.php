@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-file-earmark-medical text-primary"></i> Detail Konsultasi</h4>
                <p class="text-muted mb-0">Informasi lengkap mengenai jadwal, pasien, dan catatan medis.</p>
            </div>
            <a href="/portal/consultations" class="btn btn-outline-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow border-0">
            <div class="card-header bg-white py-4">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                        style="width:70px;height:70px;font-size:28px;font-weight:bold;">
                        <span id="avatar-initial">P</span>
                    </div>
                    <div class="ms-3 ml-3">
                        <h4 class="mb-1">Konsultasi #<span id="header_id">-</span></h4>
                        <a href="#" id="btn-chat" class="btn btn-primary shadow-sm">
                            <i class="bi bi-chat"></i> Lihat chat
                        </a>
                        <span id="payment_badge_header"></span>
                        <span id="status_badge_header" class="ml-1"></span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <h5 class="text-primary mb-3"><i class="bi bi-person-lines-fill"></i> Informasi Pasien</h5>
                <table class="table table-borderless mb-4">
                    <tr>
                        <th width="220">Nama Pasien</th>
                        <td id="patient_name">-</td>
                    </tr>
                    <tr>
                        <th>Username Pasien</th>
                        <td id="patient_username">-</td>
                    </tr>
                    <tr>
                        <th>Gender / Tgl Lahir</th>
                        <td id="patient_demography">-</td>
                    </tr>
                </table>
                <hr>

                <h5 class="text-primary mb-3 mt-4"><i class="bi bi-calendar-heart"></i> Jadwal & Dokter</h5>
                <table class="table table-borderless mb-4">
                    <tr>
                        <th width="220">Nama Dokter</th>
                        <td id="doctor_name">-</td>
                    </tr>
                    <tr>
                        <th>ID Sesi Online</th>
                        <td id="online_session_id">-</td>
                    </tr>
                    <tr>
                        <th>Waktu Mulai</th>
                        <td id="start_time">-</td>
                    </tr>
                    <tr>
                        <th>Waktu Selesai</th>
                        <td id="end_time">-</td>
                    </tr>
                </table>
                <hr>

                <h5 class="text-primary mb-3 mt-4"><i class="bi bi-receipt"></i> Detail Transaksi & Catatan</h5>
                <table class="table table-borderless">
                    <tr>
                        <th width="220">Biaya Konsultasi</th>
                        <td id="consultation_fee" class="font-weight-bold text-success">-</td>
                    </tr>
                    <tr>
                        <th>Waktu Pembayaran</th>
                        <td id="paid_at">-</td>
                    </tr>
                    <tr>
                        <th>Catatan Medis (Notes)</th>
                        <td>
                            <div class="p-3 bg-light rounded border" style="min-height: 80px;">
                                <span id="notes" class="text-muted">Memuat catatan...</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="card-footer bg-light p-3 text-right border-top-0">                
                <button class="btn btn-secondary ml-2" onclick="window.history.back()">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            let path = window.location.pathname.split('/');
            let targetId = path[path.length - 2];

            $("#btn-chat").attr("href", `/chat/${targetId}`);

            const formatCurrency = (amount) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(amount);
            };

            const formatDateTime = (dateString) => {
                if (!dateString) return "-";
                return new Date(dateString).toLocaleString('id-ID', {
                    year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            };

            $.ajax({
                url: `/api/consultations/${targetId}/detail`,
                method: "GET",
                success: function (res) {
                    if (res.success) {
                        let c = res.consultation;

                        $("#header_id").text(c.id);

                        if (c.paid_at) {
                            $("#payment_badge_header").html('<span class="badge badge-success px-2 py-1">Lunas</span>');
                        } else {
                            $("#payment_badge_header").html('<span class="badge badge-warning px-2 py-1">Belum Bayar</span>');
                        }

                        if (c.end_time) {
                            $("#status_badge_header").html('<span class="badge badge-secondary px-2 py-1">Selesai</span>');
                        } else {
                            $("#status_badge_header").html('<span class="badge badge-primary px-2 py-1">Aktif</span>');
                        }

                        //patient_user sesuai relasi model
                        let member = c.patient_data;
                        if (member) {
                            let patientFullName = [
                                member.first_name,
                                member.middle_name,
                                member.last_name
                            ].filter(Boolean).join(' ');

                            $("#avatar-initial").text(member.first_name.charAt(0).toUpperCase());
                            $("#patient_name").html(`<strong>${patientFullName}</strong>`);

                            $("#patient_demography").text(
                                `${member.gender === 'male' ? 'Laki-laki' : 'Perempuan'} / ${member.date_of_birth}`
                            );
                        } else {
                            $("#avatar-initial").text(c.patient.charAt(0).toUpperCase());
                            $("#patient_name").text("-");
                            $("#patient_demography").text("-");
                        }

                        $("#patient_username").text(member ? member.username : c.patient);

                        if (c.online_session) {
                            $("#online_session_id").text(c.online_session.id);
                            $("#consultation_fee").text(c.online_session.consultation_fee ? formatCurrency(c.online_session.consultation_fee) : "Rp 0");

                            let doctor = c.online_session.doctor_data;
                            if (doctor) {
                                let doctorName = [
                                    doctor.prefix_name,
                                    doctor.first_name,
                                    doctor.last_name,
                                    doctor.suffix_name
                                ].filter(Boolean).join(' ');
                                $("#doctor_name").html(`<strong>${doctorName}</strong>`);
                            } else {
                                $("#doctor_name").text("-");
                            }
                        }

                        $("#start_time").text(formatDateTime(c.start_time));
                        $("#end_time").text(formatDateTime(c.end_time));
                        $("#paid_at").text(c.paid_at ? formatDateTime(c.paid_at) : "Belum ada riwayat pembayaran");

                        if (c.notes) {
                            $("#notes").html(c.notes).removeClass('text-muted');
                        } else {
                            $("#notes").text("Tidak ada catatan medis.");
                        }
                    }
                },
                error: function () {
                    alert("Gagal memuat detail konsultasi.");
                }
            });
        });
    </script>
@endsection