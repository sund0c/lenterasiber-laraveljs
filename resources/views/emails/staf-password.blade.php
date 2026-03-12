<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Staf Lentera Siber</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 2rem;
        }

        .wrap {
            max-width: 520px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .logo {
            font-size: 1rem;
            font-weight: 700;
            color: #0b1e45;
            letter-spacing: 2px;
            margin-bottom: 1.5rem;
        }

        h2 {
            font-size: 1.1rem;
            color: #0b1e45;
            margin: 0 0 1rem;
        }

        p {
            color: #4b5563;
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0 0 1rem;
        }

        .cred-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.2rem;
            margin: 1.2rem 0;
        }

        .cred-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.6rem;
            font-size: 0.85rem;
        }

        .cred-row:last-child {
            margin-bottom: 0;
        }

        .cred-label {
            color: #6b7280;
        }

        .cred-value {
            font-family: monospace;
            font-weight: 600;
            color: #0b1e45;
            background: #e8f4fd;
            padding: 3px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
        }

        .btn {
            display: inline-block;
            background: #1d4ed8;
            color: #fff !important;
            text-decoration: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.88rem;
            margin: 1rem 0;
        }

        .warning {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 8px;
            padding: 0.8rem 1rem;
            font-size: 0.82rem;
            color: #92400e;
            margin: 1rem 0;
        }

        .footer {
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 1.5rem;
        }

        .divider {
            border: none;
            border-top: 1px solid #e5e7eb;
            margin: 1.2rem 0;
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="logo">LENTERA SIBER</div>

            <h2>Halo, {{ $fullName }}!</h2>
            <p>Akun staf Anda untuk Panel Admin Lentera Siber telah dibuat. Gunakan kredensial di bawah ini untuk login
                pertama kali.</p>

            <div class="cred-box">
                <div class="cred-row">
                    <span class="cred-label">Username</span>
                    <span class="cred-value">{{ $username }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password Awal</span>
                    <span class="cred-value">{{ $plainPassword }}</span>
                </div>
            </div>

            <div class="warning">
                ⚠️ <strong>Penting:</strong> Saat login pertama, Anda <strong>wajib mengganti password</strong> dan
                mengaktifkan <strong>Autentikasi Dua Faktor (2FA)</strong>. Password ini hanya berlaku untuk login
                pertama.
            </div>

            <a href="{{ $loginUrl }}" class="btn">Login ke Panel Admin →</a>

            <hr class="divider">

            <p style="font-size:0.8rem;color:#6b7280">
                Jika Anda tidak merasa mendaftar atau tidak mengenal email ini, abaikan pesan ini dan hubungi Bidang
                Persandian Dinas Kominfos Pemprov Bali.
            </p>
        </div>
        <div class="footer">
            Lentera Siber — Dinas Kominfos Pemprov Bali<br>
            Email ini dikirim otomatis, jangan dibalas.
        </div>
    </div>
</body>

</html>
