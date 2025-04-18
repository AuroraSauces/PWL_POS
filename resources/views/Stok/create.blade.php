@extends('layouts.template')

@section('content')
<div class="container">
    <h1>Tambah Stok Barang</h1>

    <form action="{{ route('stok.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="barang_id">Barang</label>
            <select name="barang_id" id="barang_id" class="form-control" required>
                <option value="">Pilih Barang</option>
                @foreach($barang as $item)
                    <option value="{{ $item->barang_id }}">{{ $item->barang_nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="stok_jumlah">Jumlah Stok</label>
            <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" required min="1">
        </div>

        <button type="submit" class="btn btn-primary mt-3">Simpan</button>
    </form>
</div>
@endsection
