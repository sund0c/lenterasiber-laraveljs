@extends('admin.layout')
@section('title', $item ? 'Edit Podcast' : 'Tambah Podcast')
@section('page-title', $item ? 'Edit Podcast' : 'Tambah Podcast')
@section('page-sub', 'Episode podcast literasi keamanan siber')

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div style="max-width:640px">
  <form method="POST"
    action="{{ $item ? route('admin.podcast.update', $item->id) : route('admin.podcast.store') }}"
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

    {{-- ── INFO EPISODE ──────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Informasi Episode</strong></div>
      <div class="card-body">

        <div class="form-row">
          <div class="form-group">
            <label>Nomor Episode <span style="color:var(--red)">*</span></label>
            <input type="text" name="episode_number"
              class="form-input @error('episode_number') is-error @enderror"
              value="{{ old('episode_number', $item->episode_number ?? '') }}"
              placeholder="EP.08" maxlength="20" required>
            @error('episode_number')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>
          <div class="form-group">
            <label>Durasi (menit) <span style="color:var(--red)">*</span></label>
            <input type="number" name="duration_minutes"
              class="form-input @error('duration_minutes') is-error @enderror"
              value="{{ old('duration_minutes', $item->duration_minutes ?? '') }}"
              placeholder="45" min="1" required>
            @error('duration_minutes')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <label>Judul Episode <span style="color:var(--red)">*</span></label>
          <input type="text" name="title"
            class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Ransomware: Ancaman Nyata bagi Data Pemerintah"
            maxlength="255" required>
          @error('title')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label>Deskripsi <span style="color:var(--red)">*</span></label>
          <textarea name="description" rows="3"
            class="form-input @error('description') is-error @enderror"
            placeholder="Membahas modus operandi ransomware modern..."
            maxlength="100" required>{{ old('description', $item->description ?? '') }}</textarea>
          <p class="field-hint">Tampil di kartu podcast halaman publik. Maksimal 100 karakter.</p>
          @error('description')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label>Link Audio (DENGARKAN) <span style="color:var(--red)">*</span></label>
          <input type="url" name="audio_url"
            class="form-input @error('audio_url') is-error @enderror"
            value="{{ old('audio_url', $item->audio_url ?? '') }}"
            placeholder="https://open.spotify.com/episode/..."
            maxlength="500" required>
          <p class="field-hint">Link Spotify, Anchor, YouTube, atau platform podcast lainnya.</p>
          @error('audio_url')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- ── COVER & PENGATURAN ────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Cover & Pengaturan</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar Cover / Thumbnail <span style="color:var(--red)">*</span></label>
          @if(isset($item) && $item->thumbnail)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->thumbnail) }}"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="thumbnail"
            class="form-input @error('thumbnail') is-error @enderror"
            accept="image/jpeg,image/png,image/webp"
            {{ !isset($item) ? 'required' : '' }}>
          <p class="field-hint">JPG, PNG, WebP. Maks 2MB. Rasio 1:1 disarankan.</p>
          @error('thumbnail')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Tanggal Publish <span style="color:var(--red)">*</span></label>
            <input type="date" name="published_date"
              class="form-input @error('published_date') is-error @enderror"
              value="{{ old('published_date', $item->published_date ?? '') }}"
              required>
            @error('published_date')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
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
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
        {{ $item ? 'Simpan Perubahan' : 'Simpan Episode' }}
      </button>
      <a href="{{ route('admin.podcast.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>
@endsection
