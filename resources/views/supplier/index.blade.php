@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <a class="btn btn-sm btn-primary mt-1" href="{{ url('supplier/create') }}">Tambah</a>
            <button onclick="modalAction('{{ url('supplier/create_ajax') }}')" class="btn btn-sm btn-success mt-1">Tambah Ajax</button>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_supplier">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Kode</th>
                    <th>Nama Supplier</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>

        <!-- Modal container untuk form Ajax -->
        <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
             data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
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

var dataSupplier;
$(document).ready(function() {
    dataSupplier = $('#table_supplier').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('supplier.list') }}",
            type: "POST",
            data: function(d) {
                d._token = "{{ csrf_token() }}";
            }
        },
        columns: [
            { data: "supplier_id", className: "text-center", orderable: true, searchable: true },
            { data: "supplier_kode", orderable: true, searchable: true },
            { data: "supplier_nama", orderable: true, searchable: true },
            { data: "supplier_alamat", orderable: true, searchable: true },
            { data: "supplier_kontak", orderable: true, searchable: true },
            { data: "aksi", orderable: false, searchable: false, className: "text-center" }
        ]
    });
});
</script>
@endpush
