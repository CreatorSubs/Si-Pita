@extends('layouts.main')

@section('title', 'Cek Sertifikat')

@section('content')
<div class="container d-flex justify-content-center py-3">
    <!-- Card Container Utama -->
    <div class="card p-4 p-md-5 shadow-sm w-100" style="max-width: 1000px; background-color: #eef2ff; border: 2px solid #000000; border-radius: 28px;">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark m-0">Cek Sertifikat</h5>
            <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary rounded-pill px-3 py-1 btn-sm" style="background-color: #2563eb; border: none;">
                + Buat Sertifikat Baru
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form Fitur Search -->
        <form action="{{ route('admin.certificate.show_all') }}" method="GET" class="mb-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-dark border-end-0 rounded-start-pill ps-3">
                    🔍
                </span>
                <input type="text" name="search" class="form-control border-dark border-start-0 rounded-end-pill py-2" 
                       placeholder="Cari berdasarkan nama, NIP/NIK, atau kegiatan..." value="{{ request('search') }}">
            </div>
        </form>

        <!-- Tabel Data Sertifikat dengan Border Bold -->
        <div class="table-responsive bg-white rounded-3 border border-2 border-dark">
            <table class="table table-hover align-middle m-0 text-center" style="border-collapse: collapse;">
                <thead class="table-light border-bottom border-2 border-dark" style="font-size: 14px;">
                    <tr>
                        <th class="border-end border-dark py-2" style="width: 50px;">No</th>
                        <th class="border-end border-dark py-2">Nama</th>
                        <th class="border-end border-dark py-2">NIP/NIK</th>
                        <th class="border-end border-dark py-2">Instansi / Unit Kerja</th>
                        <th class="border-end border-dark py-2">Nama Kegiatan</th>
                        <th class="border-end border-dark py-2">Peran</th>
                        <th class="py-2" style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    @forelse ($certificates as $index => $cert)
                        <tr class="border-bottom border-dark">
                            <td class="border-end border-dark">{{ $certificates->firstItem() + $index }}</td>
                            <td class="border-end border-dark text-start fw-semibold px-2">{{ $cert->recipient_name }}</td>
                            <td class="border-end border-dark">{{ $cert->recipient_identity }}</td>
                            <td class="border-end border-dark">{{ $cert->institution ?? '-' }}</td>
                            <td class="border-end border-dark text-start px-2">{{ $cert->event_name }}</td>
                            <td class="border-end border-dark"><span class="badge bg-secondary">{{ $cert->role }}</span></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <!-- Tombol Download -->
                                    <a href="{{ route('admin.certificate.download', $cert->id) }}" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-2" title="Download Sertifikat">
                                        📥 Download
                                    </a>

                                    <!-- Tombol Delete -->
                                    <form action="{{ route('admin.certificate.destroy', $cert->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-2" title="Hapus Sertifikat">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada data sertifikat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end mt-3">
            {{ $certificates->links() }}
        </div>

    </div>
</div>
@endsection
