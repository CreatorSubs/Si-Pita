@extends('layouts.main')
@section('title', 'Data Tidak Ditemukan')
@section('content')
<div class="container text-center">
    <div class="card card-custom p-5 shadow-sm">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h3>DATA SERTIFIKAT TIDAK DITEMUKAN</h3>
        <p class="text-muted">Periksa kembali Nama atau Nomor Identitas Anda.</p>
        <a href="{{ route('landing') }}" class="btn btn-primary-custom text-white rounded-pill px-4">Cari Ulang</a>
    </div>
</div>
@endsection
