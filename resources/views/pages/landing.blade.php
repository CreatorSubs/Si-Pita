@extends('layouts.main')
@section('title', 'Selamat Datang')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card card-custom p-4 p-md-5 text-center shadow-sm">
                <h4 class="fw-bold text-uppercase mb-4 text-dark">Selamat Datang Di Website<br>SI-PITA</h4>
                <form action="{{ route('certificate.search') }}" method="GET">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6"><input type="text" name="nama" class="form-control rounded-pill text-center border-0" placeholder="Masukkan Nama"></div>
                        <div class="col-md-6"><input type="text" name="nomor_identitas" class="form-control rounded-pill text-center border-0" placeholder="Masukkan Nomor Identitas"></div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom text-white px-5 rounded-pill shadow-sm">Cari Data</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
