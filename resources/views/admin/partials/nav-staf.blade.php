{{-- resources/views/admin/partials/nav-staf.blade.php --}}
@php $route = request()->route()?->getName(); @endphp

<a href="{{ route('admin.dashboard') }}"
  class="nav-item {{ str_starts_with($route, 'admin.dashboard') ? 'active' : '' }}">
  <span>🏠</span> Dashboard
</a>

<div class="nav-group-label">KONTEN</div>

<a href="{{ route('admin.konten.index', 'KABAR') }}"
  class="nav-item {{ str_starts_with($route, 'admin.konten') ? 'active' : '' }}">
  <span>📰</span> Kabar Lentera
</a>

<a href="{{ route('admin.konten.index', 'PODCAST') }}"
  class="nav-item {{ str_starts_with($route, 'admin.konten') ? 'active' : '' }}">
  <span>🎙</span> Podcast
</a>

<a href="{{ route('admin.konten.index', 'KOMIK') }}"
  class="nav-item {{ str_starts_with($route, 'admin.konten') ? 'active' : '' }}">
  <span>📖</span> Komik
</a>

<div class="nav-group-label">AKUN</div>

<a href="{{ route('admin.password.change') }}"
  class="nav-item {{ str_starts_with($route, 'admin.password') ? 'active' : '' }}">
  <span>🔑</span> Ganti Password
</a>
