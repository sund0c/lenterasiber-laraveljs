@extends('admin.layout')
@section('title', $item ? 'Edit Kabar' : 'Tambah Kabar')
@section('page-title', $item ? 'Edit Kabar' : 'Tambah Kabar')
@section('page-sub', 'Artikel dan berita literasi keamanan siber')

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div style="max-width:760px">
  <form method="POST"
    action="{{ $item ? route('admin.kabar.update', $item->id) : route('admin.kabar.store') }}"
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

    {{-- ── INFORMASI ARTIKEL ─────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Informasi Artikel</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Judul <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" id="kabar_title"
            class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Mengenali Serangan Phishing di Kotak Masuk Email Resmi"
            maxlength="255" required>
          @error('title')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Slug URL <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
            <input type="text" name="slug" id="kabar_slug" class="form-input"
              value="{{ old('slug', $item->slug ?? '') }}"
              placeholder="otomatis dari judul">
            <p class="field-hint">Kosongkan untuk generate otomatis.</p>
          </div>
          <div class="form-group">
            <label>Kategori <span style="color:var(--red)">*</span></label>
            <input type="text" name="category"
              class="form-input @error('category') is-error @enderror"
              value="{{ old('category', $item->category ?? '') }}"
              placeholder="TIPS KEAMANAN"
              maxlength="80" required
              list="category-suggestions">
            <datalist id="category-suggestions">
              <option value="TIPS KEAMANAN">
              <option value="KEBIJAKAN">
              <option value="WASPADA">
              <option value="EDUKASI">
              <option value="PROGRAM">
              <option value="BERITA">
            </datalist>
            @error('category')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="form-group">
          <label>Ringkasan / Excerpt <span style="color:var(--red)">*</span></label>
          <textarea name="excerpt" rows="2"
            class="form-input @error('excerpt') is-error @enderror"
            placeholder="Panduan lengkap mengidentifikasi email phishing..."
            maxlength="100" required>{{ old('excerpt', $item->excerpt ?? '') }}</textarea>
          <p class="field-hint">Maksimal 100 karakter. Tampil di kartu artikel halaman publik.</p>
          @error('excerpt')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-group">
          <label>Isi Artikel Lengkap <span style="color:var(--red)">*</span></label>
          <textarea name="content" id="kabar_content" rows="18"
            class="form-input @error('content') is-error @enderror"
            placeholder="Tulis isi artikel di sini..." required>{{ old('content', $item->content ?? '') }}</textarea>
          <p class="field-hint">Mendukung HTML: &lt;p&gt; &lt;h2&gt; &lt;h3&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;strong&gt; &lt;em&gt; &lt;a href=""&gt; &lt;blockquote&gt;</p>
          @error('content')
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
                style="width:160px;height:100px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="thumbnail"
            class="form-input @error('thumbnail') is-error @enderror"
            accept="image/jpeg,image/png,image/webp"
            {{ !isset($item) ? 'required' : '' }}>
          <p class="field-hint">JPG, PNG, WebP. Maks 2MB. Rasio 16:9 disarankan.</p>
          @error('thumbnail')
            <p class="field-error">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Estimasi Baca (menit) <span style="color:var(--red)">*</span></label>
            <input type="number" name="read_minutes"
              class="form-input @error('read_minutes') is-error @enderror"
              value="{{ old('read_minutes', $item->read_minutes ?? 3) }}"
              min="1" max="60" required>
            @error('read_minutes')
              <p class="field-error">{{ $message }}</p>
            @enderror
          </div>

          <div class="form-group" style="display:flex;align-items:center;padding-top:1.6rem">
  @if($isAdmin)
    <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
      <input type="hidden" name="status" value="draft">
      <input type="checkbox" name="status" value="published"
        {{ old('status', $item->status ?? '') === 'published' ? 'checked' : '' }}
        style="width:16px;height:16px;cursor:pointer">
      Publikasikan
    </label>
  @else
    <span style="font-size:0.82rem;color:var(--muted)">Status:&nbsp;</span>
    @if(isset($item) && $item->status === 'published')
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
        {{ $item ? 'Simpan Perubahan' : 'Simpan Kabar' }}
      </button>
      <a href="{{ route('admin.kabar.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {
  var titleInput = document.getElementById('kabar_title');
  var slugInput  = document.getElementById('kabar_slug');
  if (!titleInput || !slugInput) return;

  var slugEdited = slugInput.value !== '';

  slugInput.addEventListener('input', function () {
    slugEdited = true;
  });

  titleInput.addEventListener('input', function () {
    if (slugEdited) return;
    slugInput.value = titleInput.value
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .trim()
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-');
  });
});
</script>
@endsection
