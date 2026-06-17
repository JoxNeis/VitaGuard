@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>Manajemen Data Dokter</h4>
                <p class="text-muted mb-0">Kelola informasi dokter, spesialisasi, dan status aktif.</p>
            </div>

            <a href="{{ route('doctor.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i>
                Tambah Dokter Baru
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="doctor-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Memuat data dokter dari server...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            function loadDoctors() {
                let container = $('#doctor-container');

                $.ajax({
                    url: `/api/admin/doctors/fetch`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let doctors = response.data;

                            let tableHtml = `
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="5%" class="text-center">ID</th>
                                                    <th width="30%">Nama Dokter</th>
                                                    <th width="25%">Spesialisasi</th>
                                                    <th width="20%">Alamat</th>
                                                    <th width="20%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;

                            doctors.forEach(doctor => {

                                let spcName = '-';
                                if (doctor.specialties && doctor.specialties.length > 0) {
                                    spcName = doctor.specialties
                                        .map(item => item.specialty?.name)
                                        .filter(name => name)
                                        .join(', ');
                                }

                                let fullName = [
                                    doctor.prefix_name,
                                    doctor.first_name,
                                    doctor.middle_name,
                                    doctor.last_name,
                                    doctor.suffix_name
                                ].filter(Boolean).join(' ');

                                tableHtml += `
                                    <tr>
                                        <td class="text-center">${doctor.username}</td>

                                        <td><strong>${fullName || '-'}</strong></td>

                                        <td>${spcName}</td>

                                        <td>${doctor.address || '-'}</td>

                                        <td class="text-center">
                                            <a href="doctors/${doctor.username}/edit" class="btn btn-warning btn-info text-white btn-edit" data-id="${doctor.username}">
                                                Edit
                                            </a>
                                            <a class="btn btn-danger btn-info text-white btn-delete" data-id="${doctor.username}">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                            tableHtml += `</tbody></table></div>`;
                            container.html(tableHtml);

                        } else {
                            container.html('<div class="alert alert-warning m-4 text-center">Belum ada data dokter yang terdaftar di sistem.</div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="alert alert-danger m-4 text-center">Terjadi kesalahan saat menghubungi server.</div>');
                    }
                });
            }

            loadDoctors();

            $(document).on('click', '.btn-edit', function () {
                let doctorId = $(this).data('id');
                console.log('Edit dokter dengan ID:', doctorId);
            });

            $(document).on('click', '.btn-delete', function () {
                let doctorId = $(this).data('id');
                if (confirm('Apakah Anda yakin ingin menghapus data dokter ini?')) {
                    console.log('Hapus dokter dengan ID:', doctorId);
                }
            });

        });
    </script>
@endsection