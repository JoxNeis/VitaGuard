@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>Manajemen Data Member</h4>
                <p class="text-muted mb-0">Kelola informasi pasien / member yang terdaftar di sistem.</p>
            </div>

            <a href="/admin/members/create" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus"></i>
                Tambah Member Baru
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="member-container">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Memuat data member dari server...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalDeleteMember" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data member dengan Username: <strong id="delete-username-text"
                            class="text-danger"></strong>?</p>
                    <small class="text-muted">Peringatan: Akun user dan profil member akan dihapus permanen.</small>
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

            function loadMembers() {
                let container = $('#member-container');

                $.ajax({
                    url: `/api/admin/members/fetch`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let members = response.data;

                            let tableHtml = `
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="15%" class="text-center">Username</th>
                                                <th width="25%">Nama Lengkap</th>
                                                <th width="10%">Gender</th>
                                                <th width="15%">Tgl Lahir</th>
                                                <th width="20%">Alamat</th>
                                                <th width="15%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                `;

                            members.forEach(member => {
                                // gabungin nama depan, tengah, dan belakang
                                let fullName = [
                                    member.first_name,
                                    member.middle_name,
                                    member.last_name
                                ].filter(Boolean).join(' ');
                                
                                let genderLabel = member.gender === 'male' ? 'Laki-laki' : 'Perempuan';

                                tableHtml += `
                                    <tr id="tr_${member.username}">
                                        <td class="text-center">${member.username}</td>
                                        <td><strong>${fullName}</strong></td>
                                        <td>${genderLabel}</td>
                                        <td>${member.date_of_birth}</td>
                                        <td>${member.address || '-'}</td>
                                        <td class="text-center">
                                            <a href="/admin/members/${member.username}/show" class="btn btn-sm btn-warning text-white">
                                                Detail
                                            </a>                                            
                                            <button type="button" class="btn btn-sm btn-danger text-white btn-delete" data-id="${member.username}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    `;
                            });

                            tableHtml += `</tbody></table></div>`;
                            container.html(tableHtml);

                        } else {
                            container.html('<div class="alert alert-warning m-4 text-center">Belum ada data member yang terdaftar di sistem.</div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="alert alert-danger m-4 text-center">Terjadi kesalahan saat menghubungi server.</div>');
                    }
                });
            }
           
            loadMembers();
            
            $(document).on('click', '.btn-delete', function () {
                usernameToDelete = $(this).data('id');
                $('#delete-username-text').text(usernameToDelete);
                $('#modalDeleteMember').modal('show');
            });
            
            $('#btn-confirm-delete').on('click', function () {
                let btn = $(this);
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menghapus...').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: `/api/admin/members/${usernameToDelete}/destroy`,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (data) {
                        btn.html(originalText).prop('disabled', false);
                        $('#modalDeleteMember').modal('hide');

                        // Pengecekan disesuaikan dengan standar response()->json(['success' => true])
                        if (data.success) {
                            $('#tr_' + usernameToDelete).fadeOut(300, function () {
                                $(this).remove();

                                // Jika tabel kosong setelah menghapus, muat ulang tampilannya
                                if ($('tbody tr:visible').length === 0) {
                                    loadMembers();
                                }
                            });
                        } else {
                            alert('Gagal: ' + (data.message || 'Terjadi kesalahan tidak dikenal.'));
                        }
                    },
                    error: function (xhr) {
                        btn.html(originalText).prop('disabled', false);
                        alert('Terjadi kesalahan pada server saat menghapus data.');
                    }
                });
            });

        });
    </script>
@endsection