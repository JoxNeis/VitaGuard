@php
    $layout = auth()->user()->role === 'doctor' ? 'layouts.navbar.admin' : 'layouts.navbar.main';
    $isDoc  = auth()->user()->role === 'doctor';
@endphp

@extends($layout)

@section('content')
<div class="container pt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-chat-dots-fill text-primary"></i> Konsultasi Online
            </h4>
            <p class="text-muted mb-0" style="font-size:0.9rem;" id="chat-header-info">
                <span class="spinner-border spinner-border-sm text-secondary"></span> Memuat...
            </p>
        </div>
        <div class="d-flex align-items-center">
            <span id="status-badge" class="badge mr-3 px-3 py-2" style="display:none;"></span>
            <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm mr-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            @if($isDoc)
                <button class="btn btn-danger btn-sm" id="btn-close-consultation" style="display:none;">
                    <i class="bi bi-x-circle"></i> Tutup Konsultasi
                </button>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
        <div id="chat-messages"
    style=" height:65vh; min-height:420px; max-height:700px; overflow-y:auto; padding:24px; background:#f8fafc; ">
            <div id="chat-loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Memuat pesan...</p>
            </div>
        </div>

        <div style="border-top:1px solid #e5e7eb;"></div>

        <div id="chat-input-area" class="p-3" style="display:none;">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control"
                    placeholder="Ketik pesan Anda..."
                    autocomplete="off"
                    style="border-radius:12px 0 0 12px; border-right:none;">
                <div class="input-group-append">
                    <button class="btn btn-primary px-4" id="btn-send-message"
                        style="border-radius:0 12px 12px 0;">
                        <i class="bi bi-send-fill"></i> Kirim
                    </button>
                </div>
            </div>
            <small class="text-muted mt-1 d-block">
                Login sebagai: <strong>{{ auth()->user()->username }}</strong>
            </small>
        </div>

        <div id="chat-closed-notice" class="py-3 text-center text-muted"
            style="display:none; background:#f9fafb; font-size:0.9rem;">
            <i class="bi bi-lock-fill"></i> Konsultasi telah ditutup &mdash; tidak dapat mengirim pesan baru.
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modalCloseConsultation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; border:none;">
            <div class="modal-header bg-danger text-white" style="border-radius:12px 12px 0 0;">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle-fill"></i> Tutup Konsultasi</h5>
            </div>
            <div class="modal-body">
                <p class="mb-1">Apakah Anda yakin ingin menutup konsultasi ini?</p>
                <small class="text-muted">Setelah ditutup, tidak ada pesan baru yang dapat dikirim.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-close">Ya, Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush

@section('scripts')
<script>
$(document).ready(function () {
    const consultationId = {{ $consultationId }};
    const currentUser    = '{{ auth()->user()->username }}';
    const isDoc          = {{ $isDoc ? 'true' : 'false' }};
    let isActive         = false;
    let lastMessageCount = 0;
    let pollingInterval  = null;

    function init() {
        $.ajax({
            url: `/api/chat/${consultationId}`,
            method: 'GET',
            success: function (response) {
                if (!response.success) return;

                isActive = response.is_active;

                $('#chat-header-info').html(
                    `<i class="bi bi-person-fill"></i> <strong>${response.patient}</strong>` +
                    ` &nbsp;&mdash;&nbsp; ` +
                    `<i class="bi bi-person-badge-fill"></i> <strong>${response.doctor}</strong>`
                );

                if (isActive) {
                    $('#status-badge')
                        .css({ background: '#d1fae5', color: '#065f46' })
                        .html('<i class="bi bi-circle-fill" style="font-size:0.5rem;"></i> Aktif')
                        .show();
                } else {
                    let endTime = response.end_time ? new Date(response.end_time).toLocaleDateString('id-ID', {
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    }) : '';
                    $('#status-badge')
                        .css({ background: '#f3f4f6', color: '#6b7280' })
                        .html(`<i class="bi bi-check-circle-fill"></i> Selesai &bull; ${endTime}`)
                        .show();
                }

                if (isDoc && isActive) {
                    $('#btn-close-consultation').show();
                }

                renderMessages(response.data);
                updateInputState();

                if (isActive) {
                    pollingInterval = setInterval(loadMessages, 3000);
                }
            },
            error: function () {
                $('#chat-header-info').html('<span class="text-danger">Gagal memuat data konsultasi.</span>');
            }
        });
    }

    function loadMessages() {
        $.ajax({
            url: `/api/chat/${consultationId}`,
            method: 'GET',
            success: function (response) {
                if (!response.success) return;
                isActive = response.is_active;
                if (response.data.length === lastMessageCount) return;
                renderMessages(response.data);
                updateInputState();
            }
        });
    }

    function renderMessages(chats) {
        lastMessageCount = chats.length;
        let html = '';

        if (chats.length === 0) {
            html = `<div class="text-center text-muted mt-5">
                        <i class="bi bi-chat-square-dots" style="font-size:2rem;"></i>
                        <p class="mt-2">Belum ada pesan. Mulai konsultasi Anda.</p>
                    </div>`;
        } else {
            chats.forEach(function (chat) {
                const isSelf = chat.sender === currentUser;
                const time   = new Date(chat.created_at).toLocaleTimeString('id-ID', {
                    hour: '2-digit', minute: '2-digit'
                });

                if (isSelf) {
                    html += `
                        <div class="d-flex justify-content-end mb-3">
                            <div style="max-width:65%;">
                                <div class="text-white p-2 px-3"
                                    style="background:#3b82f6; border-radius:18px 18px 4px 18px; word-break:break-word;">
                                    ${escapeHtml(chat.message)}
                                </div>
                                <div class="text-right mt-1">
                                    <small class="text-muted">${time}</small>
                                </div>
                            </div>
                        </div>`;
                } else {
                    html += `
                        <div class="d-flex justify-content-start mb-3">
                            <div style="max-width:65%;">
                                <small class="text-muted d-block mb-1 font-weight-bold">
                                    <i class="bi bi-person-circle"></i> ${escapeHtml(chat.sender_name)}
                                </small>
                                <div class="p-2 px-3"
                                    style="background:#ffffff; border:1px solid #e5e7eb; border-radius:18px 18px 18px 4px; word-break:break-word;">
                                    ${escapeHtml(chat.message)}
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">${time}</small>
                                </div>
                            </div>
                        </div>`;
                }
            });
        }

        $('#chat-loading').hide();
        $('#chat-messages').html(html);
        scrollToBottom();
    }

    function sendMessage() {
        const message = $('#chat-input').val().trim();
        if (!message) return;

        let btn = $('#btn-send-message');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: '/api/chat/send',
            method: 'POST',
            data: {
                '_token':          '{{ csrf_token() }}',
                'consultation_id': consultationId,
                'sender':          currentUser,
                'message':         message,
            },
            success: function (response) {
                btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Kirim');
                if (response.success) {
                    $('#chat-input').val('');
                    lastMessageCount = 0;
                    loadMessages();
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="bi bi-send-fill"></i> Kirim');
                alert('Terjadi kesalahan saat mengirim pesan.');
            }
        });
    }

    $('#btn-close-consultation').on('click', function () {
        $('#modalCloseConsultation').modal('show');
    });

    $('#btn-confirm-close').on('click', function () {
        let btn = $(this);
        btn.html('<span class="spinner-border spinner-border-sm"></span> Menutup...').prop('disabled', true);

        $.ajax({
            url: `/api/chat/${consultationId}/close`,
            method: 'POST',
            data: { '_token': '{{ csrf_token() }}' },
            success: function (response) {
                btn.html('Ya, Tutup').prop('disabled', false);
                $('#modalCloseConsultation').modal('hide');
                if (response.success) {
                    isActive = false;
                    updateInputState();
                    $('#btn-close-consultation').hide();
                    $('#status-badge')
                        .css({ background: '#f3f4f6', color: '#6b7280' })
                        .html('<i class="bi bi-check-circle-fill"></i> Selesai');
                } else {
                    alert('Gagal: ' + response.message);
                }
            },
            error: function () {
                btn.html('Ya, Tutup').prop('disabled', false);
                alert('Terjadi kesalahan pada server.');
            }
        });
    });

    function updateInputState() {
        if (isActive) {
            $('#chat-input-area').show();
            $('#chat-closed-notice').hide();
        } else {
            $('#chat-input-area').hide();
            $('#chat-closed-notice').show();
            clearInterval(pollingInterval);
        }
    }

    function scrollToBottom() {
        const box = document.getElementById('chat-messages');
        box.scrollTop = box.scrollHeight;
    }

    function escapeHtml(text) {
        return $('<div>').text(text).html();
    }

    $('#chat-input').on('keypress', function (e) {
        if (e.which === 13) sendMessage();
    });

    $('#btn-send-message').on('click', sendMessage);

    init();
});
</script>
@endsection
