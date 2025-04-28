<form action="{{ route('penjualan.store_ajax') }}" method="POST" id="form-tambah-penjualan">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">Tambah Transaksi Penjualan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label for="pembeli">Nama Pembeli</label>
            <input type="text" name="pembeli" id="pembeli" class="form-control" required>
        </div>

        <label>Daftar Barang</label>
        <div id="barang-container">
            <!-- Baris barang akan ditambahkan di sini -->
        </div>

        <button type="button" id="add-barang" class="btn btn-sm btn-success mt-2">
            Tambah Barang
        </button>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" id="submit-penjualan">Simpan</button>
    </div>
</form>

<!-- Template baris barang -->
<script type="text/template" id="barang-row-template">
    <div class="row barang-row mb-2">
        <div class="col-md-6">
            <select name="barang_ids[]" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barang as $item)
                    @if($item->stok > 0)
                        <option value="{{ $item->barang_id }}">
                            {{ $item->barang_nama }} (Stok: {{ $item->stok }})
                        </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" name="jumlahs[]" class="form-control" placeholder="Qty" min="1" value="1" required>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger btn-block remove-barang">Hapus</button>
        </div>
    </div>
</script>

<script>
$(function () {
    let formSubmitting = false;

    function addBarangRow() {
        const rowTemplate = $('#barang-row-template').html();
        $('#barang-container').append(rowTemplate);
    }

    // Initialize first row
    addBarangRow();

    // Handle add button click
    $('#add-barang').off('click').on('click', function () {
        addBarangRow();
    });

    // Handle remove button click using event delegation
    $(document).off('click', '.remove-barang').on('click', '.remove-barang', function () {
        $(this).closest('.barang-row').remove();
    });

    // Unbind any existing submit handlers and add new one
    $('#form-tambah-penjualan').off('submit').on('submit', function(e) {
        e.preventDefault();

        // Prevent duplicate submissions
        if (formSubmitting) {
            return false;
        }

        formSubmitting = true;
        let form = $(this);
        let formData = new FormData(this);
        let submitBtn = $('#submit-penjualan');

        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                formSubmitting = false;
                submitBtn.prop('disabled', false).text('Simpan');

                if (response.success) {
                    $('#main-modal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function(xhr) {
                formSubmitting = false;
                submitBtn.prop('disabled', false).text('Simpan');

                let message = 'Terjadi kesalahan saat mengirim data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire('Error!', message, 'error');
            }
        });
    });
});
</script>
