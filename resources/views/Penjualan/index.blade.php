@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Transaksi Penjualan</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ route('penjualan.create_ajax') }}')" class="btn btn-success btn-sm">
                Tambah Transaksi
            </button>
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

<div class="modal fade" id="main-modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="modal-body-1">
                {{-- Modal content will be loaded here --}}
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Global form submission flag
let formSubmitting = false;

function modalAction(url) {
    $('#main-modal #modal-body-1').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
    $('#main-modal').modal('show');

    $.get(url, function(res) {
        $('#main-modal #modal-body-1').html(res);
        initializePenjualanForm(); // Initialize form after loading content
    }).fail(function() {
        $('#main-modal #modal-body-1').html('<div class="alert alert-danger">Gagal memuat data.</div>');
    });
}

function initializePenjualanForm() {
    // Add first row
    // addBarangRow();

    // Handle add button click
    $('#add-barang').off('click').on('click', function() {
        addBarangRow();
    });

    // Handle remove button click using event delegation
    $(document).off('click', '.remove-barang').on('click', '.remove-barang', function() {
        if ($('.barang-row').length > 1) {
            $(this).closest('.barang-row').remove();
        } else {
            Swal.fire('Peringatan', 'Minimal harus ada satu barang', 'warning');
        }
    });

    // Form submission handler
    $('#form-tambah-penjualan').off('submit').on('submit', function(e) {
        e.preventDefault();
        if (formSubmitting) return false;

        formSubmitting = true;
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#main-modal').modal('hide');
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        table.ajax.reload();
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim data.';
                Swal.fire('Error!', message, 'error');
            },
            complete: function() {
                formSubmitting = false;
                submitBtn.prop('disabled', false).text('Simpan');
            }
        });
    });
}

function addBarangRow() {
    const template = $('#barang-row-template').html();
    $('#barang-container').append(template);
}
</script>
@endpush
@endsection
