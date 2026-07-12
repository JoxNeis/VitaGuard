@extends('layouts.navbar.admin')

@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-file-earmark-text text-primary"></i> Article Detail</h4>
                <p class="text-muted mb-0">View complete article information.</p>
            </div>
            <a href="/admin/articles" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>
        <div class="card shadow border-0">
            <div class="card-body">
                <h2 id="title" class="font-weight-bold mb-2">Loading...</h2>
                <div class="mb-3">
                    <span class="text-muted">
                        <i class="bi bi-person"></i>
                        <span id="creator"></span>
                    </span>
                    <span class="mx-2">•</span>
                    <span class="badge badge-primary" id="topic"></span>
                </div>
                <hr>
                <div class="text-center mb-4">
                    <img id="image-preview" class="img-fluid rounded shadow-sm" style="max-height:450px;display:none;">
                    <div id="image-fallback" class="text-muted">
                        No Image Available
                    </div>
                </div>
                <h5 class="text-primary mb-3">
                    <i class="bi bi-card-text"></i> Article Content
                </h5>
                <div id="content" class="border rounded p-4 bg-light" style="white-space:pre-wrap;line-height:1.8">
                </div>
                <hr>
                <h6 class="font-weight-bold">Image Path</h6>
                <code id="image-path">-</code>
            </div>

            <div class="card-footer bg-white text-end">
                <a id="btn-edit" class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit Article
                </a>
                <button class="btn btn-secondary" onclick="window.history.back()">
                    Close
                </button>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let pathSegments = window.location.pathname.split('/');
            let articleId = pathSegments[pathSegments.length - 2];
            $('#btn-edit').attr('href', `/admin/articles/${articleId}/edit`);
            $.ajax({
                url: `/api/admin/articles/${articleId}/detail`,
                method: 'GET',
                success: function (response) {
                    if (!response.success) return;
                    let article = response.article;
                    $('#title').text(article.title);
                    $('#creator').text(article.creator.username);
                    $('#topic').text(
                        article.topic
                            ? article.topic.name
                            : '-'
                    );
                    $('#content').text(article.content);
                    $('#image-path').text(article.image ?? '-');
                    if (article.image) {
                        let imageUrl = article.image;
                        if (!imageUrl.startsWith('http')) {
                            imageUrl = '/storage/' + imageUrl;
                        }
                        $('#image-preview')
                            .attr('src', imageUrl)
                            .show();
                        $('#image-fallback').hide();
                    }
                },
                error: function () {
                    alert('Failed to load article.');
                }
            });
        });
    </script>
@endsection