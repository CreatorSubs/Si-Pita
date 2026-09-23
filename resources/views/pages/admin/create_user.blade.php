@extends('layouts.main')

@section('title', 'Buat Akun Admin')

@section('content')
<div class="container d-flex justify-content-center py-4">
    <div class="card p-4 p-md-5 shadow-sm w-100" style="max-width: 600px; background-color: #eef2ff; border: 2px solid #000000; border-radius: 28px;">
        <h4 class="fw-bold text-dark text-center mb-4">Tambah Akun Admin Baru</h4>

        @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Nama Lengkap</label>
                <input type="text" name="name" class="form-control border-dark rounded-pill py-2 px-3" value="{{ old('name') }}" placeholder="Masukkan nama..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Email</label>
                <input type="email" name="email" class="form-control border-dark rounded-pill py-2 px-3" value="{{ old('email') }}" placeholder="Masukkan email..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-dark">Password</label>
                <input type="password" name="password" class="form-control border-dark rounded-pill py-2 px-3" placeholder="Minimal 8 karakter" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-dark">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control border-dark rounded-pill py-2 px-3" placeholder="Ulangi password" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold" style="background-color: #2563eb; border: none;">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
