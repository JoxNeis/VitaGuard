@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-pencil-square text-primary"></i> Edit Article</h4>
                <p class="text-muted mb-0">Perbarui informasi artikel medis Anda di bawah ini.</p>
            </div>
            <a href="/portal/articles" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-edit-article">
                    @csrf

                    <h5 class="mb-3 text-primary font-weight-bold"><i class="bi bi-info-circle"></i> 1. Article Information
                    </h5>
                    <hr class="mt-0 mb-3">

                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="title" class="font-weight-bold">Article Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required
                                placeholder="Memuat judul...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="creator" class="font-weight-bold">Creator / Author</label>
                            <input type="text" class="form-control bg-light" id="creator" name="creator" readonly
                                placeholder="Memuat penulis...">
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label for="article_topic_id" class="font-weight-bold">Article Topic <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="article_topic_id" name="article_topic_id"
                                required>
                                <option value="" disabled selected>-- Memuat Data Topik... --</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 text-primary font-weight-bold"><i class="bi bi-card-text"></i> 2. Article Content
                    </h5>
                    <hr class="mt-0 mb-3">

                    <div class="row">
                        <div class="col-md-12 form-group mb-4">
                            <label for="content" class="font-weight-bold">Main Content <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="10" required
                                placeholder="Tulis ulang isi artikel..."></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">Article Image URL</label>

                            <div class="mt-2 mb-3">
                                <img id="image-preview" src="" alt="Preview Artikel" class="img-thumbnail"
                                    style="max-height: 350px; display: none;">
                                <span id="image-fallback" class="text-muted fst-italic">Memuat gambar...</span>
                            </div>

                            <input type="text" class="form-control" id="image-path" name="image" required
                                placeholder="Masukkan URL gambar baru...">
                            <small class="text-muted">Biarkan URL ini jika Anda tidak ingin mengubah gambar.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-light mr-2 px-4"
                            onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let pathSegments = window.location.pathname.split('/');
            let article_id = pathSegments[pathSegments.length - 2];
            
            function fetchTopicsAndLoadDetail() {
                $.ajax({
                    url: '/api/articles/topics',
                    method: 'GET',
                    success: function (res) {
                        if (res.success) {
                            let options = '<option value="" disabled>-- Pilih Topik --</option>';
                            res.data.forEach(topic => {
                                options += `<option value="${topic.id}">${topic.name}</option>`;
                            });
                            $('#article_topic_id').html(options);
                            
                            loadArticleDetail();
                        }
                    }
                });
            }
            
            function loadArticleDetail() {
                $.ajax({
                    url: `/api/articles/${article_id}/edit-data`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            let articleData = response.article;

                            $('#title').val(articleData.title);
                            $('#creator').val(articleData.creator);
                            
                            $('#article_topic_id').val(articleData.article_topic_id);

                            $('#content').val(articleData.content);
                            $('#image-path').val(articleData.image);

                            let imageUrl = articleData.image;
                            if (imageUrl && !imageUrl.startsWith('http')) {
                                imageUrl = '/storage/' + imageUrl;
                            }

                            $('#image-preview').attr('src', imageUrl).show();
                            $('#image-fallback').hide();
                        }
                    },
                    error: function () {
                        alert("Gagal mengambil data dari server.");
                    }
                })
            }

            fetchTopicsAndLoadDetail();

            $('#form-edit-article').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();
                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Updating...').prop('disabled', true);
                
                let formData = $(this).serialize();

                $.ajax({
                    url: `/api/articles/${article_id}/update`,
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            alert('Success! Data berhasil diperbarui.');
                            window.location.href = '/admin/articles';
                        }
                    },
                    error: function (xhr) {
                        btnSubmit.html(originalText).prop('disabled', false);
                        if (xhr.status != 200) {
                            alert('Server Error: Gagal menyimpan pembaruan ke database.');
                        }
                    }
                })
            })
        })
    </script>
@endsection