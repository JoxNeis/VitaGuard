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

@push('modals')
    <div class="modal fade" id="modalDeleteDoctor" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data dokter dengan Username: <strong id="delete-username-text"
                            class="text-danger"></strong>?</p>
                    <small class="text-muted">Peringatan: Data yang dihapus tidak dapat dikembalikan.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Ya, Hapus Data</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('scripts')
    <script>
        $(document).ready(function () {
            let usernameToDelete = null;

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
                                            <tr id="tr_${doctor.username}">
                                                <td class="text-center">${doctor.username}</td>
                                                <td><strong>${fullName || '-'}</strong></td>
                                                <td>${spcName}</td>
                                                <td>${doctor.address || '-'}</td>
                                                <td class="text-center">
                                                    <a href="doctors/${doctor.username}/edit" class="btn btn-sm btn-info text-white btn-edit">
                                                        Edit
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger text-white btn-delete" data-id="${doctor.username}">
                                                        Delete
                                                    </button>
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

            $(document).on('click', '.btn-delete', function () {
                usernameToDelete = $(this).data('id');

                $('#delete-username-text').text(usernameToDelete);

                $('#modalDeleteDoctor').modal('show');
            });

            $('#btn-confirm-delete').on('click', function () {
                let btn = $(this);
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menghapus...').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: `/api/admin/doctors/${usernameToDelete}/destroy`,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        btn.html(originalText).prop('disabled', false);
                        $('#modalDeleteDoctor').modal('hide');

                        if (data.status === "oke") {                            
                            $('#tr_' + usernameToDelete).fadeOut(300, function () {
                                $(this).remove();
                            });
                        } else {
                            alert('Gagal: ' + data.msg);
                        }
                    },
                    error: function () {
                        btn.html(originalText).prop('disabled', false);
                        alert('Terjadi kesalahan pada server saat menghapus data. Pastikan tidak ada data terkait.');
                    }
                });
            });

        });
    </script>
@endsection