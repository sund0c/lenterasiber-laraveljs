@extends('admin.layout')
@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')
@section('page-sub', 'Wajib diisi sebelum melanjutkan')

@section('content')
    <div style="max-width:460px">

        @if (session('warning'))
            <div class="alert alert-warning" style="margin-bottom:1rem">{{ session('warning') }}</div>
        @endif

        <div class="card">
            <div class="card-header"><strong>Buat Password Baru</strong></div>
            <div class="card-body">

                <p
                    style="font-size:0.82rem;color:#1e40af;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:0.8rem 1rem;margin:0 0 1.2rem">
                    Password harus minimal <strong>12 karakter</strong> dan mengandung huruf besar, huruf kecil, angka, dan
                    simbol.
                </p>

                <form method="POST" action="{{ route('admin.password.update') }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-error" style="margin-bottom:1rem">
                            <ul style="margin:0;padding-left:1rem">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Password Saat Ini</label>
                        <div style="position:relative">
                            <input type="password" id="current_password" name="current_password"
                                class="form-input @error('current_password') is-error @enderror"
                                placeholder="Password lama / password awal dari email">
                            <button type="button" data-toggle-pw="current_password"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password Baru</label>
                        <div style="position:relative">
                            <input type="password" id="password" name="password"
                                class="form-input @error('password') is-error @enderror"
                                placeholder="Min. 12 karakter, huruf besar+kecil+angka+simbol">
                            <button type="button" data-toggle-pw="password"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <div style="position:relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" placeholder="Ulangi password baru">
                            <button type="button" data-toggle-pw="password_confirmation"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top:0.5rem">
                        Simpan Password Baru
                    </button>

                </form>
            </div>
        </div>
    </div>
@endsection
