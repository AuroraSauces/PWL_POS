@extends('layouts.template')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Halo, {{ auth()->user()->nama }} Selamat Datang!</h3>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalStok }}</h3>
                    <p>Total Stok Ready</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalTerjual }}</h3>
                    <p>Total Barang Terjual</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($totalNominalPenjualan, 0, ',', '.') }}</h3>
                    <p>Total Penjualan (Rp)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-cash-register"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalTransaksi }}</h3>
                    <p>Total Transaksi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Barang yang Ready</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered m-0">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Stok Ready</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($barangReady as $barang)
                        <tr>
                            <td>{{ $barang->barang_nama }}</td>
                            <td>{{ $barang->stok_ready }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">Tidak ada barang yang ready</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


@endsection
