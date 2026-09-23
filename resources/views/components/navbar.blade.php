<nav class="navbar bg-white shadow-sm px-4">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">☰</button>
            <a class="navbar-brand fw-bold text-primary" href="{{ route('landing') }}">SI-PITA</a>
        </div>
        <div>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">Dashboard Admin</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none text-dark fw-semibold">Login</a>
            @endauth
        </div>
    </div>
</nav>
