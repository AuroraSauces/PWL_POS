@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Tambah Transaksi Penjualan</h3>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('penjualan.store') }}" method="POST" id="form-penjualan">
            @csrf

            <!-- Kolom Pembeli -->
            <div class="form-group">
                <label for="pembeli">Nama Pembeli</label>
                <input type="text" name="pembeli" class="form-control" required>
            </div>

            <!-- Barang -->
            <div class="form-group">
                <label for="barang_id">Barang</label>
                <select name="penjualan_details[0][barang_id]" id="barang_id" class="form-control" required>
                    @foreach($barang as $item)
                        <option value="{{ $item->barang_id }}">{{ $item->barang_nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah -->
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" name="penjualan_details[0][jumlah]" id="jumlah" class="form-control" required>
            </div>

            <!-- Tombol Simpan -->
            <button type="submit" class="btn btn-success btn-sm" id="submit-btn">Simpan Transaksi</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#form-penjualan').on('submit', function(e) {
    e.preventDefault(); // Stop form submit

    const form = this;
    const barang_id = $('#barang_id').val();
    const jumlah = $('#jumlah').val();
    const submitBtn = $('#submit-btn');

    // Disable button
    submitBtn.prop('disabled', true).text('Mengecek Stok...');

    $.ajax({
        url: "{{ route('penjualan.cek-stok') }}",
        type: "POST",
        data: {
            barang_id: barang_id,
            jumlah: jumlah,
            _token: '{{ csrf_token() }}'
        }
    })
    .done(function(response) {
        // Stock is sufficient, proceed with form submission
        form.submit();
    })
    .fail(function(xhr) {
        let response = xhr.responseJSON;

        // Show error message
        if (response && response.message) {
            alert(response.message);
        } else {
            alert('Gagal memeriksa stok. Coba lagi.');
        }

        // Note: We don't submit the form when there's an error
    })
    .always(function() {
        // Re-enable button
        submitBtn.prop('disabled', false).text('Simpan Transaksi');
    });
});
</script>
@endpush
