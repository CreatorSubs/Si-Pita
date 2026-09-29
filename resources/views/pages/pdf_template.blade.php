<!DOCTYPE html>
<html>
<head>
    <style>
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        html, body { width: 1122.52px; height: 793.7px; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #172554; }
        .certificate-sheet {
            position: relative;
            width: 1122.52px;
            height: 793.7px;
            overflow: hidden;
            background-color: #fff;
            @if($templateDataUri) background-image: url('{{ $templateDataUri }}'); background-size: 100% 100%; background-repeat: no-repeat; @endif
        }
        .absolute-element { position: absolute; line-height: 1.25; }
        .certificate-number { font-size: 19.6px; font-weight: 700; }
        .certificate-name { max-width: 700px; font-size: 25.3px; font-weight: 700; }
        .certificate-detail { max-width: 650px; font-size: 18.2px; }
        .certificate-qr { width: 105.2px; height: 105.2px; }
    </style>
</head>
<body>
    <main class="certificate-sheet">
        <div class="absolute-element certificate-number" style="left: {{ $certificate->pos_number_x * 1.40315 }}px; top: {{ $certificate->pos_number_y * 1.40315 }}px;">No: {{ $certificate->certificate_number }}</div>
        <div class="absolute-element certificate-name" style="left: {{ $certificate->pos_name_x * 1.40315 }}px; top: {{ $certificate->pos_name_y * 1.40315 }}px;">{{ $certificate->recipient_name }}</div>
        <div class="absolute-element certificate-detail" style="left: {{ $certificate->pos_event_x * 1.40315 }}px; top: {{ $certificate->pos_event_y * 1.40315 }}px;">Kegiatan: {{ $certificate->event_name }}</div>
        <div class="absolute-element certificate-detail" style="left: {{ $certificate->pos_name_x * 1.40315 }}px; top: {{ ($certificate->pos_name_y + 60) * 1.40315 }}px;">Peran: {{ $certificate->role ?: 'Peserta' }}</div>
        <div class="absolute-element certificate-detail" style="left: {{ $certificate->pos_name_x * 1.40315 }}px; top: {{ ($certificate->pos_name_y + 82) * 1.40315 }}px;">Instansi: {{ $certificate->institution ?: '-' }}</div>
        <div class="absolute-element certificate-detail" style="left: {{ $certificate->pos_name_x * 1.40315 }}px; top: {{ ($certificate->pos_name_y + 104) * 1.40315 }}px;">Tanggal terbit: {{ \Carbon\Carbon::parse($certificate->issue_date)->format('d F Y') }}</div>
        <div class="absolute-element" style="left: {{ $certificate->pos_qr_x * 1.40315 }}px; top: {{ $certificate->pos_qr_y * 1.40315 }}px;">
            <img class="certificate-qr" src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR verifikasi sertifikat">
        </div>
    </main>
</body>
</html>
