<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') — Lentera Siber</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@stack('head')
</head>
<body>
<div class="admin-layout">

  {{-- Sidebar --}}
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="s-icon">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <h1>LENTERA SIBER</h1>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">Dashboard</div>
      <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>

      <div class="nav-section" style="margin-top:0.5rem">Konten</div>
      <a href="{{ route('admin.kabar.index') }}" class="nav-item {{ request()->routeIs('admin.kabar.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Kabar Lentera
      </a>
      <a href="{{ route('admin.layanan.index') }}" class="nav-item {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        Layanan
      </a>

      <a href="{{ route('admin.komik.index') }}" class="nav-item {{ request()->routeIs('admin.komik.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        Komik
      </a>
      <a href="{{ route('admin.podcast.index') }}" class="nav-item {{ request()->routeIs('admin.podcast.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/><path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/></svg>
        Podcast
      </a>

      <div class="nav-section" style="margin-top:0.5rem">Sistem</div>
      <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        Pengaturan
      </a>
      <a href="{{ route('admin.audit.index') }}" class="nav-item {{ request()->routeIs('admin.audit.*') ? 'active' : '' }}">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        Audit Log
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-user">
        <strong>{{ $currentUser->full_name }}</strong>
        {{ $currentUser->email }}
      </div>
      <a href="{{ route('auth.logout') }}" class="nav-item" style="padding:0.4rem 0;color:rgba(255,255,255,0.5);font-size:0.78rem;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </div>
  </aside>

  {{-- Main --}}
  <div class="main-content">
    <div class="topbar">
      <div class="page-title">
        <h2>@yield('page-title', 'Dashboard')</h2>
        <p>@yield('page-sub', '')</p>
      </div>
      <div>@yield('topbar-actions')</div>
    </div>

    <div class="content-area">
      @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:1rem">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </div>
  </div>

</div>
@stack('scripts')
<script src="{{ asset('js/admin.js') }}" nonce="{{ $cspNonce }}"></script>
{{-- Modal konfirmasi hapus --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
  <div style="background:white;border-radius:12px;padding:2rem;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
    <h3 style="margin-bottom:0.5rem;font-size:1rem">Konfirmasi Hapus</h3>
    <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1.5rem">Data yang dihapus tidak dapat dikembalikan. Yakin ingin menghapus?</p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button type="button" id="modalCancel" class="btn-secondary">Batal</button>
      <form id="modalForm" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">Ya, Hapus</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>
