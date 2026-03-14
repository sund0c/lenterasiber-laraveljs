@extends('admin.layout')
@section('title', ($item ? 'Edit ' : 'Tambah ') . match($label) { 'KABAR' => 'Kabar', 'KOMIK' => 'Komik', 'PODCAST' => 'Episode Podcast', default => $label })
@section('page-title', ($item ? 'Edit ' : 'Tambah ') . match($label) { 'KABAR' => 'Kabar', 'KOMIK' => 'Komik', 'PODCAST' => 'Episode Podcast', default => $label })
@section('page-sub', match($label) {
    'KABAR'   => 'Artikel dan berita literasi keamanan siber',
    'KOMIK'   => 'Episode komik literasi keamanan siber',
    'PODCAST' => 'Episode podcast literasi keamanan siber',
    default   => '',
})

@section('content')
@php
    $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div style="max-width:760px">
  <form method="POST"
    action="{{ $item
        ? route('admin.konten.update', [$label, $item->id])
        : route('admin.konten.store', $label) }}"
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

    {{-- ── INFORMASI ─────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Informasi {{ match($label) { 'KABAR' => 'Artikel', 'KOMIK' => 'Episode', 'PODCAST' => 'Episode', default => '' } }}</strong></div>
      <div class="card-body">

        {{-- Episode Number: KOMIK & PODCAST --}}
        @if(in_array($label, ['KOMIK', 'PODCAST']))
        <div class="form-row">
          <div class="form-group">
            <label>Nomor Episode <span style="color:var(--red)">*</span></label>
            <input type="text" name="episode_number"
              class="form-input @error('episode_number') is-error @enderror"
              value="{{ old('episode_number', $item->episode_number ?? '') }}"
              placeholder="{{ $label === 'KOMIK' ? 'Episode 1' : 'EP.08' }}"
              maxlength="20" required>
            @error('episode_number')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          @if($label === 'PODCAST')
          <div class="form-group">
            <label>Durasi (menit) <span style="color:var(--red)">*</span></label>
            <input type="number" name="duration_minutes"
              class="form-input @error('duration_minutes') is-error @enderror"
              value="{{ old('duration_minutes', $item->duration_minutes ?? '') }}"
              placeholder="45" min="1" required>
            @error('duration_minutes')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          @else
          <div class="form-group">
            <label>Tanggal Publish <span style="color:var(--red)">*</span></label>
            <input type="date" name="published_date"
              class="form-input @error('published_date') is-error @enderror"
              value="{{ old('published_date', isset($item->published_date) ? \Carbon\Carbon::parse($item->published_date)->format('Y-m-d') : date('Y-m-d')) }}"
              required>
            @error('published_date')<p class="field-error">{{ $message }}</p>@enderror
          </div>
          @endif
        </div>
        @endif

        {{-- Judul --}}
        <div class="form-group">
          <label>{{ match($label) { 'KABAR' => 'Judul Artikel', 'KOMIK' => 'Judul Komik', 'PODCAST' => 'Judul Episode', default => 'Judul' } }} <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" id="konten_title"
            class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="{{ match($label) {
                'KABAR'   => 'Mengenali Serangan Phishing di Kotak Masuk Email Resmi',
                'KOMIK'   => 'Si Pancing — Kisah Phishing di Kotak Masuk',
                'PODCAST' => 'Ransomware: Ancaman Nyata bagi Data Pemerintah',
                default   => ''
            } }}"
            maxlength="255" required>
          @error('title')<p class="field-error">{{ $message }}</p>@enderror
        </div>

         <div class="form-row">
          <div class="form-group">
            <label>Slug URL <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
            <input type="text" name="slug" id="konten_slug" class="form-input"
              value="{{ old('slug', $item->slug ?? '') }}"
              placeholder="otomatis dari judul">
            <p class="field-hint">Kosongkan untuk generate otomatis.</p>
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


        {{-- Tanggal Publish: PODCAST (belum dirender di atas) --}}
        @if($label === 'PODCAST')
        <div class="form-row">
          <div class="form-group">
            <label>Tanggal Publish <span style="color:var(--red)">*</span></label>
            <input type="date" name="published_date"
              class="form-input @error('published_date') is-error @enderror"
              value="{{ old('published_date', isset($item->published_date) ? \Carbon\Carbon::parse($item->published_date)->format('Y-m-d') : date('Y-m-d')) }}"
              required>
            @error('published_date')<p class="field-error">{{ $message }}</p>@enderror
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
              <span style="font-size:0.82rem;color:var(--muted)">Status: <span class="badge badge-gray">Draft</span></span>
            @endif
          </div>
        </div>
        @endif

        {{-- Kategori: admin only --}}
        @if($isAdmin)
        <div class="form-group">
          <label>Kategori</label>
          <input type="text" name="category"
            class="form-input @error('category') is-error @enderror"
            value="{{ old('category', $item->category ?? '') }}"
            placeholder="{{ match($label) {
                'KABAR'   => 'TIPS KEAMANAN',
                'KOMIK'   => 'Keamanan Email',
                'PODCAST' => 'Malware',
                default   => ''
            } }}" maxlength="100"
            @if($label === 'KABAR') list="category-suggestions" @endif>
          @if($label === 'KABAR')
          <datalist id="category-suggestions">
            <option value="TIPS KEAMANAN">
            <option value="KEBIJAKAN">
            <option value="WASPADA">
            <option value="EDUKASI">
            <option value="PROGRAM">
            <option value="BERITA">
          </datalist>
          @endif
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

        {{-- Ringkasan / Excerpt --}}
        <div class="form-group">
          <label>Ringkasan <span style="color:var(--red)">*</span></label>
          <textarea name="excerpt" rows="2"
            class="form-input @error('excerpt') is-error @enderror"
            placeholder="{{ match($label) {
                'KABAR'   => 'Panduan lengkap mengidentifikasi email phishing...',
                'KOMIK'   => 'Deskripsi singkat episode komik ini...',
                'PODCAST' => 'Membahas ancaman ransomware modern dan cara pencegahannya...',
                default   => ''
            } }}"
            maxlength="100" required>{{ old('excerpt', $item->excerpt ?? '') }}</textarea>
          <p class="field-hint">Maksimal 100 karakter. Tampil di kartu halaman publik.</p>
          @error('excerpt')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- Artikel Lengkap / Content --}}
        <div class="form-group">
          <label>{{ match($label) { 'KABAR' => 'Isi Artikel Lengkap', 'KOMIK' => 'Penjelasan Lengkap', 'PODCAST' => 'Catatan / Transkrip Episode', default => 'Konten' } }}
            @if($label === 'KABAR') <span style="color:var(--red)">*</span> @else <span style="color:var(--muted);font-weight:normal">(opsional)</span> @endif
          </label>
          <textarea name="content" rows="18"
            class="form-input @error('content') is-error @enderror"
            placeholder="Tulis konten lengkap di sini..."
            @if($label === 'KABAR') required @endif>{{ old('content', $item->content ?? '') }}</textarea>
          <p class="field-hint">Mendukung HTML: &lt;p&gt; &lt;h2&gt; &lt;h3&gt; &lt;ul&gt; &lt;li&gt; &lt;strong&gt; &lt;em&gt; &lt;a href=""&gt; &lt;blockquote&gt;</p>
          @error('content')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- External URL: KOMIK & PODCAST --}}
        @if(in_array($label, ['KOMIK', 'PODCAST']))
        <div class="form-group">
          <label>
            {{ $label === 'KOMIK' ? 'Link Instagram' : 'Link Audio (DENGARKAN)' }}
            <span style="color:var(--red)">*</span>
          </label>
          <input type="url" name="external_url"
            class="form-input @error('external_url') is-error @enderror"
            value="{{ old('external_url', $item->external_url ?? '') }}"
            placeholder="{{ $label === 'KOMIK' ? 'https://www.instagram.com/p/...' : 'https://open.spotify.com/episode/...' }}"
            maxlength="500" required>
          <p class="field-hint">
            {{ $label === 'KOMIK'
                ? 'Klik kartu komik di frontend akan membuka link ini di tab baru.'
                : 'Link Spotify, Anchor, YouTube, atau platform podcast lainnya.' }}
          </p>
          @error('external_url')<p class="field-error">{{ $message }}</p>@enderror
        </div>
        @endif

      </div>
    </div>

    {{-- ── COVER & STATUS ────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Cover & Pengaturan</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar Cover {{ $label !== 'PODCAST' ? '/ Thumbnail' : '' }} <span style="color:var(--red)">*</span></label>
          @if(isset($item) && $item->cover_image)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:{{ $label === 'KABAR' ? '160px;height:100px' : '80px;height:80px' }};object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="cover_image"
            class="form-input @error('cover_image') is-error @enderror"
            accept="image/jpeg,image/png"
            {{ !isset($item) ? 'required' : '' }}>
          <p class="field-hint">JPG, PNG. Maks 2MB. {{ $label === 'KABAR' ? 'Rasio 16:9 disarankan.' : 'Rasio 1:1 disarankan.' }}</p>
          @error('cover_image')<p class="field-error">{{ $message }}</p>@enderror
        </div>

        {{-- Status: KABAR & KOMIK (PODCAST sudah di atas) --}}
        @if($label !== 'PODCAST')
        <div class="form-group" style="display:flex;align-items:center;padding-top:0.4rem">
          @if($isAdmin)
            <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
              <input type="hidden" name="status" value="draft">
              <input type="checkbox" name="status" value="published"
                {{ old('status', $item->status ?? '') === 'published' ? 'checked' : '' }}
                style="width:16px;height:16px;cursor:pointer">
              Publikasikan
            </label>
          @else
            <span style="font-size:0.82rem;color:var(--muted)">
              Status: <span class="badge badge-gray">Draft</span>
              <span style="margin-left:6px;font-size:0.75rem">(Admin yang akan mempublish)</span>
            </span>
          @endif
        </div>
        @endif

      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
        {{ $item ? 'Simpan Perubahan' : 'Simpan ' . match($label) { 'KABAR' => 'Kabar', 'KOMIK' => 'Komik', 'PODCAST' => 'Episode', default => '' } }}
      </button>
      <a href="{{ route('admin.konten.index', $label) }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>

@if($label === 'KABAR')
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {
    var titleInput = document.getElementById('konten_title');
    var slugInput  = document.getElementById('konten_slug');
    if (!titleInput || !slugInput) return;
    var slugEdited = slugInput.value !== '';
    slugInput.addEventListener('input', function () { slugEdited = true; });
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
@endif
@endsection
