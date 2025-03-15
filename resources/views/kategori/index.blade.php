@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a href="{{ url('kategori/create') }}" class="btn btn-sm btn-primary mt-1">Tambah Kategori</a>
            <button onclick="modalAction('{{ url('kategori/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_kategori">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>

        <!-- Modal container untuk form Ajax -->
        <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
    </div>
</div>
@endsection

@push('js')
<script>
function modalAction(url = '') {
    $('#myModal').load(url, function() {
        $('#myModal').modal('show');
    });
}

var dataKategori;
$(document).ready(function() {
    dataKategori = $('#table_kategori').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('kategori.list') }}",
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: "kategori_id", className: "text-center", orderable: true, searchable: true },
            { data: "kategori_kode", orderable: true, searchable: true },
            { data: "kategori_nama", orderable: true, searchable: true },
            { data: "aksi", orderable: false, searchable: false, className: "text-center" }
        ]
    });

    // Konfirmasi hapus data kategori
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let url = $(this).attr('data-url');

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: "Apakah Anda yakin ingin menghapus data ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Deleted!', response.message, 'success');
                            dataKategori.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
