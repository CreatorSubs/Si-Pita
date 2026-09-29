@php
    $currentUserEmail = auth()->user() ? auth()->user()->email : session('user_email', '');
    $isOwner = strtolower($currentUserEmail) === 'admin@diskominfo.go.id';
@endphp

<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel" style="width: 290px;">
    <div class="offcanvas-header border-bottom border-dark">
        <div>
            <h5 class="offcanvas-title si-pita-brand mb-0" id="sidebarOffcanvasLabel">SI - PITA</h5>
            @if($isOwner)
                <small class="badge bg-danger text-white rounded-pill mt-1">👑 Mode Owner / Super Admin</small>
            @else
                <small class="badge bg-secondary text-white rounded-pill mt-1">Admin Biasa</small>
            @endif
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    
    <div class="offcanvas-body d-flex flex-column justify-content-between">
        <ul class="nav nav-pills flex-column gap-2">
            <!-- MENU ADMIN BIASA & OWNER -->
            <li class="nav-item">
                <a href="{{ route('admin.certificate.create') }}" class="nav-link {{ request()->routeIs('admin.certificate.create') ? 'active bg-primary text-white' : 'text-dark fw-semibold' }}">
                    <i class="bi bi-plus-square me-2"></i> Buat Sertifikat
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.certificate.show_all') }}" class="nav-link {{ request()->routeIs('admin.certificate.show_all') ? 'active bg-primary text-white' : 'text-dark fw-semibold' }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Data Sertifikat
                </a>
            </li>

            <!-- MENU KHUSUS OWNER (Hanya Muncul Jika Login admin@diskominfo.go.id) -->
            @if($isOwner)
                <li class="nav-item mt-2">
                    <small class="text-uppercase text-muted fw-bold px-2">Menu Khusus Owner</small>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.certificate.history') }}" class="nav-link {{ request()->routeIs('admin.certificate.history') ? 'active bg-primary text-white' : 'text-dark fw-semibold' }}">
                        <i class="bi bi-clock-history me-2"></i> History Sertifikat
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.user.index') }}" class="nav-link {{ request()->routeIs('admin.user.index') || request()->routeIs('admin.user.create') ? 'active bg-primary text-white' : 'text-dark fw-semibold' }}">
                        <i class="bi bi-people me-2"></i> Kelola Admin & Akses
                    </a>
                </li>
            @endif
        </ul>
        
        <div class="pt-3 border-top border-dark">
            <div class="small text-muted mb-2 px-1">Login sebagai: <br><strong class="text-dark">{{ $currentUserEmail }}</strong></div>
            <button type="button" class="btn btn-outline-danger w-100 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-body px-4 py-4 text-center">
                <h2 class="fs-5 fw-semibold mb-4" id="logoutConfirmTitle">Logout?</h2>
                <form id="logoutConfirmForm" action="{{ route('logout') }}" method="POST">
                    @csrf
                </form>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-primary rounded-2 px-4" data-bs-dismiss="modal">Tidak</button>
                    <button type="submit" form="logoutConfirmForm" class="btn btn-primary rounded-2 px-4">Ya</button>
                </div>
            </div>
        </div>
    </div>
</div>
