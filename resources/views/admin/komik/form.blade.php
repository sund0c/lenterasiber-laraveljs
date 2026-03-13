@extends('admin.layout')
@section('title', $item ? 'Edit Komik' : 'Tambah Komik')
@section('page-title', $item ? 'Edit Komik' : 'Tambah Komik')
@section('page-sub', 'Episode komik literasi keamanan siber')

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div style="max-width:640px">
  <form method="POST"
    action="{{ $item ? route('admin.komik.update', $item->id) : route('admin.komik.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if($item) @method('PUT') @endif

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:1rem">
        <strong>Mohon periksa kembali isian berikut:</strong>
        <ul style="margin:6px 0 0;padding-left:1.2rem">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Informasi Episode</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Judul Komik <span style="color:var(--red)">*</span></label>
          <input type="text" name="title"
            class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Si Pancing — Kisah Phishing di Kotak Masuk"
            maxlength="255" required>
          @error('title')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Nomor Episode <span style="color:var(--red)">*</span></label>
            <input type="text" name="episode_number"
              class="form-input @error('episode_number') is-error @enderror"
              value="{{ old('episode_number', $item->episode_number ?? '') }}"
              placeholder="Episode 1" maxlength="20" required>
            @error('episode_number')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          <div class="form-group">
            <label>Tanggal Publish <span style="color:var(--red)">*</span></label>
            <input type="date" name="published_date"
              class="form-input @error('published_date') is-error @enderror"
              value="{{ old('published_date', isset($item->published_date) ? \Carbon\Carbon::parse($item->published_date)->format('Y-m-d') : date('Y-m-d')) }}"
              required>
            @error('published_date')<p class="field-error">{{ $message }}</p>@enderror
          </div>
        </div>

        {{-- Kategori: hanya admin --}}
        @if($isAdmin)
        <div class="form-group">
          <label>Kategori <span style="color:var(--red)">*</span></label>
          <input type="text" name="category"
            class="form-input @error('category') is-error @enderror"
            value="{{ old('category', $item->category ?? '') }}"
            placeholder="Keamanan Email" maxlength="100" required>
          @error('category')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        @else
          @if(isset($item) && $item->category)
          <div class="form-group">
            <label>Kategori</label>
            <p style="font-size:0.85rem;color:var(--muted);padding:0.5rem 0">
              {{ $item->category }} <span style="font-size:0.75rem">(diatur oleh Admin)</span>
            </p>
          </div>
          @endif
        @endif

        <div class="form-group">
          <label>Deskripsi <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
          <textarea name="description" rows="3"
            class="form-input @error('description') is-error @enderror"
            placeholder="Deskripsi singkat episode ini..."
            maxlength="2000">{{ old('description', $item->description ?? '') }}</textarea>
          @error('description')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
          <label>Link Instagram <span style="color:var(--red)">*</span></label>
          <input type="url" name="instagram_url"
            class="form-input @error('instagram_url') is-error @enderror"
            value="{{ old('instagram_url', $item->instagram_url ?? '') }}"
            placeholder="https://www.instagram.com/p/..." maxlength="500" required>
          <p class="field-hint">Klik kartu komik di frontend akan membuka link ini di tab baru.</p>
          @error('instagram_url')<p class="field-error">{{ $message }}</p>@enderror
        </div>

      </div>
    </div>

    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Cover & Pengaturan</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar Cover <span style="color:var(--red)">*</span></label>
          @if(isset($item) && $item->cover_image)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti gambar saat ini.</p>
            </div>
          @endif
          <input type="file" name="cover_image"
            class="form-input @error('cover_image') is-error @enderror"
            accept="image/jpeg,image/png"
            {{ !isset($item) ? 'required' : '' }}>
          <p class="field-hint">JPG, PNG. Maks 2MB. Rasio 1:1 disarankan.</p>
          @error('cover_image')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group" style="display:flex;align-items:center;padding-top:0.4rem">
          @if($isAdmin)
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
              <input type="hidden" name="is_published" value="0">
              <input type="checkbox" name="is_published" value="1"
                {{ old('is_published', $item->is_published ?? false) ? 'checked' : '' }}
                style="width:16px;height:16px;cursor:pointer">
              Publikasikan
            </label>
          @else
            <span style="font-size:0.82rem;color:var(--muted)">Status:&nbsp;</span>
            @if(isset($item) && $item->is_published)
              <span class="badge badge-green">Publik</span>
            @else
              <span class="badge badge-gray">Draft</span>
            @endif
          @endif
        </div>

      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
        {{ $item ? 'Simpan Perubahan' : 'Simpan Komik' }}
      </button>
      <a href="{{ route('admin.komik.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>
@endsection
