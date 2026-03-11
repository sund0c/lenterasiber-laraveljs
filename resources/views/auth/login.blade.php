@extends('auth.layout')
@section('title', 'Login')

@section('content')
<h2 class="auth-title">Masuk ke Panel Admin</h2>
<p class="auth-sub">Sistem Manajemen Konten Lentera Siber</p>

@if ($errors->any())
  <div class="alert alert-error">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    {{ $errors->first() }}
  </div>
@endif

<form method="POST" action="{{ route('auth.login.post') }}" autocomplete="off">
  @csrf
  <div class="form-group">
    <label for="username">Username atau Email</label>
    <input
      type="text"
      id="username"
      name="username"
      value="{{ old('username') }}"
      class="form-input @error('username') is-error @enderror"
      autocomplete="username"
      autofocus
      required
    >
  </div>

  <div class="form-group">
    <label for="password">Password</label>
    <div class="input-wrap">
      <input
        type="password"
        id="password"
        name="password"
        class="form-input"
        autocomplete="current-password"
        required
      >
      <button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Toggle password visibility">
        <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
        </svg>
      </button>
    </div>
  </div>

  <button type="submit" class="btn-primary">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
    </svg>
    Masuk
  </button>
</form>

<script>
function togglePw() {
  var pw = document.getElementById('password');
  pw.type = pw.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
