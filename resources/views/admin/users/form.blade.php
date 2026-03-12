@extends('admin.layout')
@section('title', 'Tambah Staf')
@section('page-title', 'Tambah Akun Staf')
@section('page-sub', 'Password awal akan dikirim otomatis ke email staf')

@section('content')
    <div style="max-width:500px">
        <form method="POST" action="{{ route('admin.users.store') }}">
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

            <div class="card">
                <div class="card-header"><strong>Data Akun Staf</strong></div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nama Lengkap <span style="color:var(--red)">*</span></label>
                        <input type="text" name="full_name" class="form-input @error('full_name') is-error @enderror"
                            value="{{ old('full_name') }}" placeholder="I Nyoman Staf Kominfos">
                    </div>

                    <div class="form-group">
                        <label>Username <span style="color:var(--red)">*</span></label>
                        <input type="text" name="username" class="form-input @error('username') is-error @enderror"
                            value="{{ old('username') }}" placeholder="nyoman.staf" pattern="[a-z0-9_]+"
                            title="Hanya huruf kecil, angka, dan underscore">
                        <p class="field-hint">Hanya huruf kecil, angka, underscore. Contoh: nyoman.staf</p>
                    </div>

                    <div class="form-group">
                        <label>Email <span style="color:var(--red)">*</span></label>
                        <input type="email" name="email" class="form-input @error('email') is-error @enderror"
                            value="{{ old('email') }}" placeholder="staf@baliprov.go.id">
                        <p class="field-hint">Password awal akan dikirim ke email ini.</p>
                    </div>


                </div>
            </div>

            <div style="display:flex;gap:8px;margin-top:1rem">
                <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
                    Buat Akun & Kirim Email
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>

        </form>
    </div>
@endsection
