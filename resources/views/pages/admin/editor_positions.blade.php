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
            <div id="certificateCanvas" class="position-relative border border-dark rounded overflow-hidden shadow-sm bg-white mx-auto" style="width: min(800px, 100%); aspect-ratio: 800 / 565; user-select: none; touch-action: none;">
                
                <!-- Image Template Sertifikat -->
                <img id="editorCertImage" src="" class="position-absolute top-0 start-0 w-100 h-100 d-none" style="object-fit: fill; pointer-events: none;">

                <!-- ELEMEN 1: Nomor Sertifikat -->
                <div id="dragCertNumber" class="position-absolute bg-white bg-opacity-75 border border-dark text-dark fw-bold px-2 py-1 rounded shadow-sm cursor-move" style="top: 7.08%; left: 25%; cursor: move; z-index: 10; font-size: 14px;">
                    <i class="bi bi-grip-vertical me-1"></i> No: <span id="textCertNumber">[No. Sertifikat]</span>
                </div>

                <!-- ELEMEN 2: Nama Penerima -->
                <div id="dragName" class="position-absolute bg-white bg-opacity-75 border border-primary text-primary fw-bold px-3 py-1 rounded shadow-sm cursor-move" style="top: 38.94%; left: 31.25%; cursor: move; z-index: 10; font-size: 18px;">
                    <i class="bi bi-grip-vertical me-1"></i> <span id="textRecipientName">[Nama Penerima]</span>
                </div>

                <!-- ELEMEN 3: Nama Kegiatan -->
                <div id="dragEventName" class="position-absolute bg-white bg-opacity-75 border border-success text-success fw-bold px-2 py-1 rounded shadow-sm cursor-move" style="top: 45.66%; left: 31.25%; cursor: move; z-index: 10; font-size: 13px;">
                    <i class="bi bi-grip-vertical me-1"></i> <span id="textEventName">[Nama Kegiatan]</span>
                </div>

                <div id="previewRole" class="position-absolute text-dark" style="left: 31.25%; top: 49.56%; font-size: 13px;">Peran: Peserta</div>
                <div id="previewInstitution" class="position-absolute text-dark" style="left: 31.25%; top: 53.45%; font-size: 13px;">Instansi: Diskominfo</div>
                <div id="previewIssueDate" class="position-absolute text-dark" style="left: 31.25%; top: 57.35%; font-size: 13px;">Tanggal terbit: {{ \Carbon\Carbon::parse(old('issue_date', date('Y-m-d')))->format('d F Y') }}</div>

                <!-- ELEMEN 4: QR Code Nyata -->
                <div id="dragQR" class="position-absolute bg-white p-2 border border-dark rounded shadow-sm" style="top: 77.88%; left: 85%; cursor: move; z-index: 10; width: 9.375%; aspect-ratio: 1;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=VERIFIED-CERTIFICATE-EXAMPLE" alt="QR Code" class="w-100 h-100">
                </div>

                <!-- Placeholder Jika Belum Ada Gambar Upload -->
                <div id="editorPlaceholder" class="position-absolute top-50 start-50 translate-middle fw-bold text-muted fs-4 p-5 text-center">
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
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <button type="button" id="resetDraftPositions" class="btn btn-outline-secondary rounded-pill px-4 py-3 fw-semibold">
                Atur Ulang Posisi
            </button>
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
    const draftParticipants = JSON.parse(sessionStorage.getItem('draft_participants') || '[]');
    const firstParticipant = draftParticipants.find(participant => participant.name);

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
    if (firstParticipant) {
        document.getElementById('textRecipientName').textContent = firstParticipant.name;
        document.getElementById('previewRole').textContent = `Peran: ${firstParticipant.role || 'Peserta'}`;
        document.getElementById('previewInstitution').textContent = `Instansi: ${firstParticipant.agency || 'Diskominfo'}`;
    }
    const draftIssueDate = sessionStorage.getItem('draft_issue_date');
    if (draftIssueDate) {
        const formattedDate = new Date(`${draftIssueDate}T00:00:00`).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric',
        });
        document.getElementById('previewIssueDate').textContent = `Tanggal terbit: ${formattedDate}`;
    }

    if (draftImg) {
        imgElement.src = draftImg;
        imgElement.classList.remove('d-none');
        placeholder.classList.add('d-none');
    }

    const canvas = document.getElementById('certificateCanvas');
    const defaultPositions = {
        number: { x: 200, y: 40 },
        name: { x: 250, y: 220 },
        event: { x: 250, y: 258 },
        qr: { x: 680, y: 440 },
    };
    const elements = [
        ['dragCertNumber', 'number'],
        ['dragName', 'name'],
        ['dragEventName', 'event'],
        ['dragQR', 'qr'],
    ];

    function applyPosition(key, x, y) {
        const elementId = {
            number: 'dragCertNumber',
            name: 'dragName',
            event: 'dragEventName',
            qr: 'dragQR',
        }[key];
        const element = document.getElementById(elementId);
        element.style.left = `${(x / 800) * 100}%`;
        element.style.top = `${(y / 565) * 100}%`;
        sessionStorage.setItem(`pos_${key}_x`, String(x));
        sessionStorage.setItem(`pos_${key}_y`, String(y));
        if (key === 'name') positionNameDetails(x, y);
    }

    document.getElementById('resetDraftPositions').addEventListener('click', function() {
        Object.entries(defaultPositions).forEach(([key, position]) => {
            applyPosition(key, position.x, position.y);
        });
    });

    function positionNameDetails(x, y) {
        const details = [
            ['previewRole', 60],
            ['previewInstitution', 82],
            ['previewIssueDate', 104],
        ];

        details.forEach(([id, offsetY]) => {
            const detail = document.getElementById(id);
            detail.style.left = `${(x / 800) * 100}%`;
            detail.style.top = `${((y + offsetY) / 565) * 100}%`;
        });
    }

    elements.forEach(([elementId, key]) => {
        const element = document.getElementById(elementId);
        const x = Number(sessionStorage.getItem(`pos_${key}_x`) ?? defaultPositions[key].x);
        const y = Number(sessionStorage.getItem(`pos_${key}_y`) ?? defaultPositions[key].y);
        applyPosition(key, x, y);
        makeElementDraggable(element, key);
    });

    function makeElementDraggable(element, key) {
        let startX = 0;
        let startY = 0;
        let originLeft = 0;
        let originTop = 0;

        element.addEventListener('pointerdown', function(event) {
            event.preventDefault();
            element.setPointerCapture(event.pointerId);
            startX = event.clientX;
            startY = event.clientY;
            originLeft = element.offsetLeft;
            originTop = element.offsetTop;
            element.style.zIndex = '20';
        });

        element.addEventListener('pointermove', function(event) {
            if (!element.hasPointerCapture(event.pointerId)) return;

            const left = Math.max(0, Math.min(
                originLeft + event.clientX - startX,
                canvas.clientWidth - element.offsetWidth,
            ));
            const top = Math.max(0, Math.min(
                originTop + event.clientY - startY,
                canvas.clientHeight - element.offsetHeight,
            ));

            element.style.left = `${left}px`;
            element.style.top = `${top}px`;
            sessionStorage.setItem(`pos_${key}_x`, String(Math.round(left * 800 / canvas.clientWidth)));
            sessionStorage.setItem(`pos_${key}_y`, String(Math.round(top * 565 / canvas.clientHeight)));
            if (key === 'name') {
                positionNameDetails(left * 800 / canvas.clientWidth, top * 565 / canvas.clientHeight);
            }
        });

        element.addEventListener('pointerup', function(event) {
            if (element.hasPointerCapture(event.pointerId)) element.releasePointerCapture(event.pointerId);
            element.style.zIndex = '10';
        });
    }
});

function savePositionsAndBack() {
    const positionKeys = ['number_x', 'number_y', 'name_x', 'name_y', 'event_x', 'event_y', 'qr_x', 'qr_y'];
    positionKeys.forEach(key => {
        if (sessionStorage.getItem(`pos_${key}`) === null) {
            const defaults = {
                number_x: 200,
                number_y: 40,
                name_x: 250,
                name_y: 220,
                event_x: 250,
                event_y: 258,
                qr_x: 680,
                qr_y: 440,
            };
            sessionStorage.setItem(`pos_${key}`, String(defaults[key]));
        }
    });

    window.location.href = "{{ route('admin.certificate.create') }}";
}
</script>
@endsection
