{{-- resources/views/admin/partials/nav-admin.blade.php --}}
@php $route = request()->route()?->getName(); @endphp

<a href="{{ route('admin.dashboard') }}"
    class="nav-item {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
    <span>🏠</span> Dashboard
</a>

<div class="nav-group-label">KONTEN</div>

<a href="{{ route('admin.kabar.index') }}" class="nav-item {{ str_starts_with($route, 'admin.kabar') ? 'active' : '' }}">
    <span>📰</span> Kabar Lentera
</a>

<a href="{{ route('admin.podcast.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.podcast') ? 'active' : '' }}">
    <span>🎙</span> Podcast
</a>

<a href="{{ route('admin.komik.index') }}" class="nav-item {{ str_starts_with($route, 'admin.komik') ? 'active' : '' }}">
    <span>📖</span> Komik
</a>

<a href="{{ route('admin.layanan.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.layanan') ? 'active' : '' }}">
    <span>🛠</span> Layanan
</a>

<a href="{{ route('admin.workshop.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.workshop') ? 'active' : '' }}">
    <span>🎓</span> Workshop
</a>

<div class="nav-group-label">SISTEM</div>

<a href="{{ route('admin.users.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.users') ? 'active' : '' }}">
    <span>👥</span> Pengguna
</a>

<a href="{{ route('admin.audit.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.audit') ? 'active' : '' }}">
    <span>🔍</span> Audit Log
</a>

<a href="{{ route('admin.settings.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.settings') ? 'active' : '' }}">
    <span>⚙️</span> Pengaturan
</a>
<a href="{{ route('admin.password.change') }}"
    class="nav-item {{ str_starts_with($route, 'admin.password') ? 'active' : '' }}">
    <span>🔑</span> Ganti Password
</a>
