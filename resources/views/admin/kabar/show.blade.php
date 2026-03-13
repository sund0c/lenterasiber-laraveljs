@extends('admin.layout')
@section('title', 'Detail Kabar')
@section('page-title', 'Detail Kabar')
@section('page-sub', 'Artikel yang sudah dipublikasikan — hanya bisa dilihat')

@section('content')
<div style="max-width:640px">
  <div class="card" style="margin-bottom:1rem">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      <strong>Informasi Artikel</strong>
      <span class="badge badge-green">Publik</span>
    </div>
    <div class="card-body">

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">JUDUL</label>
        <div style="font-size:0.95rem;font-weight:600;padding:0.5rem 0">{{ $item->title }}</div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">KATEGORI</label>
          <div style="padding:0.5rem 0">{{ $item->category ?? '—' }}</div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">ESTIMASI BACA</label>
          <div style="padding:0.5rem 0">{{ $item->read_minutes ?? '—' }} menit</div>
        </div>
      </div>

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">SLUG</label>
        <div style="padding:0.5rem 0;font-size:0.82rem;font-family:monospace;color:var(--muted)">{{ $item->slug }}</div>
      </div>

      @if($item->excerpt)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">RINGKASAN</label>
        <div style="padding:0.5rem 0;font-size:0.88rem;line-height:1.6">{{ $item->excerpt }}</div>
      </div>
      @endif

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">ISI ARTIKEL</label>
        <div style="padding:0.5rem 0;font-size:0.88rem;line-height:1.6">
          {{ Str::limit(strip_tags($item->content), 400) }}
          @if(strlen(strip_tags($item->content)) > 400)
            <span style="color:var(--muted)">...</span>
          @endif
        </div>
      </div>

    </div>
  </div>

  <div class="card" style="margin-bottom:1rem">
    <div class="card-header"><strong>Cover & Pengaturan</strong></div>
    <div class="card-body">

      @if($item->thumbnail)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">COVER</label>
        <div style="padding:0.5rem 0">
          <img src="{{ asset('storage/' . $item->thumbnail) }}"
            style="width:180px;height:110px;object-fit:cover;border-radius:10px;border:1px solid var(--border)">
        </div>
      </div>
      @endif

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DIPUBLIKASIKAN</label>
          <div style="padding:0.5rem 0;font-size:0.82rem;color:var(--muted)">
            {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('d/m/Y H:i') : '—' }}
          </div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DIBUAT</label>
          <div style="padding:0.5rem 0;font-size:0.82rem;color:var(--muted)">
            {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
          </div>
        </div>
      </div>

      <div style="padding:0.75rem;background:var(--bg);border-radius:8px;border:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
        ⚠️ Artikel ini sudah dipublikasikan. Hubungi Admin untuk melakukan perubahan.
      </div>

    </div>
  </div>

  <div>
    <a href="{{ route('admin.kabar.index') }}" class="btn-secondary">← Kembali</a>
  </div>
</div>
@endsection
