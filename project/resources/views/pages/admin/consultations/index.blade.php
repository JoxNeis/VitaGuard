@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-calendar2-check text-primary"></i> Consultations</h4>
                <p class="text-muted mb-0">Manajemen jadwal dan riwayat konsultasi pasien.</p>
            </div>

            @if(auth()->user()->role === \App\Data\Value\Account\Role::ADMIN->value)
                <a href="/portal/consultations/create" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-circle"></i>
                    Add new Consultation
                </a>
            @endif
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div id="consultation-container">

                    <div id="loading-indicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-3 text-muted">Loading consultations data...</p>
                    </div>

                    <div class="table-responsive" id="table-wrapper" style="display: none;">
                        <table class="table table-hover table-striped mb-0" id="consultations-table">
                            <thead class="bg-light">
                            <tr>
                                <th class="text-center" width="6%">#</th>
                                <th width="30%">
                                    <i class="bi bi-person"></i> Pasien
                                </th>
                                <th width="24%">
                                    <i class="bi bi-person-badge"></i> Dokter
                                </th>
                                <th width="22%">
                                    <i class="bi bi-calendar-event"></i> Jadwal
                                </th>
                                <th class="text-center" width="10%">
                                    <i class="bi bi-credit-card"></i> Bayar
                                </th>
                                <th class="text-center" width="8%">
                                    <i class="bi bi-gear"></i>
                                </th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalDeleteConsultation" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus jadwal konsultasi ID: <strong id="delete-id-text"
                            class="text-danger"></strong>?</p>
                    <small class="text-muted">Peringatan: Data yang dihapus tidak dapat dikembalikan (akan masuk ke riwayat
                        Soft Delete).</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Ya, Hapus Data</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@section('scripts')
    <script>
        $(document).ready(function () {

            let consultationToDelete = null;

            // admin -> fetch all, doctor -> fetch biasa
            let fetchUrl = '{{ auth()->user()->role === \App\Data\Value\Account\Role::ADMIN->value ? "/api/admin/consultations/fetch-all" : "/api/consultations/fetch" }}';

            function loadConsultations() {
                let container = $('#consultation-container');
                let tbody = $('#consultations-table tbody');
                let loadingIndicator = $('#loading-indicator');
                let tableWrapper = $('#table-wrapper');

                $.ajax({                    
                    url: fetchUrl,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let consultations = response.data;
                            let rowsHtml = '';

                            consultations.forEach(consultation => {                                
                                let patientName = consultation.patient;

                                if (consultation.patient_data) {
                                    let m = consultation.patient_data;

                                    patientName = [
                                        m.first_name,
                                        m.middle_name,
                                        m.last_name
                                    ].filter(Boolean).join(' ');
                                }
                                
                                let doctorName = '-';

                                if (
                                    consultation.online_session &&
                                    consultation.online_session.doctor_data
                                ) {
                                    let d = consultation.online_session.doctor_data;

                                    doctorName = [
                                        d.prefix_name,
                                        d.first_name,
                                        d.last_name,
                                        d.suffix_name
                                    ].filter(Boolean).join(' ');
                                }

                                let startDate = new Date(consultation.start_time).toLocaleString('id-ID', {
                                    year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                                });
                               
                                let paymentStatus = consultation.paid_at
                                    ? '<span class="badge bg-success px-3 py-2">Lunas</span>'
                                    : '<span class="badge bg-warning text-dark px-3 py-2">Belum Bayar</span>'
                                                               
                                let actionButtons = `
                                    <a href="/portal/consultations/${consultation.id}/show"
                                    class="btn btn-sm btn-primary">

                                    <i class="bi bi-eye"></i>
                                    Detail

                                    </a>
                                `;
                               
                                @if(auth()->user()->role === \App\Data\Value\Account\Role::ADMIN->value)
                                actionButtons += `
                                    <button type="button" class="btn btn-sm btn-danger text-white btn-delete" data-id="${consultation.id}" title="Hapus">
                                        Delete
                                    </button>
                                `;
                                @endif

                                rowsHtml += `
                                <tr id="tr_${consultation.id}" class="align-middle">

                                    <td class="text-center fw-bold">
                                        #${consultation.id}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">

                                            <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center me-3"
                                                style="width:42px;height:42px;font-weight:bold;">

                                                ${patientName.charAt(0).toUpperCase()}

                                            </div>

                                            <div>

                                                <div class="fw-bold">
                                                    ${patientName}
                                                </div>

                                                <small class="text-muted">
                                                    @${consultation.patient}
                                                </small>

                                            </div>

                                        </div>
                                    </td>

                                    <td>

                                        ${
                                            doctorName == '-'
                                            ?
                                            '<span class="text-muted"><i class="bi bi-dash-circle"></i> Belum ditentukan</span>'
                                            :
                                            `<span class="fw-semibold">${doctorName}</span>`
                                        }

                                    </td>

                                    <td>

                                        <div class="fw-semibold">

                                            ${new Date(consultation.start_time).toLocaleDateString('id-ID',{
                                                day:'numeric',
                                                month:'short',
                                                year:'numeric'
                                            })}

                                        </div>

                                        <small class="text-muted">

                                            ${new Date(consultation.start_time).toLocaleTimeString('id-ID',{
                                                hour:'2-digit',
                                                minute:'2-digit'
                                            })}

                                        </small>

                                    </td>

                                    <td class="text-center">

                                        ${paymentStatus}

                                    </td>

                                    <td class="text-center">

                                        ${actionButtons}

                                    </td>

                                </tr>
                                `;
                            });
                        
                            tbody.html(rowsHtml);
                            
                            loadingIndicator.hide();
                            tableWrapper.fadeIn(300);

                        } else {
                            loadingIndicator.hide();
                            container.html('<div class="alert alert-warning m-4 text-center">Belum ada data konsultasi.</div>');
                        }
                    },
                    error: function () {
                        loadingIndicator.hide();
                        container.html('<div class="alert alert-danger m-4 text-center">Terjadi kesalahan saat memuat data dari server.</div>');
                    }
                });
            }
            
            loadConsultations();

            $(document).on('click', '.btn-delete', function () {
                consultationToDelete = $(this).data('id');
                $('#delete-id-text').text(consultationToDelete);
                $('#modalDeleteConsultation').modal('show');
            });

            $('#btn-confirm-delete').on('click', function () {
                let btn = $(this);
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menghapus...').prop('disabled', true);

                $.ajax({                                       
                    url: `/api/admin/consultations/${consultationToDelete}/destroy`,
                    method: 'POST',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        btn.html(originalText).prop('disabled', false);
                        $('#modalDeleteConsultation').modal('hide');

                        if (response.success) {
                            $('#tr_' + consultationToDelete).fadeOut(300, function () {
                                $(this).remove();
                                
                                if ($('#consultations-table tbody tr:visible').length === 0) {
                                    $('#table-wrapper').hide();
                                    $('#consultation-container').append('<div class="alert alert-warning m-4 text-center">Belum ada data konsultasi.</div>');
                                }
                            });
                        } else {
                            alert('Gagal: ' + (response.message || 'Terjadi kesalahan'));
                        }
                    },
                    error: function () {
                        btn.html(originalText).prop('disabled', false);
                        alert('Terjadi kesalahan pada server saat menghapus data.');
                    }
                });
            });

        });
    </script>

<style>

#consultations-table tbody tr{
    transition:.2s;
}

#consultations-table tbody tr:hover{
    transform:scale(1.005);
    box-shadow:0 3px 12px rgba(0,0,0,.06);
}

#consultations-table td{
    vertical-align:middle;
}

#consultations-table thead th{
    font-weight:600;
    color:#495057;
}

.card{
    border-radius:14px;
}

</style>
@endsection