@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card card-body border-0">
        <div class="clearfix mb-3">
            <div class="float-start">
                <h5>Transaksi Penjualan</h5>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <label for="namapb">Nama</label>
            <input type="text" id="namapb" class="form-control" input>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="obat">Nama Obat</label>
                <select name="obat" id="obat" class="form-control select2">
                    <option value="0">Pilih Obat</option>
                    @foreach ($obats as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->harga }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="harga">Harga Satuan</label>
                <input type="text" id="harga" class="form-control" readonly>
            </div>
            <div class="col-md-4 mb-3">
                <label for="jumlah">Kuantitas</label>
                <input type="number" id="jumlah" class="form-control">
            </div>
            <input type="hidden" id="selected-product-id">
            <div class="col-md-12 text-end">
                <button class="btn btn-success btn-sm text-white rounded" id="simpanBtn"><i class="fa fa-save"></i>Simpan</button>
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Harga Satuan</th>
                        <th>Kuantitas</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="transaction-records">
                    <!-- Data transaksi akan ditampilkan di sini -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total Jumlah Harga :</strong></td>
                        <td colspan="2"><span id="total-price">0</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="text-end">
            <button id="simpanTransaksiBtn" class="btn btn-sm btn-primary rounded"><i class="fa fa-save"></i> Simpan Transaksi</button>
        </div>
    </div>

    <!-- Modal untuk menampilkan pesan transaksi berhasil -->
    
</div>

<!-- Script JavaScript -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script>
    const selectObat = document.getElementById('obat');
    const inputHarga = document.getElementById('harga');

    const transactionRecords = document.getElementById('transaction-records');
    let totalHarga = 0;

    selectObat.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        inputHarga.value = price;
    });

    function tambahObatKeTabel() {
        const obatSelect = document.getElementById('obat');
        const selectedOption = obatSelect.options[obatSelect.selectedIndex];
        const obatId = selectedOption.value;
        const obatNama = selectedOption.text;
        const obatHarga = parseFloat(selectedOption.getAttribute('data-price'));
        const obatJumlah = parseInt(document.getElementById('jumlah').value);

        // Periksa apakah ada field yang kosong
        if (!obatId || isNaN(obatHarga) || isNaN(obatJumlah)) {
            alert('Silakan lengkapi semua field sebelum menambahkan obat.');
            return;
        }

        const subtotal = obatHarga * obatJumlah;

        const row = document.createElement('tr');
        row.innerHTML = `
        <td>${obatNama}</td>
        <td class="harga">${obatHarga}</td>
        <td>
            <div class="input-group">
                <button class="btn btn-primary btn-sm" onclick="tambahObat(this)">+</button>
                <input class="kuantitas" value="${obatJumlah}">
                <button class="btn btn-primary btn-sm" onclick="kurangObat(this)">-</button>
            </div>
        </td>

        <td class="subtotal">${subtotal}</td>
        <td>
            <button class="btn btn-danger btn-sm" onclick="hapusObat(this)">Hapus</button>
        </td>
        `;

        transactionRecords.appendChild(row);

        totalHarga += subtotal;
        document.getElementById('total-price').textContent = totalHarga;

        obatSelect.selectedIndex = 0;
        inputHarga.value = '';
        document.getElementById('jumlah').value = '';
        obatSelect.focus();
    }

    function hapusObat(button) {
        const row = button.closest('tr');
        const subtotal = parseFloat(row.children[3].textContent);
        totalHarga -= subtotal;
        document.getElementById('total-price').textContent = totalHarga;
        row.remove();
    }

    function tambahObat(button) {
        const row = button.closest('tr');
        const kuantitasInput = row.querySelector('.kuantitas'); // Mengambil input kuantitas
        const subtotalElement = row.querySelector('.subtotal'); // Mengambil elemen subtotal
        const harga = parseFloat(row.querySelector('.harga').textContent);
        
        let kuantitas = parseFloat(kuantitasInput.value);
        kuantitas++;
        kuantitasInput.value = kuantitas; // Mengubah nilai input
        
        const subtotal = kuantitas * harga;
        subtotalElement.textContent = subtotal; // Memperbarui subtotal

        totalHarga += harga;
        document.getElementById('total-price').textContent = totalHarga;
    }


    function kurangObat(button) {
        const row = button.closest('tr');
        const kuantitasInput = row.querySelector('.kuantitas'); // Mengambil input kuantitas
        const subtotalElement = row.querySelector('.subtotal'); // Mengambil elemen subtotal
        
        let kuantitas = parseInt(kuantitasInput.value);
        if (kuantitas > 1) {
            kuantitas--;
            const harga = parseFloat(row.querySelector('.harga').textContent);
            const subtotal = kuantitas * harga;

            kuantitasInput.value = kuantitas; // Mengubah nilai input
            subtotalElement.textContent = subtotal;

            totalHarga -= harga;
            document.getElementById('total-price').textContent = totalHarga;
        }
    }

    document.getElementById('simpanBtn').addEventListener('click', function (e) {
        const obatSelect = document.getElementById('obat');
        const selectedOption = obatSelect.options[obatSelect.selectedIndex];
        const obatNama = selectedOption.text;

        let isObatExists = false; // Untuk melacak apakah obat dengan nama yang sama ada di daftar transaksi

        document.querySelectorAll('#transaction-records tr').forEach(function (row) {
            var namaObat = row.querySelector('td:first-child').textContent;
            if (namaObat === obatNama) {
                isObatExists = true;
            }
        });

        if (isObatExists) {
            alert('Obat dengan nama yang sama sudah ada dalam daftar transaksi.');
            e.preventDefault();
        } else {
            tambahObatKeTabel();
        }
    });


    document.getElementById('simpanTransaksiBtn').addEventListener('click', function () {
        var transaksi = [];
        var namaPembeli = document.getElementById('namapb').value;


        document.querySelectorAll('#transaction-records tr').forEach(function (row) {
            var obatId = row.querySelector('td:first-child').textContent;
            var harga = parseFloat(row.querySelector('td:nth-child(2)').textContent); 
            var subtotal = parseInt(row.querySelector('td:nth-child(4)').textContent, 10);
            var jumlah = subtotal/harga;
            console.log(jumlah);

            transaksi.push({
                nama: obatId,
                harga: harga,
                jumlah: jumlah
            });
        });

        // Kirim data ke server menggunakan AJAX
        $.ajax({
            type: 'POST',
            url: '{{ url("Transaksi/store") }}', // Ganti dengan URL yang sesuai di aplikasi Anda
            data: {
                namaPembeli: namaPembeli,
                totalHarga: totalHarga,
                transaksi: transaksi,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                // Transaksi berhasil disimpan
                alert('Transaksi berhasil disimpan.');
                window.location.href = '{{ route("transaksi.index") }}';
            },
            error: function (xhr, textStatus, errorThrown) {
                console.error('Error:', errorThrown);
                alert('Transaksi gagal. Silakan coba lagi.');
            }
        });
    });



</script>
@endsection