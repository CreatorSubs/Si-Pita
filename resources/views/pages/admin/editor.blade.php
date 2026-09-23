@extends('layouts.main')

@section('title', 'Atur Posisi Sertifikat')

@section('content')
<div class="container">
    <div class="card card-custom p-4 shadow-sm">
        <h5 class="fw-bold mb-3 text-dark text-center">Atur Posisi Teks Sertifikat</h5>
        <p class="text-muted text-center small mb-4">Geser (Drag) teks ke posisi yang diinginkan di atas template, lalu klik Simpan Posisi.</p>

        <div id="certificate-canvas" class="position-relative mx-auto border rounded shadow-sm overflow-hidden" style="width: 800px; height: 565px; background: #fff url('{{ $certificate->template_path ? asset("storage/" . $certificate->template_path) : "" }}') no-repeat center/cover;">
            
            <div id="drag-number" class="position-absolute p-2 bg-warning bg-opacity-75 rounded fw-bold text-dark" style="cursor: move; top: {{ $certificate->pos_number_y }}px; left: {{ $certificate->pos_number_x }}px;">
                No: {{ $certificate->certificate_number }}
            </div>

            <div id="drag-name" class="position-absolute p-2 bg-primary bg-opacity-75 rounded fw-bold text-white fs-5" style="cursor: move; top: {{ $certificate->pos_name_y }}px; left: {{ $certificate->pos_name_x }}px;">
                {{ $certificate->recipient_name }}
            </div>

            <div id="drag-qr" class="position-absolute p-2 bg-dark bg-opacity-75 rounded text-white small" style="cursor: move; top: {{ $certificate->pos_qr_y }}px; left: {{ $certificate->pos_qr_x }}px; width: 70px; height: 70px; text-align: center;">
                [ QR Code ]
            </div>
        </div>

        <form id="form-positions" action="{{ route('admin.certificate.update_positions', $certificate->id) }}" method="POST" class="mt-4 text-center">
            @csrf
            <input type="hidden" name="pos_number_x" id="pos_number_x" value="{{ $certificate->pos_number_x }}">
            <input type="hidden" name="pos_number_y" id="pos_number_y" value="{{ $certificate->pos_number_y }}">
            <input type="hidden" name="pos_name_x" id="pos_name_x" value="{{ $certificate->pos_name_x }}">
            <input type="hidden" name="pos_name_y" id="pos_name_y" value="{{ $certificate->pos_name_y }}">
            <input type="hidden" name="pos_qr_x" id="pos_qr_x" value="{{ $certificate->pos_qr_x }}">
            <input type="hidden" name="pos_qr_y" id="pos_qr_y" value="{{ $certificate->pos_qr_y }}">

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill px-4 me-2">Selesai / Kembali</a>
            <button type="submit" class="btn btn-primary-custom text-white rounded-pill px-4">Simpan Posisi</button>
        </form>
    </div>
</div>
@endsection
