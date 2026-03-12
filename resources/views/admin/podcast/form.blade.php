@extends('admin.layout')
@section('title', $item ? 'Edit Podcast' : 'Tambah Podcast')
@section('page-title', $item ? 'Edit Podcast' : 'Tambah Podcast')
@section('page-sub', 'Episode podcast literasi keamanan siber')

@section('content')
<div style="max-width:640px">
  <form method="POST"
    action="{{ $item ? route('admin.podcast.update', $item->id) : route('admin.podcast.store') }}"
    enctype="multipart/form-data">
    @csrf
    @if($item) @method('PUT') @endif

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:1rem">
        <ul style="margin:0;padding-left:1rem">
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
            <label>Nomor Episode</label>
            <input type="text" name="episode_number" class="form-input"
              value="{{ old('episode_number', $item->episode_number ?? '') }}"
              placeholder="EP.08" maxlength="20">
          </div>
          <div class="form-group">
            <label>Durasi (menit)</label>
            <input type="number" name="duration_minutes" class="form-input"
              value="{{ old('duration_minutes', $item->duration_minutes ?? '') }}"
              placeholder="45" min="1">
          </div>
        </div>

        <div class="form-group">
          <label>Judul Episode <span style="color:var(--red)">*</span></label>
          <input type="text" name="title"
            class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Ransomware: Ancaman Nyata bagi Data Pemerintah">
        </div>

        <div class="form-group">
          <label>Deskripsi</label>
          <textarea name="description" class="form-input" rows="3"
            placeholder="Membahas modus operandi ransomware modern dan strategi mitigasi...">{{ old('description', $item->description ?? '') }}</textarea>
          <p class="field-hint">Tampil di kartu podcast halaman publik.</p>
        </div>

        <div class="form-group">
          <label>Link Audio (DENGARKAN) <span style="color:var(--red)">*</span></label>
          <input type="url" name="audio_url"
            class="form-input @error('audio_url') is-error @enderror"
            value="{{ old('audio_url', $item->audio_url ?? '') }}"
            placeholder="https://open.spotify.com/episode/... atau https://anchor.fm/...">
          <p class="field-hint">Link Spotify, Anchor, YouTube, atau platform podcast lainnya. Tombol DENGARKAN akan membuka link ini.</p>
        </div>

      </div>
    </div>

    {{-- ── COVER & STATUS ────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Cover & Pengaturan</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar Cover / Thumbnail</label>
          @if(isset($item) && $item->thumbnail)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->thumbnail) }}"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="thumbnail" class="form-input"
            accept="image/jpeg,image/png,image/webp">
          <p class="field-hint">JPG, PNG, WebP. Maks 2MB. Rasio 1:1 disarankan.</p>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Tanggal Publish</label>
            <input type="date" name="published_date" class="form-input"
              value="{{ old('published_date', $item->published_date ?? '') }}">
          </div>
          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
              <input type="hidden" name="is_published" value="0">
              <input type="checkbox" name="is_published" value="1"
                {{ old('is_published', $item->is_published ?? false) ? 'checked' : '' }}
                style="width:16px;height:16px;cursor:pointer">
              Tampilkan di halaman publik
            </label>
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
