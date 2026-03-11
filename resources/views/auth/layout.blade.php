<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Admin') — Lentera Siber</title>
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="auth-body">
<div class="auth-wrap">
  <div class="auth-brand">
    <div class="auth-icon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      </svg>
    </div>
    <h1>LENTERA SIBER</h1>
    <p>Panel Administrasi</p>
  </div>

  <div class="auth-card">
    @yield('content')
  </div>
</div>
</body>
</html>
