<form action="{{ route('stok.store_ajax') }}" method="POST" id="form-tambah-stok">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Tambah Data Stok Barang</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label for="barang_id">Barang</label>
            <select name="barang_id" id="barang_id" class="form-control" required>
                <option value="">Pilih Barang</option>
                @foreach($barang as $item)
                    <option value="{{ $item->barang_id }}">{{ $item->barang_nama }}</option>
                @endforeach
            </select>
            <small id="error-barang_id" class="error-text form-text text-danger"></small>
        </div>
        <div class="form-group">
            <label for="stok_jumlah">Jumlah Stok</label>
            <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" required min="1">
            <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

{{-- Keep the existing script section as is --}}
