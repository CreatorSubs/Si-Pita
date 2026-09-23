<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Si-Pita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #eef2ff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-landing {
            background-color: #ffffff;
            border-bottom: 2px solid #000000;
        }
        .card-search {
            background-color: #ffffff;
            border: 2px solid #000000;
            border-radius: 28px;
            box-shadow: 6px 6px 0px #000000;
            max-width: 650px;
            width: 100%;
        }
        .form-control-custom {
            border: 2px solid #000000;
            border-radius: 50px;
            padding: 10px 20px;
        }
        .btn-search {
            background-color: #2563eb;
            color: #ffffff;
            border: 2px solid #000000;
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: bold;
            box-shadow: 3px 3px 0px #000000;
            transition: all 0.2s ease;
        }
        .btn-search:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0px #000000;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar Publik -->
    <nav class="navbar navbar-landing px-4 py-3">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span class="fs-3 fw-extrabold text-primary fw-bold">Si-Pita</span>
            <a href="{{ route('login') }}" class="btn btn-outline-dark rounded-pill fw-bold border-2">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In / Login
            </a>
        </div>
    </nav>

    <!-- Content Utama / Form Pencarian -->
    <div class="container flex-grow-1 d-flex align-items-center justify-content-center py-5">
        <div class="card-search p-4 p-md-5 text-center">
            <h2 class="fw-bold text-dark mb-4">SELAMAT DATANG DI WEBSITE<br><span class="text-primary">SI-PITA</span></h2>
            <p class="text-muted mb-4">Cek dan verifikasi keabsahan sertifikat Anda secara cepat dan mudah.</p>

            <form action="{{ route('certificate.search') }}" method="GET">
                <div class="row g-3 mb-4">
                    <div class="col-md-6 text-start">
                        <label class="form-label fw-bold text-dark">Masukkan Nama</label>
                        <input type="text" name="name" class="form-control form-control-custom" placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="col-md-6 text-start">
                        <label class="form-label fw-bold text-dark">Masukkan Nomor Identitas</label>
                        <input type="text" name="identity_number" class="form-control form-control-custom" placeholder="NIP / NIK / No. KTP">
                    </div>
                </div>

                <div>
                    <button type="submit" class="btn btn-search w-100">
                        <i class="bi bi-search me-2"></i> Cari Sertifikat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 bg-white border-top border-dark mt-auto">
        <small class="fw-bold text-dark">&copy; {{ date('Y') }} Si-Pita. All rights reserved.</small>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
