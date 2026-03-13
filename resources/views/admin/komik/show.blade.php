@extends('admin.layout')
@section('title', 'Detail Komik')
@section('page-title', 'Detail Komik')
@section('page-sub', 'Komik yang sudah dipublikasikan — hanya bisa dilihat')

@section('content')
<div style="max-width:640px">
  <div class="card" style="margin-bottom:1rem">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      <strong>Informasi Episode</strong>
      <span class="badge badge-green">Publik</span>
    </div>
    <div class="card-body">

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">JUDUL</label>
        <div style="font-size:0.95rem;font-weight:600;padding:0.5rem 0">{{ $item->title }}</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">NOMOR EPISODE</label>
          <div style="padding:0.5rem 0">{{ $item->episode_number ?? '—' }}</div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">KATEGORI</label>
          <div style="padding:0.5rem 0">{{ $item->category ?? '—' }}</div>
        </div>
      </div>

      @if($item->description)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">DESKRIPSI</label>
        <div style="padding:0.5rem 0;font-size:0.88rem;line-height:1.6">{{ $item->description }}</div>
      </div>
      @endif

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">LINK INSTAGRAM</label>
        <div style="padding:0.5rem 0">
          <a href="{{ $item->instagram_url }}" target="_blank" rel="noopener"
            style="color:var(--accent);font-size:0.88rem">
            {{ $item->instagram_url }}
          </a>
        </div>
      </div>

    </div>
  </div>

  <div class="card" style="margin-bottom:1rem">
    <div class="card-header"><strong>Cover & Pengaturan</strong></div>
    <div class="card-body">

      @if($item->cover_image)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">COVER</label>
        <div style="padding:0.5rem 0">
          <img src="{{ asset('storage/' . $item->cover_image) }}"
            style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--border)">
        </div>
      </div>
      @endif

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">URUTAN TAMPIL</label>
          <div style="padding:0.5rem 0">{{ $item->sort_order }}</div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DIBUAT</label>
          <div style="padding:0.5rem 0;font-size:0.82rem;color:var(--muted)">
            {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
          </div>
        </div>
      </div>

      <div style="padding:0.75rem;background:var(--bg);border-radius:8px;border:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
        ⚠️ Komik ini sudah dipublikasikan. Hubungi Admin untuk melakukan perubahan.
      </div>

    </div>
  </div>

  <div>
    <a href="{{ route('admin.komik.index') }}" class="btn-secondary">← Kembali</a>
  </div>

</div>
@endsection
