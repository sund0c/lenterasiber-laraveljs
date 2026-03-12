@extends('auth.layout')
@section('title', 'Aktivasi 2FA')

@section('content')
<h2 class="auth-title">Aktivasi Autentikasi Dua Faktor</h2>
<p class="auth-sub">Scan QR code dengan aplikasi autentikator, lalu masukkan kode 6 digit untuk konfirmasi.</p>

@if ($errors->any())
  <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<div class="setup-warning">
  Pastikan waktu perangkat sinkron. Gunakan Google Authenticator, Authy, atau Microsoft Authenticator.
</div>

<div class="qr-section">
  <div class="qr-box">
    {!! $qrSvg !!}
  </div>
  <div style="margin-top:0.8rem">
    <p style="font-size:0.72rem;color:var(--muted);text-align:center;margin-bottom:4px">Atau masukkan kode manual:</p>
    <div class="secret-box" id="secretBox" title="Klik untuk salin">{{ $secret }}</div>
    <p class="secret-hint">Klik untuk menyalin · Issuer: <strong>{{ config('totp.issuer', 'Lentera Siber') }}</strong></p>
  </div>
</div>

<form method="POST" action="{{ route('auth.2fa.setup.post') }}">
  @csrf
  <div class="form-group">
    <label>Kode 6 Digit dari Aplikasi</label>
    <input
      type="text"
      name="totp_code"
      class="form-input"
      maxlength="6"
      inputmode="numeric"
      placeholder="000000"
      autofocus
      autocomplete="one-time-code"
      style="font-size:1.5rem;letter-spacing:8px;text-align:center;font-family:monospace"
    >
    @error('totp_code')
      <p style="color:var(--red);font-size:0.78rem;margin-top:4px">{{ $message }}</p>
    @enderror
  </div>
  <button type="submit" class="btn-primary">Aktifkan 2FA</button>
</form>
@endsection
