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
            <p style="font-size:0.72rem;color:var(--muted);text-align:center;margin-bottom:4px">Atau masukkan kode manual:
            </p>
            <div class="secret-box" id="secretBox" onclick="copySecret()" title="Klik untuk salin">{{ $secret }}</div>
            <p class="secret-hint">Klik untuk menyalin · Issuer:
                <strong>{{ config('totp.issuer', 'Lentera Siber') }}</strong>
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('auth.2fa.setup.post') }}">
        @csrf
        <div class="form-group">
            <label>Kode 6 Digit dari Aplikasi</label>
            <input type="text" name="totp_code" class="form-input" maxlength="6" inputmode="numeric"
                placeholder="000000" autofocus
                style="font-size:1.5rem;letter-spacing:8px;text-align:center;font-family:monospace">
            @error('totp_code')
                <p style="color:var(--red);font-size:0.78rem;margin-top:4px">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Aktifkan 2FA</button>

    </form>

    <script>
        function copySecret() {
            navigator.clipboard && navigator.clipboard.writeText('{{ $secret }}').then(function() {
                var b = document.getElementById('secretBox');
                b.style.background = '#d1fae5';
                setTimeout(function() {
                    b.style.background = '';
                }, 1500);
            });
        }

        var cells = document.querySelectorAll('.otp-cell');
        var hidden = document.getElementById('totp_hidden');
        var btn = document.getElementById('otpSubmit');

        function sync() {
            var val = '';
            cells.forEach(function(c) {
                val += c.value;
            });
            hidden.value = val;
            btn.disabled = val.length < 6;
        }

        cells.forEach(function(cell, i) {
            cell.addEventListener('input', function() {
                // Ambil hanya satu digit
                cell.value = cell.value.replace(/[^0-9]/g, '').slice(-1);
                sync();
                if (cell.value && i < 5) cells[i + 1].focus();
            });
            cell.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !cell.value && i > 0) {
                    cells[i - 1].focus();
                }
            });
            // Trigger sync juga saat change (untuk autofill)
            cell.addEventListener('change', sync);
        });

        cells[0].addEventListener('paste', function(e) {
            e.preventDefault();
            var p = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            p.slice(0, 6).split('').forEach(function(d, i) {
                if (cells[i]) cells[i].value = d;
            });
            sync();
            var next = Math.min(p.length, 5);
            cells[next].focus();
        });
    </script>
@endsection
