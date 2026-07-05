@extends('layouts.navbar.main')

@section('content')
    <div class="bg-primary text-white py-5 mb-5">
        <div class="container text-center">
            <h1 class="fw-bold">Pusat Edukasi Kesehatan</h1>
            <p class="lead mb-0">Temukan artikel medis, tips pola hidup, dan info kesehatan terpercaya dari para ahli.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h3 class="fw-bold text-dark border-bottom border-primary border-3 pb-2 mb-0">
                Artikel Terbaru
            </h3>

            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search"></i>
                </span>
                <input type="search" class="form-control" id="search-article" placeholder="Cari artikel...">
            </div>
        </div>

        <div class="row" id="newest-articles-container">
            <div class="col-12 text-center py-4 loading-indicator">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2">Memuat artikel terbaru...</p>
            </div>
        </div>

        <div id="dynamic-popular-topics"></div>
    </div>

    <style>
        .article-card {
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .article-img {
            height: 200px;
            object-fit: cover;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(function () {
            const badgeColors = ['bg-success', 'bg-info text-dark', 'bg-warning text-dark', 'bg-danger'];

            function renderArticleCard(article, badgeColor = 'bg-primary') {
                let title = article.title || 'Judul Artikel';
                let content = article.content || 'Deskripsi singkat artikel kesehatan...';
                let topicName = article.topic?.name || 'Kesehatan';
                let author = (typeof article.creator === 'object' && article.creator !== null)
                    ? (article.creator.username || article.creator.name || 'Tim Medis')
                    : (article.creator || 'Tim Medis');
                let date = article.created_at || 'Hari ini';
                let imageUrl = article.image || '{{ asset("assets/img/default-article.jpg") }}';
                let url = `/articles/${article.id}`;

                return `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm article-card">
                            <img src="${imageUrl}" class="card-img-top article-img" alt="${title}">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge ${badgeColor} text-light">${topicName}</span>
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-calendar3"></i> ${date}
                                    </small>
                                </div>

                                <h5 class="card-title fw-bold">
                                    <a href="${url}" class="text-dark text-decoration-none">${title}</a>
                                </h5>

                                <p class="card-text text-muted small flex-grow-1 text-truncate">
                                    ${content}
                                </p>

                                <div class="mt-3 text-muted small border-top pt-3 d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="bi bi-person-circle"></i> ${author}
                                    </span>

                                    <a href="${url}" class="text-primary text-decoration-none">
                                        Baca <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            $.ajax({
                url: '/api/articles/latest',
                method: 'GET',
                success: function (response) {
                    let container = $('#newest-articles-container');
                    container.empty();

                    if (response.success && response.data.length > 0) {
                        response.data.slice(0, 3).forEach(article => {
                            container.append(renderArticleCard(article, 'bg-primary'));
                        });
                    } else {
                        container.html('<div class="col-12"><p class="text-muted">Belum ada artikel.</p></div>');
                    }
                }
            });

            $.ajax({
                url: '/api/articles/popular-topics',
                method: 'GET',
                success: function (response) {
                    if (!response.success || response.data.length === 0) return;

                    let popularContainer = $('#dynamic-popular-topics');

                    response.data.forEach((topic, index) => {
                        let colorClass = badgeColors[index % badgeColors.length];
                        let borderColor = colorClass.split(' ')[0].replace('bg-', 'border-');

                        popularContainer.append(`
                                                <hr class="my-5">

                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h3 class="fw-bold text-dark border-bottom ${borderColor} border-3 pb-2">
                                                        ${topic.name}
                                                    </h3>
                                                </div>

                                                <div class="row" id="topic-articles-${topic.id}">
                                                    <div class="col-12 text-center py-4">
                                                        <div class="spinner-border text-secondary" role="status"></div>
                                                    </div>
                                                </div>
                                            `);

                        fetchArticlesForTopic(topic.id, colorClass);
                    });
                }
            });

            function fetchArticlesForTopic(topicId, colorClass) {
                $.ajax({
                    url: `/api/articles/latest?topic=${topicId}`,
                    method: 'GET',
                    success: function (response) {
                        let container = $(`#topic-articles-${topicId}`);
                        container.empty();

                        if (response.success && response.data.length > 0) {
                            response.data.slice(0, 3).forEach(article => {
                                container.append(renderArticleCard(article, colorClass));
                            });
                        } else {
                            container.html('<div class="col-12"><p class="text-muted">Belum ada artikel di kategori ini.</p></div>');
                        }
                    }
                });
            }

            let typingTimer;
            const doneTypingInterval = 800;
            $('#search-article').on('input', function () {
                let searchQuery = $(this).val().trim();
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () {
                    if (searchQuery === '') {
                        resetToDefaultView();
                    } else {
                        performSearch(searchQuery);
                    }
                }, doneTypingInterval);
            });

            function performSearch(query) {
                $('#dynamic-popular-topics').hide();
                let container = $('#newest-articles-container');
                container.html(`
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                `);

                $.ajax({
                    url: `/api/articles/public?search=${encodeURIComponent(query)}`,
                    method: 'GET',
                    success: function (response) {
                        container.empty();
                        let results = response.data?.data || [];
                        if (response.success && results.length > 0) {
                            results.forEach(article => {
                                container.append(renderArticleCard(article, 'bg-primary'));
                            });
                        } else {
                            container.html('<div class="col-12"><p class="text-muted">Artikel tidak ditemukan.</p></div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="col-12"><div class="alert alert-danger">Terjadi kesalahan saat mencari artikel.</div></div>');
                    }
                });
            }

            function resetToDefaultView() {
                $('#dynamic-popular-topics').show();
                let container = $('#newest-articles-container');
                container.html(`
                    <div class="col-12 text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                `);
                $.ajax({
                    url: '/api/articles/latest',
                    method: 'GET',
                    success: function (response) {
                        container.empty();
                        if (response.success && response.data.length > 0) {
                            response.data.slice(0, 3).forEach(article => {
                                container.append(renderArticleCard(article, 'bg-primary'));
                            });
                        } else {
                            container.html('<div class="col-12"><p class="text-muted">Belum ada artikel.</p></div>');
                        }
                    }
                });
            }
        });
    </script>
@endsection