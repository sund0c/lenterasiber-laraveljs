@extends('admin.layout')
@section('title', $item ? 'Edit Marquee' : 'Tambah Marquee')
@section('page-title', $item ? 'Edit Marquee' : 'Tambah Marquee')
@section('page-sub', 'Teks berjalan yang tampil di halaman publik')

@section('content')
    <div style="max-width:640px">
        <form method="POST" action="{{ $item ? route('admin.marquee.update', $item->id) : route('admin.marquee.store') }}">
            @csrf
            @if ($item)
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="alert alert-error" style="margin-bottom:1rem">
                    <strong>Mohon periksa kembali isian berikut:</strong>
                    <ul style="margin:6px 0 0;padding-left:1.2rem">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card" style="margin-bottom:1rem">
                <div class="card-header"><strong>Informasi Marquee</strong></div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Teks Marquee <span style="color:var(--red)">*</span></label>
                        <input type="text" name="title" class="form-input @error('title') is-error @enderror"
                            value="{{ old('title', $item->title ?? '') }}"
                            placeholder="Selamat datang di Lentera Siber — Portal Literasi Keamanan Siber ASN Pemprov Bali"
                            maxlength="100" required>
                        <p class="field-hint">Maksimal 100 karakter. Teks ini akan tampil berjalan di halaman publik.</p>
                        @error('title')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row">


                        <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
                            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
                                <input type="hidden" name="status" value="draft">
                                <input type="checkbox" name="status" value="published"
                                    {{ old('status', $item->status ?? '') === 'published' ? 'checked' : '' }}
                                    style="width:16px;height:16px;cursor:pointer">
                                Publikasikan
                            </label>
                        </div>
                    </div>

                    @if (isset($item) && $item->updated_by)
                        <div style="font-size:0.78rem;color:var(--muted);padding-top:0.5rem">
                            Terakhir diubah oleh:
                            <strong>{{ DB::table('admin_users')->where('id', $item->updated_by)->value('username') ?? '—' }}</strong>
                            pada {{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}
                        </div>
                    @endif

                </div>
            </div>

            <div style="display:flex;gap:8px">
                <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
                    {{ $item ? 'Simpan Perubahan' : 'Simpan Marquee' }}
                </button>
                <a href="{{ route('admin.marquee.index') }}" class="btn-secondary">Batal</a>
            </div>

        </form>
    </div>
@endsection
