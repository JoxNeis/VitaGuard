@extends('layouts.navbar.main')

@section('content')
<div class="container mt-4 mb-5 pt-5">
    
    <div class="mb-3">
        <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back   
        </a>
    </div>

    <div class="mb-4">
        <h4 class="mb-1"><i class="bi bi-calendar-check-fill text-primary"></i> Appointment Saya</h4>
        <p class="text-muted">Daftar jadwal appointment Anda dengan dokter.</p>
    </div>

    <div id="appointment-container">
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
    function loadAppointments() {
        $.ajax({
            url: '/api/appointments/fetch',
            method: 'GET',
            success: function (response) {
                $('#loading-indicator').hide();

                if (response.success && response.data.length > 0) {
                    let html = '';

                    response.data.forEach(function (item) {
                        // Tentukan badge status
                        let statusBadge = '';
                        let badgeClass = '';
                        switch (item.status) {
                            case 'pending':
                                badgeClass = 'badge-warning text-dark';
                                break;
                            case 'confirmed':
                                badgeClass = 'badge-success';
                                break;
                            case 'completed':
                                badgeClass = 'badge-secondary';
                                break;
                            case 'cancelled':
                                badgeClass = 'badge-danger';
                                break;
                            default:
                                badgeClass = 'badge-light';
                        }
                        statusBadge = `<span class="badge ${badgeClass} px-3 py-2">${item.status.toUpperCase()}</span>`;

          
                        let dateTime = `<strong>${item.date}</strong> jam <strong>${item.time}</strong>`;
                        let queueInfo = `Antrean ke-<strong>${item.queue_order}</strong>`;

                        html += `
                            <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                                <div class="card-body p-0">
                                    <div class="d-flex">

        
                                        <div style="width:5px; background:${item.status === 'pending' ? '#f59e0b' : (item.status === 'confirmed' ? '#10b981' : '#6b7280')}; flex-shrink:0;"></div>

                                        <div class="d-flex justify-content-between align-items-center w-100 p-3">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="bi bi-person-badge-fill text-primary"></i>
                                                    <strong>${item.doctor_name}</strong>
                                                </h6>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-calendar-event"></i> ${dateTime}
                                                </small>
                                                <small class="text-muted d-block">
                                                    <i class="bi bi-sort-numeric-down"></i> ${queueInfo}
                                                </small>
                                                ${item.notes ? `<small class="text-muted d-block mt-1"><i class="bi bi-journal-text"></i> ${item.notes}</small>` : ''}
                                            </div>
                                            <div class="d-flex align-items-center ml-3" style="flex-shrink:0;">
                                                ${statusBadge}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>`;
                    });

                    $('#cards-wrapper').html(html).show();
                } else {
                    $('#appointment-container').html(`
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                            <p class="mt-3">Belum ada appointment. <a href="{{ route('appointments.index') }}">Booking sekarang</a></p>
                        </div>`);
                }
            },
            error: function () {
                $('#loading-indicator').hide();
                $('#appointment-container').html('<div class="alert alert-danger text-center">Gagal memuat data appointment.</div>');
            }
        });
    }

    loadAppointments();
});
</script>
@endsection