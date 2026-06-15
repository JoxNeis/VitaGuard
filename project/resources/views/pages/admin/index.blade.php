@extends('layouts.navbar.admin')
@section('content')
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h4>Dashboard Admin VitaGuard</h4>
                <p>Pilih data yang ingin Anda kelola:</p>

                <select id="table-selector" class="form-control w-25">
                    <option value="" selected disabled>-- Memuat Pilihan Tabel... --</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div id="table-container">
                    <div class="alert alert-info">Silakan pilih tabel di atas untuk memuat data.</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
                        
            $.ajax({
                url: '/api/admin/available-tables',
                method: 'GET',
                success: function (response) {
                    if (response.success && response.data.length > 0) {
                        let selector = $('#table-selector');
                        
                        // Bersihkan tulisan "Memuat..." dan ganti opsi default
                        selector.empty();
                        selector.append('<option value="" selected disabled>-- Pilih Tabel --</option>');
                        
                        // Suntikkan nama-nama tabel dari database ke dalam dropdown
                        response.data.forEach(function(table) {
                            selector.append(`<option value="${table.id}">${table.name}</option>`);
                        });
                    }
                },
                error: function () {
                    $('#table-selector').html('<option value="" disabled>Gagal memuat opsi tabel</option>');
                }
            });
            
            $('#table-selector').on('change', function () {
                let selectedTable = $(this).val();
                let container = $('#table-container');

                container.html('<div class="text-center"><div class="spinner-border text-primary"></div><p>Memuat data...</p></div>');

                $.ajax({
                    url: `/api/admin/fetch-table/${selectedTable}`,
                    method: 'GET',
                    success: function (response) {
                        if (response.success && response.data.length > 0) {
                            let rows = response.data;
                            let columns = Object.keys(rows[0]);
                            
                            let tableHtml = `
                                <h5>Menampilkan Data: ${selectedTable.toUpperCase()}</h5>
                                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                    <table class="table table-striped table-bordered">
                                        <thead><tr>
                            `;
                            
                            columns.forEach(col => {
                                tableHtml += `<th>${col.toUpperCase()}</th>`;
                            });
                            tableHtml += `</tr></thead><tbody>`;

                            rows.forEach(row => {
                                tableHtml += `<tr>`;
                                columns.forEach(col => {
                                    tableHtml += `<td>${row[col] !== null ? row[col] : '-'}</td>`;
                                });
                                tableHtml += `</tr>`;
                            });

                            tableHtml += `</tbody></table></div>`;
                            container.html(tableHtml);

                        } else {
                            container.html('<div class="alert alert-warning">Tabel ini masih kosong.</div>');
                        }
                    },
                    error: function () {
                        container.html('<div class="alert alert-danger">Gagal mengambil data dari server.</div>');
                    }
                });
            });
            
        });
    </script>
@endsection