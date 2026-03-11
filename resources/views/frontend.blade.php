<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lentera Siber — Literasi Keamanan Siber ASN Bali</title>
  <meta name="description" content="Platform literasi keamanan siber untuk ASN Pemerintah Provinsi Bali.">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  <style>
    body { background: linear-gradient(135deg, #0b1e45, #1a1060); min-height: 100vh;
           display: flex; align-items: center; justify-content: center; }
    .placeholder { text-align: center; color: rgba(255,255,255,0.8); padding: 2rem; }
    .placeholder h1 { font-size: 2rem; margin-bottom: 1rem; letter-spacing: 4px; }
    .placeholder p { font-size: 0.9rem; opacity: 0.6; }
  </style>
</head>
<body>
  <div class="placeholder">
    <h1>LENTERA SIBER</h1>
    <p>Frontend akan dipasang di sini.</p>
    <p style="margin-top:0.5rem;font-size:0.75rem">
      Admin: <a href="{{ env('ADMIN_LOGIN_PATH','/portal-internal-x83fj9/login') }}" style="color:#5db8ff">Login Panel</a>
    </p>
  </div>
</body>
</html>
