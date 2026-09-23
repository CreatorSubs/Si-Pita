@extends('layouts.main')

@section('title', 'Buat Sertifikat Baru')

@section('content')
<div class="container d-flex justify-content-center py-4">
    <div class="card p-4 p-md-5 shadow-sm w-100" style="max-width: 800px; background-color: #eef2ff; border: 2px solid #000000; border-radius: 28px;">
        <h4 class="fw-bold text-center mb-4 text-dark">Form Pembuatan Sertifikat</h4>

        @if ($errors->any())
            <div class="alert alert-danger rounded-3 mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.certificate.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <!-- Nomor Sertifikat -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor Sertifikat</label>
                    <input type="text" name="certificate_number" class="form-control rounded-3" placeholder="Contoh: SERT/001/2026" value="{{ old('certificate_number') }}" required>
                </div>

                <!-- Tanggal Terbit -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Terbit</label>
                    <input type="date" name="issue_date" class="form-control rounded-3" value="{{ old('issue_date', date('Y-m-d')) }}" required>
                </div>

                <!-- Nama Penerima -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Penerima</label>
                    <input type="text" name="recipient_name" class="form-control rounded-3" placeholder="Nama Lengkap & Gelar" value="{{ old('recipient_name') }}" required>
                </div>

                <!-- NIK / NIP / NIM -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Identitas (NIK / NIP / NIM)</label>
                    <input type="text" name="recipient_identity" class="form-control rounded-3" placeholder="Nomor Identitas" value="{{ old('recipient_identity') }}" required>
                </div>

                <!-- Instansi / Perusahaan -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Instansi / Organisasi</label>
                    <input type="text" name="institution" class="form-control rounded-3" placeholder="Contoh: Diskominfo" value="{{ old('institution') }}">
                </div>

                <!-- Peran -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Peran / Sebagai</label>
                    <select name="role" class="form-select rounded-3" required>
                        <option value="Peserta" {{ old('role') == 'Peserta' ? 'selected' : '' }}>Peserta</option>
                        <option value="Narasumber" {{ old('role') == 'Narasumber' ? 'selected' : '' }}>Narasumber</option>
                        <option value="Panitia" {{ old('role') == 'Panitia' ? 'selected' : '' }}>Panitia</option>
                        <option value="Peserta Magang" {{ old('role') == 'Peserta Magang' ? 'selected' : '' }}>Peserta Magang</option>
                    </select>
                </div>

                <!-- Nama Kegiatan -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Nama Kegiatan / Acara</label>
                    <input type="text" name="event_name" class="form-control rounded-3" placeholder="Contoh: Pelatihan Web Developer 2026" value="{{ old('event_name') }}" required>
                </div>

                <!-- Upload Template Background -->
                <div class="col-12">
                    <label class="form-label fw-semibold">Upload Desain Template (PNG / JPG)</label>
                    <input type="file" name="template" class="form-control rounded-3" accept="image/png, image/jpeg">
                    <small class="text-muted fs-7">*Kosongkan jika ingin menggunakan gambar default.</small>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold" style="background-color: #2563eb; border: none;">
                    Lanjut Atur Posisi Teks & QR &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
