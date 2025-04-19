@extends('layouts.template')

@section('content')
<div class="container">
    <h1>Transaksi Penjualan</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div id="error-container" class="alert alert-danger" style="display:none;"></div>

    <form id="transaksiForm" action="{{ route('penjualan.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="pembeli">Nama Pembeli</label>
            <input type="text" name="pembeli" id="pembeli" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label>Pilih Barang yang Dibeli (masing-masing akan dianggap beli 1 unit)</label>
            <div class="row">
                @foreach($barang as $item)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="barang_ids[]" value="{{ $item->barang_id }}" class="form-check-input barang-checkbox" id="barang_{{ $item->barang_id }}" data-barang-id="{{ $item->barang_id }}">
                            <label class="form-check-label" for="barang_{{ $item->barang_id }}" id="label_{{ $item->barang_id }}">
                                {{ $item->barang_nama }} (Stok: {{ $item->stok }})
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-4" id="submitBtn" disabled>Simpan Transaksi</button>
    </form>
</div>
@endsection

@push('js')
<script>
    function updateCheckboxStates(stokInfo) {
        let adaYangBisaDipilih = false;

        document.querySelectorAll('.barang-checkbox').forEach(cb => {
            const id = cb.dataset.barangId;
            const label = document.getElementById('label_' + id);

            if (stokInfo[id] === 'Stok tidak cukup') {
                label.style.color = 'red';
                cb.checked = false;
                cb.disabled = true;
            } else {
                label.style.color = 'black';
                cb.disabled = false;
                adaYangBisaDipilih = true;
            }
        });

        const checked = document.querySelectorAll('.barang-checkbox:checked');
        const validChecked = Array.from(checked).filter(cb => !cb.disabled);
        document.getElementById('submitBtn').disabled = validChecked.length === 0 && !adaYangBisaDipilih;
    }

    function cekStokBarang() {
        const allCheckboxes = document.querySelectorAll('.barang-checkbox');
        const barangIds = Array.from(allCheckboxes).map(item => item.dataset.barangId);

        if (barangIds.length === 0) {
            document.getElementById('submitBtn').disabled = true;
            return;
        }

        fetch("{{ route('penjualan.cek-stok') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ barang_ids: barangIds })
        })
        .then(res => res.json())
        .then(data => {
            updateCheckboxStates(data.stokInfo);
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat memeriksa stok.');
        });
    }

    // Cek stok saat halaman pertama kali dimuat
    window.addEventListener('DOMContentLoaded', () => {
        cekStokBarang();
    });

    // Cek ulang saat checkbox berubah
    document.querySelectorAll('.barang-checkbox').forEach(cb => {
        cb.addEventListener('change', cekStokBarang);
    });

    // Submit form
    document.getElementById('transaksiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorContainer = document.getElementById('error-container');
        errorContainer.style.display = 'none';

        const checkedItems = Array.from(document.querySelectorAll('.barang-checkbox:checked')).filter(cb => !cb.disabled);
        if (checkedItems.length === 0) {
            errorContainer.textContent = 'Pilih minimal satu barang.';
            errorContainer.style.display = 'block';
            return;
        }

        const formData = new FormData(this);
        formData.delete('barang_ids[]');

        checkedItems.forEach((checkbox, index) => {
            formData.append(`penjualan_details[${index}][barang_id]`, checkbox.value);
        });

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect || "{{ route('penjualan.index') }}";
            } else {
                errorContainer.textContent = data.message || 'Terjadi kesalahan saat menyimpan transaksi.';
                errorContainer.style.display = 'block';
            }
        })
        .catch(() => {
            errorContainer.textContent = 'Terjadi kesalahan saat menyimpan transaksi.';
            errorContainer.style.display = 'block';
        });
    });
</script>
@endpush
