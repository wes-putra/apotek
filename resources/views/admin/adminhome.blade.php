@extends('layouts.app')
  
@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0">Daftar Obat</h2>
        <a class="btn btn-primary" href="{{ route('adminobat.create') }}">Tambah</a>
    </div>
    
    <!-- Tabel untuk menampilkan data -->
    <table class="table table-bordered">
        <thead>
            <tr class="text-center">
                <th>No</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($obats as $obat)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $obat->nama }}</td>
                <td>{{ $obat->deskripsi }}</td>
                <td>{{ $obat->harga }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
