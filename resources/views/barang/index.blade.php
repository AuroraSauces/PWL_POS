@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('barang/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="col-1 control-label col-form-label">Filter:</label>
                    <div class="col-3">
                        <select class="form-control" id="kategori_id" name="kategori_id" required>
                            <option value="">- Semua -</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->kategori_id }}">{{ $item->kategori_nama }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Kategori barang</small>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover table-sm" id="table_barang">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
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

var dataBarang;
$(document).ready(function() {
    dataBarang = $('#table_barang').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('barang.list') }}",
            type: "POST",
            "data": function(d) {
                d.kategori_id = $('#kategori_id').val();
            }
        },
        columns: [
            { data: "barang_id", className: "text-center", orderable: true, searchable: true },
            { data: "barang_kode", orderable: true, searchable: true },
            { data: "barang_nama", orderable: true, searchable: true },
            { data: "kategori_nama", orderable: true, searchable: true },
            {
                data: "harga_beli",
                className: "text-right",
                orderable: true,
                searchable: true,
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            {
                data: "harga_jual",
                className: "text-right",
                orderable: true,
                searchable: true,
                render: function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { data: "aksi", orderable: false, searchable: false, className: "text-center" }
        ]
    });
    $('#kategori_id').change(function() {
        dataBarang.ajax.reload(); // Reload data tanpa refresh halaman
    });
});
</script>
@endpush
