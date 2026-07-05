@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-people-fill text-primary"></i> Manajemen Data User</h4>
                <p class="text-muted mb-0">Kelola seluruh akun pengguna, hak akses (role), dan status aktif.</p>
            </div>

            <a href="/admin/users/create" class="btn btn-primary shadow-sm">
                <i class="bi bi-person-plus-fill"></i>
                Tambah User Baru
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="user-container">
                    <div class="text-center py-5" id="loading-indicator">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Memuat data user dari server...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalDeleteUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus data akun dengan Username: <strong id="delete-username-text" class="text-danger"></strong>?</p>
                    <small class="text-muted">Peringatan: Jika akun ini memiliki data profil atau transaksi terkait, penghapusan ini mungkin memengaruhi data tersebut.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Ya, Hapus Akun</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('scripts')
    <script>
        $(document).ready(function () {
            let usernameToDelete = null;

            function loadUsers() {
                let container = $('#user-container');

                $.ajax({
                    url: `/api/admin/users/fetch`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let users = response.data;

                            let tableHtml = `
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="15%" class="text-center">Username</th>
                                            <th width="20%">Email</th>
                                            <th width="15%" class="text-center">Role</th>
                                            <th width="10%" class="text-center">Status</th>
                                            <th width="20%">Terakhir Login</th>
                                            <th width="20%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                <tbody>
                            `;

                            $.each(users, function (index, user) {                                
                                let roleBadge = '';
                                if (user.role === 'admin') {
                                    roleBadge = '<span class="badge bg-danger px-2 py-1">Admin</span>';
                                } else if (user.role === 'doctor') {
                                    roleBadge = '<span class="badge bg-info px-2 py-1">Doctor</span>';
                                } else if (user.role === 'facility_admin') {
                                    roleBadge = '<span class="badge bg-warning px-2 py-1">Facility Admin</span>';
                                } else {
                                    roleBadge = '<span class="badge bg-primary px-2 py-1">Member</span>';
                                }
                                
                                let statusBadge = user.status === 'active' 
                                    ? '<span class="badge bg-success px-2 py-1">Active</span>' 
                                    : '<span class="badge bg-secondary px-2 py-1">Suspended</span>';
                                                                   
                                let lastLogin = user.last_login_at 
                                    ? new Date(user.last_login_at).toLocaleString('id-ID') 
                                    : '<em class="text-muted">Belum pernah login</em>';

                                tableHtml += `
                                <tr id="tr_${user.username}">
                                    <td class="text-center"><strong>${user.username}</strong></td>
                                    <td>${user.email}</td>
                                    <td class="text-center">${roleBadge}</td>
                                    <td class="text-center">${statusBadge}</td>
                                    <td><small>${lastLogin}</small></td>
                                    <td class="text-center">
                                        <a href="/admin/users/${user.username}/show" class="btn btn-sm btn-warning text-white" title="Detail">
                                            Detail
                                        </a>                                        
                                        <button type="button" class="btn btn-sm btn-danger text-white btn-delete" data-id="${user.username}" title="Hapus">
                                            Delete
                                         </button>
                                    </td>
                                </tr>
                                `;
                            });

                            tableHtml += `</tbody></table></div>`;
                            container.html(tableHtml);

                        } else {
                            container.html('<div class="alert alert-warning m-4 text-center"><i class="bi bi-info-circle"></i> Belum ada data user yang terdaftar di sistem.</div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="alert alert-danger m-4 text-center"><i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan saat mengambil data user dari server.</div>');
                    }
                });
            }
            
            loadUsers();
            
            $(document).on('click', '.btn-delete', function () {
                usernameToDelete = $(this).data('id');
                $('#delete-username-text').text(usernameToDelete);
                $('#modalDeleteUser').modal('show');
            });
            
            $('#btn-confirm-delete').on('click', function () {
                let btn = $(this);
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menghapus...').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: `/api/admin/users/${usernameToDelete}/destroy`,
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        btn.html(originalText).prop('disabled', false);
                        $('#modalDeleteUser').modal('hide');

                        if (response.success) {                                                        
                            $('#tr_' + usernameToDelete).fadeOut(400, function () {
                                $(this).remove();
                                                               
                                if ($('tbody tr:visible').length === 0) {
                                    loadUsers();
                                }
                            });
                        } else {
                            alert('Gagal: ' + (response.message || 'Terjadi kesalahan.'));
                        }
                    },
                    error: function (xhr) {
                        btn.html(originalText).prop('disabled', false);
                        alert('Terjadi kesalahan pada server saat mencoba menghapus data.');
                    }
                });
            });

        });
    </script>
@endsection