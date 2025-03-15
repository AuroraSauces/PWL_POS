<form action="{{ route('supplier.store_ajax') }}" method="POST" id="form-tambah-supplier">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Supplier Kode -->
                <div class="form-group">
                    <label>Kode Supplier</label>
                    <input type="text" name="supplier_kode" id="supplier_kode" class="form-control" required>
                    <small id="error-supplier_kode" class="error-text form-text text-danger"></small>
                </div>
                <!-- Supplier Nama -->
                <div class="form-group">
                    <label>Nama Supplier</label>
                    <input type="text" name="supplier_nama" id="supplier_nama" class="form-control" required>
                    <small id="error-supplier_nama" class="error-text form-text text-danger"></small>
                </div>
                <!-- Supplier Alamat -->
                <div class="form-group">
                    <label>Alamat Supplier</label>
                    <textarea name="supplier_alamat" id="supplier_alamat" class="form-control" rows="3"></textarea>
                    <small id="error-supplier_alamat" class="error-text form-text text-danger"></small>
                </div>
                <!-- Supplier Kontak -->
                <div class="form-group">
                    <label>Kontak Supplier</label>
                    <input type="text" name="supplier_kontak" id="supplier_kontak" class="form-control">
                    <small id="error-supplier_kontak" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-tambah-supplier").validate({
        rules: {
            supplier_kode: { required: true, maxlength: 20 },
            supplier_nama: { required: true, minlength: 3, maxlength: 255 },
            supplier_alamat: { maxlength: 500 },
            supplier_kontak: { maxlength: 255 }
        },
        submitHandler: function(form) {
            $.ajax({
                url: $(form).attr('action'),
                type: $(form).attr('method'),
                data: $(form).serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        if (typeof dataSupplier !== 'undefined') {
                            dataSupplier.ajax.reload();
                        }
                    } else {
                        $('.error-text').text('');
                        $.each(response.msgField, function(prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data.'
                    });
                }
            });
            return false;
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>
