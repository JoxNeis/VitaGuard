@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>Articles</h4>
                <p class="text-muted mb-0">Manage Articles</p>
            </div>

            <a href="{{ route('articles.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle"></i>
                Add new Article
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="article-container">
                    <div id="loading-indicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading...</p>
                    </div>

                    <div class="table-responsive" id="table-wrapper" style="display: none;">
                        <table class="table table-hover table-striped mb-0" id="articles-table">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="text-center">Id</th>
                                    <th width="30%">Title</th>
                                    <th width="25%">Creator</th>
                                    <th width="20%">Topic</th>
                                    <th width="20%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('modals')
    <div class="modal fade" id="modalDeleteArticle" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus Article dengan judul: <strong id="delete-username-text"
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
            function loadArticles() {
                let container = $('#article-container');
                let tbody = $('#articles-table tbody');
                let loadingIndicator = $('#loading-indicator');
                let tableWrapper = $('#table-wrapper');

                $.ajax({
                    url: `/api/admin/articles/fetch`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.data && response.data.data.length > 0) {
                            let articles = response.data.data;
                            let rowsHtml = '';

                            articles.forEach(article => {
                                let creatorName = article.creator ? article.creator.username : '-'; // Tambahkan pengecekan if exists
                                let topicName = article.topic ? article.topic.name : '-';           // Tambahkan pengecekan if exists

                                rowsHtml += `
                                    <tr id="tr_${article.id}">
                                        <td class="text-center">${article.id}</td>
                                        <td><strong>${article.title}</strong></td>
                                        <td>${creatorName}</td>
                                        <td>${topicName}</td>
                                        <td class="text-center">
                                            <a href="/admin/articles/${article.id}/show" class="btn btn-sm btn-warning text-white">Detail</a>                                            
                                            <button type="button" class="btn btn-sm btn-danger text-white btn-delete" data-id="${article.id}">
                                                    Delete
                                            </button>                    
                                        </td>
                                    </tr>
                                `;
                            });
                            
                            tbody.html(rowsHtml);
                            
                            loadingIndicator.hide();
                            tableWrapper.show();

                        }
                        else {
                            loadingIndicator.hide();
                            container.html('<div class="alert alert-warning m-4 text-center">No article data</div>');
                        }
                    },
                    error: function (response) {

                    },
                });
            }
            loadArticles()

            $(document).on('click', '.btn-delete', function () {
                articleToDelete = $(this).data('id');

                $('#delete-username-text').text(articleToDelete);

                $('#modalDeleteArticle').modal('show');
            });

            $('#btn-confirm-delete').on('click', function () {
                let btn = $(this);
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menghapus...').prop('disabled', true);

                $.ajax({
                    url: `/api/admin/articles/${articleToDelete}/destroy`,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        btn.html(originalText).prop('disabled', false);
                        $('#modalDeleteArticle').modal('hide');

                        if (response.success) {
                            $('#tr_' + articleToDelete).fadeOut(300, function () {
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
                })
            })
        })

    </script>
@endsection