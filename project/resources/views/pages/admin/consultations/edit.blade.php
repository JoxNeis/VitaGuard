@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4 mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4><i class="bi bi-pencil-square text-primary"></i> Edit Konsultasi</h4>
                <p class="text-muted mb-0">Perbarui jadwal dan catatan konsultasi.</p>
            </div>
            <a href="/portal/consultations" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form id="form-edit-consultation">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label for="patient" class="font-weight-bold">Pasien <span class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="patient" name="patient" required>
                                <option value="" disabled>-- Memuat Data --</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label for="online_session_id" class="font-weight-bold">Sesi Dokter <span class="text-danger">*</span></label>
                            <select class="form-control custom-select" id="online_session_id" name="online_session_id" required>
                                <option value="" disabled>-- Memuat Data --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label for="start_time" class="font-weight-bold">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="end_time" class="font-weight-bold">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time">
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label for="paid_at" class="font-weight-bold">Waktu Pembayaran</label>
                            <input type="datetime-local" class="form-control" id="paid_at" name="paid_at">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 form-group mb-4">
                            <label for="notes" class="font-weight-bold">Catatan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 border-top pt-4">
                        <button type="button" class="btn btn-light mr-2 px-4" onclick="window.history.back()">Batal</button>
                        <button type="submit" class="btn btn-success px-4" id="btn-submit">
                            <i class="bi bi-save2"></i> Simpan Perubahan
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
            let path = window.location.pathname.split('/');
            let targetId = path[path.length - 2]; 

            const formatForInput = (dateString) => {
                if (!dateString) return '';
                let d = new Date(dateString);
                d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
                return d.toISOString().slice(0, 16);
            };

            function loadFormData() {
                $.ajax({
                    url: '/api/admin/consultations/create-data',
                    method: 'GET',
                    success: function (resDropdown) {
                        if (resDropdown.success) {
                            let patientSelect = $('#patient').empty();
                            $.each(resDropdown.members, function (i, m) {
                                let name = [m.first_name, m.middle_name, m.last_name].filter(Boolean).join(' ');
                                patientSelect.append(`<option value="${m.username}">${m.username} - ${name}</option>`);
                            });

                            let sessionSelect = $('#online_session_id').empty();
                            $.each(resDropdown.online_sessions, function (i, s) {
                                let docName = s.doctor.first_name ? `${s.doctor.first_name} ${s.doctor.last_name}` : s.doctor;
                                sessionSelect.append(`<option value="${s.id}">ID: ${s.id} - Dr. ${docName}</option>`);
                            });

                            $.ajax({
                                url: `/api/admin/consultations/${targetId}/edit-data`,
                                method: 'GET',
                                success: function (resEdit) {
                                    if (resEdit.success) {
                                        let c = resEdit.consultation;
                                        $('#patient').val(c.patient);
                                        $('#online_session_id').val(c.online_session_id);
                                        $('#start_time').val(formatForInput(c.start_time));
                                        $('#end_time').val(formatForInput(c.end_time));
                                        $('#paid_at').val(formatForInput(c.paid_at));
                                        $('#notes').val(c.notes);
                                    }
                                }
                            });
                        }
                    }
                });
            }

            loadFormData();

            $('#form-edit-consultation').on('submit', function (e) {
                e.preventDefault();
                let btn = $('#btn-submit');
                let originalText = btn.html();

                btn.html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...').prop('disabled', true);
                $('.form-control').removeClass('is-invalid');

                $.ajax({
                    url: `/api/admin/consultations/${targetId}/update`,
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: new FormData(this),
                    success: function (response) {
                        if (response.success) {
                            alert('Sukses! ' + response.message);
                            $(location).attr('href', '/portal/consultations');
                        }
                    },
                    error: function (xhr) {
                        btn.html(originalText).prop('disabled', false);
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, function (field, messages) {
                                $(`#${field}`).addClass('is-invalid');
                            });
                            alert('Gagal validasi data. Periksa kembali form Anda.');
                        } else {
                            alert('Server Error.');
                        }
                    }
                });
            });
        });
    </script>
@endsection