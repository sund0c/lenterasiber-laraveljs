@extends('auth.layout')
@section('title', 'Kode Backup 2FA')

@section('content')
    <h2 class="auth-title">Simpan Kode Backup Anda</h2>
    <p class="auth-sub">Kode ini ditampilkan <strong>SATU KALI SAJA</strong>. Simpan di tempat aman sebelum melanjutkan.</p>

    <div class="alert alert-warning">
        Jika kehilangan akses ke aplikasi autentikator, kode ini adalah satu-satunya cara masuk.
    </div>

    <div class="backup-grid">
        @foreach ($codes as $code)
            <div class="backup-code">{{ $code }}</div>
        @endforeach
    </div>

 {{-- Hapus onclick dari button --}}
<button type="button" class="btn-secondary" id="btnCopy" style="width:100%;margin-bottom:1rem">
  Salin Semua Kode
</button>



    <form method="POST" action="{{ route('auth.2fa.backup.post') }}">
        @csrf
        <button type="submit" class="btn-primary">
            Lanjutkan ke Panel Admin →
        </button>
    </form>

@endsection
