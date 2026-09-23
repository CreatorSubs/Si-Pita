@extends('layouts.main')

@section('title', 'Data Sertifikat')

@section('content')
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0">📜 Daftar Data Sertifikat Terbit</h4>
        <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary rounded-pill border-2 border-dark fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Buat Sertifikat Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-2 border-dark rounded-3 fw-bold mb-3">
            {{ session('success') }}
        </div>
    @endif

    <!-- FILTER & PENCARIAN -->
    <div class="card border-2 border-dark rounded-4 shadow-sm p-3 mb-4 bg-light">
        <form action="{{ route('admin.certificate.show_all') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control border-dark rounded-pill" placeholder="Cari Nama Penerima..." value="{{ request('name') }}">
            </div>
            <div class="col-md-5">
                <input type="text" name="identity_number" class="form-control border-dark rounded-pill" placeholder="Cari NIK / NIP / NIM..." value="{{ request('identity_number') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold border border-dark">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DATA SERTIFIKAT -->
    <div class="card border-2 border-dark rounded-4 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light border-bottom border-dark">
                    <tr>
                        <th>No</th>
                        <th>No. Sertifikat</th>
                        <th>Nama Penerima</th>
                        <th>NIK / NIP</th>
                        <th>Instansi</th>
                        <th>Nama Kegiatan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $index => $cert)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><span class="badge bg-secondary">{{ $cert->certificate_number ?? '-' }}</span></td>
                            <td class="fw-bold">{{ $cert->name ?? '-' }}</td>
                            <td>{{ $cert->identity_number ?? '-' }}</td>
                            <td>{{ $cert->agency ?? '-' }}</td>
                            <td>{{ $cert->event_name ?? '-' }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.certificate.destroy', $cert->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus sertifikat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Belum ada data sertifikat yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
