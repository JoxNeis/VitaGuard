@extends('layouts.navbar.main')
@section('content')
    <div id="carouselExampleIndicators" class="carousel slide carousel-custom" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <picture>
                    <source srcset="/assets/images/hero-bg.webp" type="image/webp">
                    <img class="d-block w-100" src="/assets/images/hero-bg.jpg">
                </picture>

                <div class="carousel-caption d-none d-md-block">
                    <h5>Template Nav admin bisa diakses dengan login</h5>
                    <p>username: reza_h | password: RezaPwd99!</p>
                </div>
            </div>

            <div class="carousel-item">
                <picture>
                    <source srcset="/assets/images/hero-bg.webp" type="image/webp">
                    <img class="d-block w-100" src="/assets/images/hero-bg.jpg">
                </picture>

                <div class="carousel-caption d-none d-md-block">
                    <h5>Template Nav admin bisa diakses dengan login</h5>
                    <p>username: reza_h | password: RezaPwd99!</p>
                </div>
            </div>

            <div class="carousel-item">
                <picture>
                    <source srcset="/assets/images/doctor.webp" type="image/webp">
                    <img class="d-block w-100" src="/assets/images/hero-bg.jpg">
                </picture>

                <div class="carousel-caption d-none d-md-block">
                    <h5>Template Nav admin bisa diakses dengan login</h5>
                    <p>username: reza_h | password: RezaPwd99!</p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
    <div class="container mt-5">
        <h4>Artikel Kesehatan Untuk Anda</h4>
        <div class="d-flex align-items-center mb-4">
            <button type="button" class="btn btn-outline-primary mr-2">Primary</button>
            <button type="button" class="btn btn-outline-secondary mr-2">Secondary</button>
            <a href="/artikel" class="text-primary">
                Lihat semua artikel &raquo;
            </a>
        </div>

        <div class="container" id="article-container">
            <div class="text-center" id="loading-spinner">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat artikel terbaru...</p>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {            
            $.ajax({
                url: "/api/articles/latest",
                method: "GET",
                success: function (response) {
                    if (response.success) {
                        let articles = response.data;
                        let htmlContent = '';

                        articles.forEach(function (article) {
                            htmlContent += `
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="row g-0">
                                        <div class="col-md-2">
                                            <img src="${article.image || '/assets/images/default-article.jpg'}" class="img-fluid rounded-start" alt="${article.title}">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title">${article.title}</h5>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2">Info Kesehatan</span>
                                                    <p class="card-text mb-0"><small class="text-muted">Diperbarui pada ${new Date(article.updated_at).toLocaleDateString('id-ID')}</small></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        $('#article-container').html(htmlContent);
                    }
                },
                error: function () {
                    $('#article-container').html('<p class="text-danger">Gagal memuat artikel. Silakan muat ulang halaman.</p>');
                }
            });
        });
    </script>
@endsection