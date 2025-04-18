@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Stok Barang</h3>
        <div class="card-tools">
            <a href="{{ route('stok.create') }}" class="btn btn-success btn-sm">Tambah Stok</a>
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table table-bordered table-striped">
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
                @foreach($stok as $key => $s)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $s->barang->barang_nama }}</td>
                        <td>{{ $s->stok_jumlah }}</td>
                        <td>{{ $s->stok_tanggal }}</td>
                        <td>
                            <a href="{{ route('stok.edit', $s->stok_id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="{{ route('stok.destroy', $s->stok_id) }}" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
