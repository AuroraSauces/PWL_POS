@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Tambah Supplier</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('supplier') }}">
            @csrf
            <div class="form-group">
                <label>Kode Supplier</label>
                <input type="text" class="form-control" name="supplier_kode" required>
                @error('supplier_kode')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" class="form-control" name="supplier_nama" required>
                @error('supplier_nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" name="supplier_alamat" rows="3"></textarea>
                @error('supplier_alamat')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Kontak</label>
                <input type="text" class="form-control" name="supplier_kontak">
                @error('supplier_kontak')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ url('supplier') }}" class="btn btn-default">Kembali</a>
        </form>
    </div>
</div>
@endsection
