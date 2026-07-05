@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-file-earmark-plus text-primary"></i> Add New Article</h4>
                <p class="text-muted mb-0">Lengkapi formulir di bawah ini untuk menerbitkan artikel baru.</p>
            </div>
            <a href="/portal/articles" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-add-article">
                    @csrf

                    <h5 class="mb-3 text-primary font-weight-bold"><i class="bi bi-info-circle"></i> 1. Article Information
                    </h5>
                    <hr class="mt-0 mb-3">

                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label for="title" class="font-weight-bold">Article Title <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" maxlength="255" required
                                placeholder="Masukkan judul artikel yang menarik...">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="creator" class="font-weight-bold">Creator / Author <span
                                    class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="creator" name="creator" required>
                                <option value="" disabled selected>-- Memuat Data Creator... --</option>
                            </select>
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
                                placeholder="Tuliskan isi artikel Anda di sini..."></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group mb-3">
                            <label class="font-weight-bold">Image Upload Method <span class="text-danger">*</span></label>

                            <div class="mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input radio-image-type" type="radio" name="image_type"
                                        id="uploadPhotoInUrl" value="url" checked>
                                    <label class="form-check-label" for="uploadPhotoInUrl">URL Link</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input radio-image-type" type="radio" name="image_type"
                                        id="uploadPhotoInFile" value="file">
                                    <label class="form-check-label" for="uploadPhotoInFile">Upload File Lokal</label>
                                </div>
                            </div>

                            <div id="uploadImageContainer">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-3">
                        <button type="button" class="btn btn-light mr-2 px-4"
                            onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Publish Article
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
            function loadFormData() {
                let topicSelect = $('#article_topic_id');
                let creatorSelect = $('#creator');

                $.ajax({                    
                    url: '/api/articles/create-data',
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {

                            // 1. Masukkan Data Topik
                            topicSelect.empty();
                            topicSelect.append('<option value="" disabled selected>-- Pilih Topik Artikel --</option>');
                            response.article_topics.forEach(topic => {
                                topicSelect.append(`<option value="${topic.id}">${topic.name}</option>`);
                            });

                            // 2. Masukkan Data Creator
                            creatorSelect.empty();
                            creatorSelect.append('<option value="" disabled selected>-- Pilih Penulis/Creator --</option>');
                            response.creators.forEach(creator => {
                                // Asumsi tabel users Anda pakai kolom username
                                creatorSelect.append(`<option value="${creator.username}">${creator.username}</option>`);
                            });
                        }
                    },
                    error: function () {
                        topicSelect.html('<option value="" disabled selected>Gagal memuat topik</option>');
                        creatorSelect.html('<option value="" disabled selected>Gagal memuat creator</option>');
                    }
                });
            }
           
            loadFormData();

            function renderImageInput() {
                //get radio button value either file/url
                let type = $('input[name="image_type"]:checked').val();
                let container = $('#uploadImageContainer');

                if (type === 'url') {
                    container.html(`
                        <input type="url" class="form-control" id="image" name="image" required placeholder="https://contoh.com/gambar.jpg">
                        <small class="form-text text-muted">Masukkan tautan URL gambar secara langsung.</small>
                    `);
                } else if (type === 'file') {
                    container.html(`
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <small class="form-text text-muted">Format yang didukung: JPG, PNG, JPEG. Maks: 2MB.</small>
                    `);
                }
            }

            $('.radio-image-type').on('change', function () {
                renderImageInput();
            });

            renderImageInput();

            $('#form-add-article').on('submit', function (e) {
                e.preventDefault();

                let btnSubmit = $('#btn-submit');
                let originalText = btnSubmit.html();

                btnSubmit.html('<span class="spinner-border spinner-border-sm"></span> Publishing...').prop('disabled', true);
                $('.form-control').removeClass('is-invalid');

                let formElement = document.getElementById('form-add-article');
                let formData = new FormData(formElement);

                $.ajax({
                    url: '/api/admin/articles/store',
                    method: 'POST',
                    processData: false,
                    // Wajib false untuk file upload
                    data: formData,
                    // Wajib false untuk file upload
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            alert('Success! ' + response.message);
                            window.location.href = '/admin/articles';
                        }
                    },
                    error: function (xhr) {
                        btnSubmit.html(originalText).prop('disabled', false);

                        if (xhr.status === 422) {
                            alert('Validation Error: Pastikan format gambar dan isian wajib sudah benar.');
                        } else {
                            alert('Server Error: Gagal menyimpan artikel ke database.');
                        }
                    }
                });
            });
        });
    </script>
@endsection