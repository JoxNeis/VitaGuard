@extends('layouts.navbar.admin')

@section('content')
    <div class="container-fluid py-4">
       
        <div id="dashboard-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Menyiapkan ruang kerja Anda...</p>
        </div>

        
        <div id="dashboard-content" style="display: none;">            
            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="fw-bold">Selamat Datang, dr. <span id="doctor-name">...</span>!</h3>
                    <p class="text-muted">Berikut adalah ringkasan jadwal dan aktivitas Anda hari ini.</p>
                </div>
            </div>
           
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Janji Temu Hari Ini</p>
                                        <h5 class="font-weight-bolder mb-0 text-primary">
                                            <span id="count-appointments">0</span> Pasien
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-primary text-white shadow text-center rounded-circle"
                                        style="width: 48px; height: 48px; line-height: 48px;">
                                        <i class="bi bi-calendar-check fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-capitalize font-weight-bold">Artikel Publikasi</p>
                                        <h5 class="font-weight-bolder mb-0 text-success">
                                            <span id="count-articles">0</span> Artikel
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon icon-shape bg-success text-white shadow text-center rounded-circle"
                                        style="width: 48px; height: 48px; line-height: 48px;">
                                        <i class="bi bi-journal-text fs-5"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Jadwal Terdekat -->
            <div class="row">
                <div class="col-lg-12 col-md-12 mb-md-0 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header pb-0 bg-white border-bottom pb-3">
                            <div class="row">
                                <div class="col-lg-6 col-7">
                                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-info me-2"></i> Jadwal
                                        Terdekat Hari Ini</h6>
                                </div>
                                <div class="col-lg-6 col-5 my-auto text-end">
                                    <a href="/appointments/doctor" class="text-sm text-primary text-decoration-none">Lihat
                                        Semua</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0 table-hover">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Pasien</th>
                                            <th
                                                class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                                Jam</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="upcoming-appointments-list">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            
            $.ajax({
                url: '/api/dashboard/fetch',
                method: 'GET',
                success: function (response) {
                    if (response.success && response.data.role === 'doctor') {
                        let data = response.data;
                       
                        $('#doctor-name').text(data.doctor_name);
                        $('#count-appointments').text(data.appointments_today);
                        $('#count-articles').text(data.total_articles);

                        let tbody = $('#upcoming-appointments-list');
                        tbody.empty();

                        if (data.upcoming_appointments.length > 0) {
                            data.upcoming_appointments.forEach(function (appointment) {
                                
                                let timeObj = new Date('1970-01-01T' + appointment.time + 'Z');
                                let timeString = appointment.time.substring(0, 5);

                                let rowHtml = `
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm fw-bold">${appointment.patient}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-sm font-weight-bold">${timeString} WIB</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning">Menunggu</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="/appointments/doctor" class="btn btn-sm btn-outline-primary mb-0">Mulai Sesi</a>
                                        </td>
                                    </tr>
                                `;
                                tbody.append(rowHtml);
                            });
                        } else {
                            tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada jadwal antrean terdekat.</td></tr>');
                        }
                    
                        $('#dashboard-loading').fadeOut(300, function () {
                            $('#dashboard-content').fadeIn(300);
                        });
                    }
                },
                error: function () {
                    $('#dashboard-loading').html('<div class="alert alert-danger mx-4">Gagal memuat data dasbor.</div>');
                }
            });
        });
    </script>
@endsection