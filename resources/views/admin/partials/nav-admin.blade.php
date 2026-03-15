{{-- resources/views/admin/partials/nav-admin.blade.php --}}
@php $route = request()->route()?->getName(); @endphp

<a href="{{ route('admin.dashboard') }}"
    class="nav-item {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
    <span>🏠</span> Dashboard
</a>

<div class="nav-group-label">Konten Dinamis</div>

<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ str_starts_with($route, 'admin.konten') && request()->route('label') === 'KABAR' ? 'active' : '' }}">
    <span>📰</span> Kabar Lentera
</a>

<a href="{{ route('admin.konten.index', 'PODCAST') }}"
    class="nav-item {{ str_starts_with($route, 'admin.konten') && request()->route('label') === 'PODCAST' ? 'active' : '' }}">
    <span>🎧</span> Podcast
</a>

<a href="{{ route('admin.konten.index', 'KOMIK') }}"
    class="nav-item {{ str_starts_with($route, 'admin.konten') && request()->route('label') === 'KOMIK' ? 'active' : '' }}">
    <span>💬</span> Komik
</a>

<div class="nav-group-label">Landing Page</div>

<a href="{{ route('admin.marquee.index') }}"
    class="nav-item {{ str_starts_with($route, 'admin.marquee') ? 'active' : '' }}">
    <span>❯</span> Marquee Info
</a>
<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ false ? 'active' : '' }}">
    <span>❯</span> Hero
</a>
<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ false ? 'active' : '' }}">
    <span>❯</span> Program
</a>
<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ false ? 'active' : '' }}">
    <span>❯</span> Tentang
</a>
<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ false ? 'active' : '' }}">
    <span>❯</span> Komik
</a>
<a href="{{ route('admin.konten.index', 'KABAR') }}"
    class="nav-item {{ false ? 'active' : '' }}">
    <span>❯</span> Kontak
</a>

<div class="nav-group-label">Halaman Statis</div>

<a href="{{ route('admin.page.index', 'WORKSHOP') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'WORKSHOP' ? 'active' : '' }}">
    <span>🛠️</span> Workshop
</a>

<a href="{{ route('admin.page.index', 'GTL') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'GTL' ? 'active' : '' }}">
    <span>🎓</span> Goes To Latsar
</a>

<a href="{{ route('admin.page.index', 'ROADSHOW') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'ROADSHOW' ? 'active' : '' }}">
    <span>🚐</span> Roadshow
</a>

<a href="{{ route('admin.page.index', 'JSC') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'JSC' ? 'active' : '' }}">
    <span>🏆</span> JSC
</a>

<a href="{{ route('admin.page.index', 'TENTANG') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'TENTANG' ? 'active' : '' }}">
    <span>ℹ️</span> Tentang
</a>

<a href="{{ route('admin.page.index', 'KOMIK') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'KOMIK' ? 'active' : '' }}">
    <span>📚</span> Komik
</a>

<a href="{{ route('admin.page.index', 'PODCAST') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'PODCAST' ? 'active' : '' }}">
    <span>🎙️</span> Podcast
</a>

<a href="{{ route('admin.page.index', 'KABAR') }}"
    class="nav-item {{ str_starts_with($route, 'admin.page') && request()->route('label') === 'KABAR' ? 'active' : '' }}">
    <span>📋</span> Kabar
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
