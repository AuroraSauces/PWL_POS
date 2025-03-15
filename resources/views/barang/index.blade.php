@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('barang/create') }}">Tambah</a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

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
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#table_barang').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('barang.list') }}",
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: "barang_id", className: "text-center", orderable: true, searchable: true },
                { data: "barang_kode", orderable: true, searchable: true },
                { data: "barang_nama", orderable: true, searchable: true },
                { data: "kategori_nama", orderable: true, searchable: true },
                { data: "harga_beli", className: "text-right", orderable: true, searchable: true,
                  render: function(data) {
                      return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                  }
                },
                { data: "harga_jual", className: "text-right", orderable: true, searchable: true,
                  render: function(data) {
                      return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                  }
                },
                { data: "aksi", orderable: false, searchable: false }
            ]
        });
    });
</script>
@endpush
