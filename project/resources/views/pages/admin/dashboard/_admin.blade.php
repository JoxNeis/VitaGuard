@extends('layouts.navbar.admin')

@section('content')
    <div class="container mt-4">
        <div class="mb-4">
            <h3>
                <i class="bi bi-speedometer2 text-primary"></i>
                Dashboard Administrator
            </h3>
            <p class="text-muted">
                Monitoring statistik sistem VitaGuard.
            </p>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-primary">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge display-4 text-primary"></i>
                        <h6 class="mt-3">Total Doctor</h6>
                        <h2 id="doctor-count">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-success">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-4 text-success"></i>
                        <h6 class="mt-3">Total Member</h6>
                        <h2 id="member-count">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-info">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text display-4 text-info"></i>
                        <h6 class="mt-3">Total Article</h6>
                        <h2 id="article-count">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check display-4 text-warning"></i>
                        <h6 class="mt-3">Total Booking</h6>
                        <h2 id="booking-count">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-danger">
                    <div class="card-body text-center">
                        <i class="bi bi-chat-dots display-4 text-danger"></i>
                        <h6 class="mt-3">Consultation Ongoing</h6>
                        <h2 id="ongoing-count">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card shadow-sm border-left-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle display-4 text-secondary"></i>
                        <h6 class="mt-3">Consultation Finished</h6>
                        <h2 id="finished-count">0</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <strong>Consultation Statistics</strong>
            </div>
            <div class="card-body">
                <canvas id="dashboardChart" height="100"></canvas>
            </div>
        </div>
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <strong>Database Explorer</strong>
            </div>
            <div class="card-body">
                <select id="table-selector" class="form-control w-25 mb-4">
                    <option selected disabled>Loading...</option>
                </select>
                <div id="table-container">
                    <div class="alert alert-info">
                        Pilih tabel untuk melihat data.
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
                url: "/api/dashboard/fetch",
                method: "GET",
                success: function (res) {
                    $("#doctor-count").text(res.data.totalDoctor);
                    $("#member-count").text(res.data.totalMember);
                    $("#article-count").text(res.data.totalArticle);
                    $("#booking-count").text(res.data.totalAppointment);
                    $("#ongoing-count").text(res.data.totalOngoingConsultation);
                    $("#finished-count").text(res.data.totalCompletedConsultation);
                    new Chart(document.getElementById("dashboardChart"), {
                        type: "bar",
                        data: {
                            labels: [
                                "Booking",
                                "Ongoing",
                                "Finished"
                            ],
                            datasets: [{
                                label: "Total",
                                data: [
                                    res.data.totalAppointment,
                                    res.data.totalOngoingConsultation,
                                    res.data.totalCompletedConsultation
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }

                    });

                }
            });

            $.ajax({
                url: '/api/admin/available-tables',
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        let selector = $('#table-selector');
                        selector.empty();
                        selector.append(
                            '<option selected disabled>-- Pilih Tabel --</option>'
                        );
                        response.data.forEach(function (table) {
                            selector.append(
                                `<option value="${table.id}">${table.name}</option>`
                            );
                        });
                    }
                },
                error: function () {
                    $('#table-selector').html(
                        '<option disabled>Gagal memuat tabel</option>'
                    );
                }
            });
            $('#table-selector').on('change', function () {
                let selectedTable = $(this).val();
                let container = $('#table-container');
                container.html('<div class="text-center"><div class="spinner-border text-primary"></div><p>Memuat data...</p></div>');
                $.ajax({
                    url: `/api/admin/fetch-table/${selectedTable}`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let rows = response.data;
                            let columns = Object.keys(rows[0]);
                            let tableHtml = `
                                            <h5>Menampilkan Data: ${selectedTable.toUpperCase()}</h5>
                                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                                <table class="table table-striped table-bordered">
                                                    <thead><tr>
                                        `;
                            columns.forEach(col => {
                                tableHtml += `<th>${col.toUpperCase()}</th>`;
                            });
                            tableHtml += `</tr></thead><tbody>`;
                            rows.forEach(row => {
                                tableHtml += `<tr>`;
                                columns.forEach(col => {
                                    tableHtml += `<td>${row[col] !== null ? row[col] : '-'}</td>`;
                                });
                                tableHtml += `</tr>`;
                            });
                            tableHtml += `</tbody></table></div>`;
                            container.html(tableHtml);

                        } else {
                            container.html('<div class="alert alert-warning">Tabel ini masih kosong.</div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="alert alert-danger">Gagal mengambil data dari server.</div>');
                    }
                });
            });

        });
    </script>
@endsection