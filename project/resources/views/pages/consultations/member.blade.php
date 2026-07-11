@extends('layouts.navbar.main')

@section('content')
<div class="container pt-5">

    <div class="mb-4">
        <h4 class="mb-1"><i class="bi bi-clipboard2-heart-fill text-primary"></i> Konsultasi Saya</h4>
        <p class="text-muted">Daftar konsultasi aktif dan riwayat konsultasi Anda.</p>
    </div>

    <div id="consultation-container">
        <div id="loading-indicator" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Memuat data...</p>
        </div>
        <div id="cards-wrapper" style="display:none;"></div>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    function loadConsultations() {
        $.ajax({
            url: '/api/consultations/fetch',
            method: 'GET',
            success: function (response) {
                $('#loading-indicator').hide();

                if (response.success && response.data.length > 0) {
                    let html = '';

                    response.data.forEach(function (c) {
                        let isActive = c.is_active;

                        let statusBadge = isActive
                            ? `<span class="px-3 py-1 rounded-pill"
                                style="background:#d1fae5; color:#065f46; font-size:0.78rem; font-weight:600; white-space:nowrap;">
                                <i class="bi bi-circle-fill" style="font-size:0.4rem; vertical-align:middle;"></i> Aktif
                               </span>`
                            : `<span class="px-3 py-1 rounded-pill"
                                style="background:#f3f4f6; color:#6b7280; font-size:0.78rem; font-weight:600; white-space:nowrap;">
                                <i class="bi bi-check-circle-fill" style="vertical-align:middle;"></i> Selesai
                               </span>`;

                        let btnLabel = isActive ? 'Lanjut Chat' : 'Lihat Riwayat';
                        let btnStyle = isActive
                            ? 'background:#3b82f6; color:#fff; border:none;'
                            : 'background:#fff; color:#6b7280; border:1px solid #d1d5db;';

                        let timeInfo = `Mulai: <strong>${c.start_time ?? '-'}</strong>`;
                        if (c.end_time) {
                            timeInfo += ` &mdash; Selesai: <strong>${c.end_time}</strong>`;
                        }

                        html += `
                            <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                                <div class="card-body p-0">
                                    <div class="d-flex">

                                        {{-- Aksen warna kiri --}}
                                        <div style="width:5px; background:${isActive ? '#3b82f6' : '#d1d5db'}; flex-shrink:0;"></div>

                                        <div class="d-flex justify-content-between align-items-center w-100 p-3">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="bi bi-person-badge-fill text-primary"></i>
                                                    dr. <strong>${c.doctor}</strong>
                                                </h6>
                                                <small class="text-muted d-block">${timeInfo}</small>
                                                ${c.notes
                                                    ? `<small class="text-muted d-block mt-1">
                                                        <i class="bi bi-journal-text"></i> ${c.notes}
                                                       </small>`
                                                    : ''}
                                            </div>
                                            <div class="d-flex align-items-center ml-3" style="flex-shrink:0;">
                                                <span class="mr-3">${statusBadge}</span>
                                                <a href="${c.chat_url}" class="btn btn-sm"
                                                    style="${btnStyle} border-radius:8px; padding:5px 14px;">
                                                    <i class="bi bi-chat-dots-fill"></i> ${btnLabel}
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>`;
                    });

                    $('#cards-wrapper').html(html).show();
                } else {
                    $('#consultation-container').html(`
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                            <p class="mt-3">Belum ada data konsultasi.</p>
                        </div>`);
                }
            },
            error: function () {
                $('#loading-indicator').hide();
                $('#consultation-container').html('<div class="alert alert-danger text-center">Gagal memuat data konsultasi.</div>');
            }
        });
    }

    loadConsultations();
});
</script>
@endsection
