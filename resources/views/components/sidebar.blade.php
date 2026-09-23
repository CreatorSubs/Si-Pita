<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" style="width: 280px; background-color: #ffffff;">
    <div class="offcanvas-header border-bottom p-3">
        <h5 class="offcanvas-title fw-bold text-primary">Menu Admin</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
        <div>
            <!-- Info User Login -->
            <div class="text-center mb-4 p-3 rounded-3" style="background-color: #f0f4ff;">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold" style="width: 50px; height: 50px; font-size: 20px;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <h6 class="fw-bold text-dark m-0">{{ Auth::user()->name ?? 'Admin SI-PITA' }}</h6>
                <small class="text-muted">{{ Auth::user()->email ?? 'admin@diskominfo.go.id' }}</small>
            </div>

            <!-- Navigasi 3 Fitur Utama Admin -->
            <div class="d-grid gap-2">
                <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary-custom text-white text-start d-flex align-items-center gap-2">
                    ➕ <span>Buat Sertifikat</span>
                </a>
                <a href="{{ route('admin.certificate.index') }}" class="btn btn-primary-custom text-white text-start d-flex align-items-center gap-2">
                    📋 <span>Cek Sertifikat</span>
                </a>
            </div>
        </div>

        <!-- Tombol Logout -->
        <div class="pt-3 border-top">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-semibold d-flex align-items-center justify-content-center gap-2">
                    🚪 <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
