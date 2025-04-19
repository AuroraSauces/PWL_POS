@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Transaksi Penjualan</h3>
        <div class="card-tools">
            <a href="{{ route('penjualan.create') }}" class="btn btn-success btn-sm">Tambah Transaksi</a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Transaksi</th>
                    <th>Nama Pembeli</th>
                    <th>Nama User (Yang Menambahkan)</th> <!-- Menambahkan kolom Nama User -->
                    <th>Barang yang Dijual</th>
                    <th>Jumlah</th>
                    <th>Total Harga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan as $item)
                    <tr>
                        <td>{{ $item->penjualan_id }}</td>
                        <td>{{ $item->pembeli }}</td>
                        <td>{{ $item->user->nama ?? 'Tidak Diketahui' }}</td> <!-- Menampilkan Nama User -->
                        <td>
                            @foreach($item->detailPenjualan as $detail)
                                {{ $detail->barang->barang_nama }}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($item->detailPenjualan as $detail)
                                {{ $detail->jumlah }}<br>
                            @endforeach
                        </td>
                        <td>
                            Rp. {{ number_format($item->totalTransaksi(), 0, ',', '.') }}
                        </td>
                        <td>{{ $item->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
