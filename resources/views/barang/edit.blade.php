@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Edit Barang</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('barang/'.$barang->barang_id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Kode Barang</label>
                <input type="text" class="form-control" name="barang_kode" value="{{ $barang->barang_kode }}" required>
                @error('barang_kode')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" class="form-control" name="barang_nama" value="{{ $barang->barang_nama }}" required>
                @error('barang_nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select class="form-control" name="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->kategori_id }}" {{ $kategori->kategori_id == $barang->kategori_id ? 'selected' : '' }}>
                            {{ $kategori->kategori_nama }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Harga Beli</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" class="form-control" name="harga_beli" value="{{ $barang->harga_beli }}" required>
                </div>
                @error('harga_beli')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Harga Jual</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" class="form-control" name="harga_jual" value="{{ $barang->harga_jual }}" required>
                </div>
                @error('harga_jual')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ url('barang') }}" class="btn btn-default">Kembali</a>
        </form>
    </div>
</div>
@endsection
