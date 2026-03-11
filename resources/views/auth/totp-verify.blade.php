@extends('auth.layout')
@section('title', 'Verifikasi 2FA')

@section('content')
    <h2 class="auth-title">Verifikasi Dua Faktor</h2>
    <p class="auth-sub">Masukkan kode 6 digit dari aplikasi autentikator Anda.</p>

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('auth.2fa.verify.post') }}">
        @csrf
        <div class="form-group">
            <label>Kode Autentikator</label>
            <input type="text" name="totp_code" class="form-input" maxlength="6" inputmode="numeric" placeholder="000000"
                autofocus autocomplete="one-time-code"
                style="font-size:1.5rem;letter-spacing:8px;text-align:center;font-family:monospace">
        </div>
        <button type="submit" class="btn-primary">Verifikasi</button>
    </form>

    <div class="auth-alt" style="margin-top:1.5rem">
        <details>
            <summary style="cursor:pointer;color:var(--accent);font-size:0.82rem">Tidak punya akses ke aplikasi? Gunakan
                kode backup</summary>
            <form method="POST" action="{{ route('auth.2fa.verify.post') }}" style="margin-top:0.8rem">
                @csrf
                <input type="text" name="totp_code" class="form-input" placeholder="XXXX-XXXX" autocomplete="off"
                    style="text-transform:uppercase;letter-spacing:2px">
                <button type="submit" class="btn-secondary" style="margin-top:0.5rem;width:100%">Gunakan Kode
                    Backup</button>
            </form>
        </details>
    </div>

    <div style="text-align:center;margin-top:1rem">
        <a href="{{ route('auth.logout') }}" style="font-size:0.78rem;color:var(--muted)">← Logout</a>
    </div>
@endsection
