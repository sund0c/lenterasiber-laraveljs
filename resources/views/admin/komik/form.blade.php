@extends('admin.layout')
@section('title', $item ? 'Edit Komik' : 'Tambah Komik')
@section('page-title', $item ? 'Edit Komik' : 'Tambah Komik')

@section('content')
<div style="max-width:640px">
  <form method="POST"
    action="{{ $item ? route('admin.komik.update', $item->id) : route('admin.komik.store') }}"
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

    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Informasi Episode</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Judul Komik <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Si Pancing — Kisah Phishing di Kotak Masuk">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Nomor Episode</label>
            <input type="text" name="episode_number" class="form-input"
              value="{{ old('episode_number', $item->episode_number ?? '') }}"
              placeholder="Episode 1">
          </div>
          <div class="form-group">
            <label>Kategori</label>
            <input type="text" name="category" class="form-input"
              value="{{ old('category', $item->category ?? '') }}"
              placeholder="Keamanan Email">
          </div>
        </div>

        <div class="form-group">
          <label>Deskripsi <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
          <textarea name="description" class="form-input" rows="3"
            placeholder="Deskripsi singkat episode ini...">{{ old('description', $item->description ?? '') }}</textarea>
        </div>

        <div class="form-group">
          <label>Link Instagram</label>
          <input type="url" name="instagram_url" class="form-input @error('instagram_url') is-error @enderror"
            value="{{ old('instagram_url', $item->instagram_url ?? '') }}"
            placeholder="https://www.instagram.com/p/...">
          <p style="font-size:0.72rem;color:var(--muted);margin-top:3px">
            Klik kartu komik di frontend akan membuka link ini di tab baru.
          </p>
        </div>

      </div>
    </div>

    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Cover & Pengaturan</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar Cover</label>
          @if(isset($item) && $item->cover_image)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p style="font-size:0.72rem;color:var(--muted);margin-top:4px">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="cover_image" class="form-input" accept="image/jpeg,image/png,image/webp">
          <p style="font-size:0.72rem;color:var(--muted);margin-top:3px">JPG, PNG, WebP. Maks 2MB.</p>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-input"
              value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0">
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
        {{ $item ? 'Simpan Perubahan' : 'Simpan Komik' }}
      </button>
      <a href="{{ route('admin.komik.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>
@endsection
