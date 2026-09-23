<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" style="width: 280px; background-color: #e0e7ff;">
    <div class="offcanvas-header border-bottom"><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
        <div>
            <div class="text-center mb-4">
                <div class="bg-secondary rounded-circle mx-auto mb-2" style="width: 70px; height: 70px;"></div>
                <h6 class="fw-bold m-0">{{ Auth::user()->name ?? 'Guest User' }}</h6>
            </div>
            <div class="d-grid gap-2">
                @auth
                    <a href="{{ route('admin.certificate.create') }}" class="btn btn-primary-custom text-white text-start">Buat Sertifikat</a>
                    <a href="{{ route('admin.certificate.index') }}" class="btn btn-primary-custom text-white text-start">Cek Sertifikat</a>
                @else
                    <a href="{{ route('landing') }}" class="btn btn-primary-custom text-white text-start">Cari Sertifikat</a>
                @endauth
            </div>
        </div>
        @auth
            <form action="{{ route('logout') }}" method="POST">@csrf <button type="submit" class="btn btn-primary-custom text-white w-100">Logout</button></form>
        @endauth
    </div>
</div>
