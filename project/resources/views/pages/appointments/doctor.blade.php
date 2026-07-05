@extends('layouts.navbar.admin')

@section('content')
<div class="container mb-5">
    
    <div class="mb-3">
        
    </div>

    <div class="mb-4">
        <h4 class="mb-1"><i class="bi bi-calendar-check-fill text-primary"></i> Appointment Pasien</h4>
        <p class="text-muted">Daftar jadwal appointment dari pasien Anda.</p>
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
            url: '/api/doctor/appointments/fetch',
            method: 'GET',
            success: function (response) {
                $('#loading-indicator').hide();

                if (response.success && response.data.length > 0) {
                    let html = '';

                    response.data.forEach(function (item) {
                        let dateTime = `<strong>${item.date}</strong> jam <strong>${item.time}</strong>`;
                        let queueInfo = `Antrean ke-<strong>${item.queue_order}</strong>`;

                        // Dropdown status
                        let statusOptions = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
                        let selectHtml = `<select class="form-select form-select-sm status-select" data-id="${item.id}" style="width:150px; border-radius:6px;">`;
                        statusOptions.forEach(function(opt) {
                            let selected = opt === item.status ? 'selected' : '';
                            selectHtml += `<option value="${opt}" ${selected}>${opt.toUpperCase()}</option>`;
                        });
                        selectHtml += '</select>';

                        html += `
                            <div class="card mb-3 border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
                                <div class="card-body p-0">
                                    <div class="d-flex">
                                        <div style="width:5px; background:${item.status === 'pending' ? '#f59e0b' : (item.status === 'confirmed' ? '#10b981' : '#6b7280')}; flex-shrink:0;"></div>
                                        <div class="d-flex justify-content-between align-items-center w-100 p-3">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="bi bi-person-fill text-primary"></i>
                                                    <strong>${item.patient_name}</strong>
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
                                                ${selectHtml}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });

                    $('#cards-wrapper').html(html).show();

                    // Attach event listener
                    $('.status-select').on('change', function() {
                        let appointmentId = $(this).data('id');
                        let newStatus = $(this).val();
                        updateStatus(appointmentId, newStatus, $(this));
                    });
                } else {
                    $('#appointment-container').html(`
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                            <p class="mt-3">Belum ada appointment dari pasien.</p>
                        </div>`);
                }
            },
            error: function () {
                $('#loading-indicator').hide();
                $('#appointment-container').html('<div class="alert alert-danger text-center">Gagal memuat data appointment.</div>');
            }
        });
    }

    function updateStatus(appointmentId, status, selectElement) {
        $.ajax({
            url: '/api/appointments/' + appointmentId + '/status',
            method: 'PUT',
            data: { 
                status: status,
                _token: $('meta[name="csrf-token"]').attr('content') 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                        alert('Status berhasil diubah!');
                        loadAppointments();
                    } else {
                    alert('Gagal update status: ' + response.message);
                    loadAppointments(); // reload untuk reset
                }
            },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                alert('Error: ' + msg);
                loadAppointments();
            }
        });
    }

    loadAppointments();
});
</script>
@endsection