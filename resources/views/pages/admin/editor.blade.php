@extends('layouts.main')

@section('title', 'Atur Posisi Sertifikat')

@section('content')
<div class="container py-3">
    <div class="card card-custom p-4 shadow-sm">
        <h5 class="fw-bold mb-2 text-dark text-center">Atur Posisi Teks Sertifikat</h5>
        <p class="text-muted text-center small mb-4">Klik dan geser (drag) elemen teks/QR ke posisi yang pas di atas gambar template.</p>

        <!-- Container Canvas Sertifikat -->
        <div id="certificate-canvas" class="position-relative mx-auto border rounded shadow-sm overflow-hidden" 
             style="width: 800px; height: 565px; background: #ffffff url('{{ $templateUrl ?? '' }}') no-repeat center/contain;">
            
            @if(!$certificate->template_path)
                <div class="position-absolute top-50 start-50 translate-middle text-center text-muted">
                    <small>⚠️ Tidak ada gambar template yang diunggah.<br>Gunakan gambar default atau buat sertifikat baru dengan gambar background.</small>
                </div>
            @endif

            <!-- Drag Element: Nomor -->
            <div id="drag-number" class="draggable position-absolute p-2 bg-warning bg-opacity-75 rounded fw-bold text-dark user-select-none shadow-sm" 
                 style="cursor: move; top: {{ $certificate->pos_number_y }}px; left: {{ $certificate->pos_number_x }}px;">
                No: {{ $certificate->certificate_number }}
            </div>

            <!-- Drag Element: Nama -->
              <div id="drag-name" class="draggable position-absolute p-2 bg-primary bg-opacity-75 rounded fw-bold text-white user-select-none shadow-sm" 
                  style="cursor: move; top: {{ $certificate->pos_name_y }}px; left: {{ $certificate->pos_name_x }}px; font-size: 18px;">
                {{ $certificate->recipient_name }}
            </div>

            <div class="certificate-data-preview position-absolute text-dark" style="top: {{ $certificate->pos_name_y + 60 }}px; left: {{ $certificate->pos_name_x }}px; font-size: 13px;">Peran: {{ $certificate->role ?: 'Peserta' }}</div>
            <div class="certificate-data-preview position-absolute text-dark" style="top: {{ $certificate->pos_name_y + 82 }}px; left: {{ $certificate->pos_name_x }}px; font-size: 13px;">Instansi: {{ $certificate->institution ?: '-' }}</div>
            <div class="certificate-data-preview position-absolute text-dark" style="top: {{ $certificate->pos_name_y + 104 }}px; left: {{ $certificate->pos_name_x }}px; font-size: 13px;">Tanggal terbit: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d F Y') }}</div>

            <!-- Drag Element: Kegiatan -->
            <div id="drag-event" class="draggable position-absolute p-2 bg-success bg-opacity-75 rounded fw-bold text-white user-select-none shadow-sm" 
                 style="cursor: move; top: {{ $certificate->pos_event_y }}px; left: {{ $certificate->pos_event_x }}px;">
                {{ $certificate->event_name }}
            </div>

            <!-- Drag Element: QR Code -->
            <div id="drag-qr" class="draggable position-absolute p-2 bg-dark bg-opacity-75 rounded text-white small user-select-none shadow-sm d-flex align-items-center justify-content-center" 
                 style="cursor: move; top: {{ $certificate->pos_qr_y }}px; left: {{ $certificate->pos_qr_x }}px; width: 75px; height: 75px; text-align: center;">
                [ QR Code ]
            </div>
        </div>

        <!-- Form Simpan Koordinat -->
        <form id="form-positions" action="{{ route('admin.certificate.update_positions', $certificate->id) }}" method="POST" class="mt-4 text-center">
            @csrf
            <input type="hidden" name="pos_number_x" id="pos_number_x" value="{{ $certificate->pos_number_x }}">
            <input type="hidden" name="pos_number_y" id="pos_number_y" value="{{ $certificate->pos_number_y }}">
            <input type="hidden" name="pos_name_x" id="pos_name_x" value="{{ $certificate->pos_name_x }}">
            <input type="hidden" name="pos_name_y" id="pos_name_y" value="{{ $certificate->pos_name_y }}">
            <input type="hidden" name="pos_event_x" id="pos_event_x" value="{{ $certificate->pos_event_x }}">
            <input type="hidden" name="pos_event_y" id="pos_event_y" value="{{ $certificate->pos_event_y }}">
            <input type="hidden" name="pos_qr_x" id="pos_qr_x" value="{{ $certificate->pos_qr_x }}">
            <input type="hidden" name="pos_qr_y" id="pos_qr_y" value="{{ $certificate->pos_qr_y }}">

            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('admin.certificate.create') }}" class="btn btn-secondary rounded-pill px-4">Batal / Kembali</a>
                <button type="submit" class="btn btn-primary-custom text-white rounded-pill px-4">Simpan Posisi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('certificate-canvas');
    const draggables = document.querySelectorAll('.draggable');

    draggables.forEach(elem => {
        let isDragging = false;
        let startX, startY, initialLeft, initialTop;

        elem.addEventListener('pointerdown', function (e) {
            e.preventDefault();
            elem.setPointerCapture(e.pointerId);
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            initialLeft = elem.offsetLeft;
            initialTop = elem.offsetTop;
            elem.style.zIndex = 1000;
        });

        elem.addEventListener('pointermove', function (e) {
            if (!isDragging) return;

            const dx = e.clientX - startX;
            const dy = e.clientY - startY;

            let newLeft = initialLeft + dx;
            let newTop = initialTop + dy;

            // Batasi perpindahan hanya di dalam area canvas
            const maxLeft = canvas.clientWidth - elem.clientWidth;
            const maxTop = canvas.clientHeight - elem.clientHeight;

            newLeft = Math.max(0, Math.min(newLeft, maxLeft));
            newTop = Math.max(0, Math.min(newTop, maxTop));

            elem.style.left = newLeft + 'px';
            elem.style.top = newTop + 'px';

            // Sync koordinat ke input form hidden
            if (elem.id === 'drag-number') {
                document.getElementById('pos_number_x').value = newLeft;
                document.getElementById('pos_number_y').value = newTop;
            } else if (elem.id === 'drag-name') {
                document.getElementById('pos_name_x').value = newLeft;
                document.getElementById('pos_name_y').value = newTop;
                document.querySelectorAll('.certificate-data-preview').forEach((detail, index) => {
                    detail.style.left = newLeft + 'px';
                    detail.style.top = (newTop + [60, 82, 104][index]) + 'px';
                });
            } else if (elem.id === 'drag-event') {
                document.getElementById('pos_event_x').value = newLeft;
                document.getElementById('pos_event_y').value = newTop;
            } else if (elem.id === 'drag-qr') {
                document.getElementById('pos_qr_x').value = newLeft;
                document.getElementById('pos_qr_y').value = newTop;
            }
        });

        elem.addEventListener('pointerup', function (e) {
            if (isDragging) {
                isDragging = false;
                elem.style.zIndex = 'auto';
                if (elem.hasPointerCapture(e.pointerId)) elem.releasePointerCapture(e.pointerId);
            }
        });
    });
});
</script>
@endpush
