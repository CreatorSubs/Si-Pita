<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian Sertifikat | Si-Pita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: #eef2ff; color: #1d2d50; }
        .public-header { background: #fff; border-bottom: 1px solid #d9e0ef; }
        .results-wrap { width: min(100% - 32px, 1000px); margin: 40px auto; }
        .results-panel { border: 1px solid #e1e6f0; border-radius: 12px; background: #fff; }
        .certificate-number { color: #244fa9; font-weight: 700; }
    </style>
    <link rel="stylesheet" href="{{ asset('si-pita-theme.css') }}">
</head>
<body>
    <header class="public-header">
        <nav class="container d-flex align-items-center justify-content-between py-3" aria-label="Navigasi publik">
            <a class="si-pita-brand" href="{{ route('landing') }}">SI - PITA</a>
            <a href="{{ route('login') }}" class="link-primary text-decoration-none">Login Admin</a>
        </nav>
    </header>

    <main class="results-wrap">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h4 fw-bold mb-1">Hasil Pencarian Sertifikat</h1>
                <p class="text-secondary mb-0">Informasi untuk verifikasi sertifikat.</p>
            </div>
            <a href="{{ route('landing') }}" class="btn btn-outline-primary">Kembali</a>
        </div>

        <section class="results-panel p-3 p-md-4" aria-label="Daftar hasil sertifikat">
            @if (! $hasSearch)
                <p class="text-secondary text-center my-4">Masukkan nama atau nomor identitas dari halaman utama untuk mencari sertifikat.</p>
            @elseif ($certificates->isEmpty())
                <p class="text-secondary text-center my-4">Data sertifikat tidak ditemukan.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Nomor Sertifikat</th>
                                <th scope="col">Nama Penerima</th>
                                <th scope="col">Kegiatan</th>
                                <th scope="col">Tanggal Terbit</th>
                                <th scope="col"><span class="visually-hidden">Unduh</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($certificates as $certificate)
                                <tr>
                                    <td class="certificate-number">{{ $certificate->certificate_number }}</td>
                                    <td>{{ $certificate->recipient_name }}</td>
                                    <td>{{ $certificate->event_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($certificate->issue_date)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a class="btn btn-primary btn-sm" href="{{ route('public.certificate.download', $certificate->qr_token) }}">
                                            Unduh PDF
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($certificates->hasPages())
                    <div class="pt-3">{{ $certificates->links() }}</div>
                @endif
            @endif
        </section>
    </main>
</body>
</html>