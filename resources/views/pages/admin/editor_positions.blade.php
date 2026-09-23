@extends('layouts.main')

@section('title', 'Posisi Teks & QR')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card border-2 border-dark rounded-4 bg-white shadow-sm p-4 text-center max-w-5xl mx-auto">
        <h4 class="fw-bold text-dark mb-1">🎯 Posisi Teks & QR Code</h4>
        <p class="text-muted small mb-4">Geser (drag & drop) semua elemen teks dan QR Code ke lokasi yang diinginkan pada sertifikat.</p>

        <!-- Preview Container Utama -->
        <div class="border-2 border-dark rounded-4 p-3 bg-light d-flex flex-column align-items-center justify-content-center shadow-inner mb-4">
            
            <!-- Canvas Container Sertifikat -->
            <div id="certificateCanvas" class="position-relative border border-dark rounded overflow-hidden shadow-sm bg-white d-inline-block mx-auto" style="max-width: 100%; user-select: none;">
                
                <!-- Image Template Sertifikat -->
                <img id="editorCertImage" src="" class="img-fluid d-none" style="max-height: 500px; width: auto; display: block;">

                <!-- ELEMEN 1: Nomor Sertifikat -->
                <div id="dragCertNumber" class="position-absolute bg-white bg-opacity-75 border border-dark text-dark fw-bold px-2 py-1 rounded shadow-sm cursor-move d-none" style="top: 15%; left: 35%; cursor: move; z-index: 10; font-size: 14px;">
                    <i class="bi bi-grip-vertical me-1"></i> <span id="textCertNumber">[No. Sertifikat]</span>
                </div>

                <!-- ELEMEN 2: Nama Penerima -->
                <div id="dragName" class="position-absolute bg-white bg-opacity-75 border border-primary text-primary fw-bold px-3 py-1 rounded shadow-sm cursor-move d-none" style="top: 40%; left: 32%; cursor: move; z-index: 10; font-size: 18px;">
                    <i class="bi bi-grip-vertical me-1"></i> [Nama Penerima]
                </div>

                <!-- ELEMEN 3: Nama Kegiatan -->
                <div id="dragEventName" class="position-absolute bg-white bg-opacity-75 border border-success text-success fw-bold px-2 py-1 rounded shadow-sm cursor-move d-none" style="top: 55%; left: 30%; cursor: move; z-index: 10; font-size: 14px;">
                    <i class="bi bi-grip-vertical me-1"></i> <span id="textEventName">[Nama Kegiatan]</span>
                </div>

                <!-- ELEMEN 4: QR Code Nyata -->
                <div id="dragQR" class="position-absolute bg-white p-2 border border-dark rounded shadow-sm d-none" style="bottom: 8%; right: 8%; cursor: move; z-index: 10; width: 85px; height: 85px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=VERIFIED-CERTIFICATE-EXAMPLE" alt="QR Code" class="w-100 h-100">
                </div>

                <!-- Placeholder Jika Belum Ada Gambar Upload -->
                <div id="editorPlaceholder" class="fw-bold text-muted fs-4 p-5">
                    Preview Canvas (Posisi Teks & QR)
                </div>

            </div>

            <!-- Pagination Below Preview -->
            <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                <button type="button" class="btn btn-dark btn-sm rounded-circle px-2" disabled>&laquo;</button>
                <span class="badge bg-primary px-3 py-2 border border-dark rounded-pill fs-7 fw-bold">1</span>
                <button type="button" class="btn btn-dark btn-sm rounded-circle px-2" disabled>&raquo;</button>
            </div>
        </div>

        <!-- Tombol Simpan & Kembali -->
        <div>
            <button type="button" onclick="savePositionsAndBack()" class="btn btn-primary border-2 border-dark rounded-pill w-100 py-3 fw-bold fs-5 shadow">
                Posisi Sudah Sesuai
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const draftImg = sessionStorage.getItem('draft_cert_image');
    const draftEvent = sessionStorage.getItem('draft_event_name');
    const draftPrefix = sessionStorage.getItem('draft_cert_prefix');

    const imgElement = document.getElementById('editorCertImage');
    const placeholder = document.getElementById('editorPlaceholder');
    
    const dragCertNumber = document.getElementById('dragCertNumber');
    const dragName = document.getElementById('dragName');
    const dragEventName = document.getElementById('dragEventName');
    const dragQR = document.getElementById('dragQR');

    if (draftEvent) {
        document.getElementById('textEventName').textContent = draftEvent;
    }
    if (draftPrefix) {
        document.getElementById('textCertNumber').textContent = draftPrefix;
    }

    if (draftImg) {
        imgElement.src = draftImg;
        imgElement.classList.remove('d-none');
        placeholder.classList.add('d-none');
    }

    dragCertNumber.classList.remove('d-none');
    dragName.classList.remove('d-none');
    dragEventName.classList.remove('d-none');
    dragQR.classList.remove('d-none');

    makeElementDraggable(dragCertNumber, 'cert_num');
    makeElementDraggable(dragName, 'name');
    makeElementDraggable(dragEventName, 'event');
    makeElementDraggable(dragQR, 'qr');

    function makeElementDraggable(elmnt, keyPrefix) {
        let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
        elmnt.onmousedown = dragMouseDown;

        function dragMouseDown(e) {
            e = e || window.event;
            e.preventDefault();
            pos3 = e.clientX;
            pos4 = e.clientY;
            document.onmouseup = closeDragElement;
            document.onmousemove = elementDrag;
        }

        function elementDrag(e) {
            e = e || window.event;
            e.preventDefault();
            pos1 = pos3 - e.clientX;
            pos2 = pos4 - e.clientY;
            pos3 = e.clientX;
            pos4 = e.clientY;

            const parent = document.getElementById('certificateCanvas');
            let newTop = (elmnt.offsetTop - pos2);
            let newLeft = (elmnt.offsetLeft - pos1);

            if (newTop >= 0 && newTop <= (parent.offsetHeight - elmnt.offsetHeight)) {
                elmnt.style.top = newTop + "px";
            }
            if (newLeft >= 0 && newLeft <= (parent.offsetWidth - elmnt.offsetWidth)) {
                elmnt.style.left = newLeft + "px";
            }

            sessionStorage.setItem(keyPrefix + '_x', newLeft);
            sessionStorage.setItem(keyPrefix + '_y', newTop);
        }

        function closeDragElement() {
            document.onmouseup = null;
            document.onmousemove = null;
        }
    }
});

function savePositionsAndBack() {
    window.location.href = "{{ route('admin.certificate.create') }}";
}
</script>
@endsection
