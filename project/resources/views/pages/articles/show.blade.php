@extends('layouts.navbar.main')
@section('content')
    <div class="bg-light py-5">
        <div class="container">
            <div id="loading-indicator" class="text-center py-5">
                <div class="spinner-border text-primary" style="width:3rem;height:3rem"></div>
                <p class="text-muted mt-3 mb-0">Memuat artikel...</p>
            </div>

            <div id="article-container" class="d-none">
                <div class="row justify-content-center">
                    <div class="col-lg-9">
                        <nav class="mb-3">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="/articles" class="text-decoration-none">Pusat Edukasi</a>
                                </li>
                                <li class="breadcrumb-item active" id="breadcrumb-title">Artikel</li>
                            </ol>
                        </nav>

                        <span class="badge rounded-pill bg-primary px-3 py-2 mb-3 fs-6" id="article-topic">Kategori</span>
                        <h1 class="display-5 fw-bold mb-4" id="article-title">Judul Artikel</h1>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center fw-bold flex-shrink-0 mr-2"
                                         style="width:60px;height:60px;font-size:22px;">
                                        <span id="author-initial">A</span>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="mb-1" id="article-author">Penulis</h5>
                                        <div class="text-muted small">
                                            <span><i class="bi bi-calendar3 me-1"></i><span id="article-date"></span></span>
                                            <span class="mx-2">•</span>
                                            <span><i class="bi bi-clock me-1"></i><span id="reading-time">5 menit baca</span></span>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                        <img id="article-image" class="img-fluid rounded-4 shadow mb-5 w-100" style="max-height:500px;object-fit:cover;">

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-5">
                                <div id="article-content" class="article-body fs-5 lh-lg"></div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <a href="/articles" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Artikel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-primary rounded-circle position-fixed d-none" id="backToTop"
            style="bottom:24px;right:24px;width:48px;height:48px;z-index:1000;box-shadow:0 8px 20px rgba(0,0,0,.2);">
        <i class="bi bi-arrow-up"></i>
    </button>

    <style>
    .article-body p{ margin-bottom:1.6rem; }
    .article-body h2, .article-body h3, .article-body h4{ font-weight:700; margin-top:2rem; margin-bottom:1rem; }
    .article-body img{ max-width:100%; border-radius:12px; margin:2rem 0; }
    .article-body ul, .article-body ol{ margin-bottom:1.5rem; }
    .article-body blockquote{ border-left:4px solid #0d6efd; background:#f8f9fa; padding:1rem 1.5rem; border-radius:8px; font-style:italic; }
    #article-container{ animation:fadeInUp .5s ease; }
    @keyframes fadeInUp{ from{ opacity:0; transform:translateY(12px); } to{ opacity:1; transform:translateY(0); } }
    </style>
@endsection

@section('scripts')
    <script>
    $(function () {
        let articleId = '{{ $articleId }}';

        $.get(`/api/articles/${articleId}/detail`, function (response) {
            if (!response.success) return;
            let article = response.article;
            let title = article.title;
            let content = article.content;
            let topic = article.topic?.name ?? 'Kesehatan';
            let author = article.creator?.username ?? 'Tim Medis';
            let image = article.image ?? '{{ asset("assets/img/default-article.jpg") }}';
            let date = new Date(article.created_at).toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
            let words = $('<div>').html(content).text().split(/\s+/).length;
            let readingTime = Math.max(1, Math.ceil(words / 200));

            $('#breadcrumb-title').text(title);
            $('#article-topic').text(topic);
            $('#article-title').text(title);
            $('#article-author').text(author);
            $('#author-initial').text(author.charAt(0).toUpperCase());
            $('#article-date').text(date);
            $('#reading-time').text(readingTime + ' menit baca');
            $('#article-image').attr('src', image);
            $('#article-content').html(content);
            $('#waShareLink').attr('href', 'https://wa.me/?text=' + encodeURIComponent(title + ' ' + window.location.href));

            $('#loading-indicator').fadeOut(250, function () {
                $('#article-container').removeClass('d-none');
            });
        }).fail(function () {
            $('#loading-indicator').html('<div class="alert alert-danger">Artikel tidak ditemukan.</div>');
        });

        $('#copyLinkBtn').on('click', function () {
            navigator.clipboard.writeText(window.location.href);
            let original = $(this).html();
            $(this).html('<i class="bi bi-check2 me-2"></i>Tautan disalin!');
            setTimeout(() => $(this).html(original), 1500);
        });

        $(window).on('scroll', function () {
            $('#backToTop').toggleClass('d-none', $(window).scrollTop() < 400);
        });

        $('#backToTop').on('click', function () {
            $('html, body').animate({ scrollTop: 0 }, 400);
        });
    });
    </script>
@endsection