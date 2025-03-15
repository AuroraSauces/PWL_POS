@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Edit Supplier</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('supplier/'.$supplier->supplier_id) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Kode Supplier</label>
                <input type="text" class="form-control" name="supplier_kode" value="{{ $supplier->supplier_kode }}" required>
                @error('supplier_kode')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" class="form-control" name="supplier_nama" value="{{ $supplier->supplier_nama }}" required>
                @error('supplier_nama')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea class="form-control" name="supplier_alamat" rows="3">{{ $supplier->supplier_alamat }}</textarea>
                @error('supplier_alamat')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label>Kontak</label>
                <input type="text" class="form-control" name="supplier_kontak" value="{{ $supplier->supplier_kontak }}">
                @error('supplier_kontak')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ url('supplier') }}" class="btn btn-default">Kembali</a>
        </form>
    </div>
</div>
@endsection
