@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Stok Barang</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('stok/create_ajax') }}')" class="btn btn-success btn-sm">
                Tambah Stok
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Barang</th>
                        <th>Jumlah Stok</th>
                        <th>Tanggal Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Data diisi DataTables --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="main-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="modal-body-1">
                {{-- AJAX content here --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
var table;
$(document).ready(function () {
    table = $('#table-stok').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('stok/list') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'barang_nama', name: 'barang.barang_nama' },
            { data: 'stok_jumlah', name: 'stok_jumlah' },
            { data: 'stok_tanggal', name: 'stok_tanggal' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
        ]
    });
});

// Buka modal dan load konten dari URL
function modalAction(url) {
    $('#main-modal #modal-body-1').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#main-modal').modal('show');
    $.get(url, function(res) {
        $('#main-modal #modal-body-1').html(res);
    }).fail(function() {
        $('#main-modal #modal-body-1').html('<div class="alert alert-danger">Gagal memuat data.</div>');
    });
}

// Delete data stok
function deleteData(url) {
    Swal.fire({
        title: 'Yakin hapus data?',
        text: "Data akan hilang permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function(response) {
                    Swal.fire(response.status ? 'Berhasil' : 'Gagal', response.message, response.status ? 'success' : 'error');
                    if (response.status) table.ajax.reload();
                },
                error: function() {
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                }
            });
        }
    });
}

// AJAX Submit Form Tambah Stok
$(document).on('submit', '#form-tambah-stok', function (event) {
    event.preventDefault();

    let form = $(this);
    let formData = new FormData(this);

    // Reset error
    form.find('.form-control').removeClass('is-invalid');
    form.find('.error-text').text('');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            if (response.status) {
                Swal.fire('Berhasil!', response.message, 'success');
                $('#main-modal').modal('hide');
                table.ajax.reload();
            } else {
                Swal.fire('Gagal!', response.message, 'error');
                if (response.msgField) {
                    $.each(response.msgField, function (key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#error-' + key).text(value);
                    });
                }
            }
        },
        error: function () {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengirim data.', 'error');
        }
    });
});
</script>
@endpush
