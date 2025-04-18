@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title ?? 'Data Level' }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-info btn-sm">Import Level</button>
            <a href="{{ url('/level/export_excel') }}" class="btn btn-sm btn-primary"><i class="fa fa-fileexcel"></i> Export Level</a>
            <a href="{{ url('/level/export_pdf') }}" class="btn btn-sm btn-warning"><i class="fa fa-filepdf"></i> Export Level PDF</a>
            <button onclick="modalAction('{{ url('level/create_ajax') }}')" class="btn btn-success btn-sm">Tambah (Ajax)</button>
        </div>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Level</th>
                    <th>Kode Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tableLevel;
    $(document).ready(function () {
        tableLevel = $('#table_level').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('level.list') }}",
                dataType: "json",
                type: "POST",
                data: function (d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                {
                    data: "level_id",
                    className: "text-center",
                    width: "10%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "level_nama",
                    width: "35%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "level_kode",
                    width: "30%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    width: "25%",
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Enter to search
        $('#table_level_filter input').unbind().bind().on('keyup', function (e) {
            if (e.keyCode == 13) {
                tableLevel.search(this.value).draw();
            }
        });

    });
</script>
@endpush
