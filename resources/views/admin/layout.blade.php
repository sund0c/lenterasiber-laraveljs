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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <h1>LENTERA SIBER</h1>
            </div>

            @php
                $authUser = \App\Models\AdminUser::find(session('auth_user_id'));
            @endphp

            @if ($authUser?->isAdmin())
                @include('admin.partials.nav-admin')
            @else
                @include('admin.partials.nav-staf')
            @endif


            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <strong>{{ $currentUser->full_name }}</strong>
                    {{ $currentUser->email }}
                </div>
                <a href="{{ route('auth.logout') }}" class="nav-item"
                    style="padding:0.4rem 0;color:rgba(255,255,255,0.5);font-size:0.78rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
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
                @if (session('success'))
                    <div class="alert alert-success" style="margin-bottom:1rem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-error" style="margin-bottom:1rem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
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
    <div id="deleteModal"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center">
        <div
            style="background:white;border-radius:12px;padding:2rem;max-width:400px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
            <h3 style="margin-bottom:0.5rem;font-size:1rem">Konfirmasi Hapus</h3>
            <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1.5rem">Data yang dihapus tidak dapat
                dikembalikan. Yakin ingin menghapus?</p>
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
