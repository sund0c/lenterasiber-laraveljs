@extends('admin.layout')
@section('title', 'Detail ' . match($label) { 'KABAR' => 'Kabar', 'KOMIK' => 'Komik', 'PODCAST' => 'Podcast', default => $label })
@section('page-title', 'Detail ' . match($label) { 'KABAR' => 'Kabar', 'KOMIK' => 'Komik', 'PODCAST' => 'Podcast', default => $label })
@section('page-sub', 'Konten yang sudah dipublikasikan — hanya bisa dilihat')

@section('content')
<div style="max-width:760px">
  <div class="card" style="margin-bottom:1rem">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      <strong>Informasi Konten</strong>
      <span class="badge badge-green">Publik</span>
    </div>
    <div class="card-body">

      @if(in_array($label, ['KOMIK', 'PODCAST']))
      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">NOMOR EPISODE</label>
          <div style="padding:0.5rem 0">{{ $item->episode_number ?? '—' }}</div>
        </div>
        @if($label === 'PODCAST')
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DURASI</label>
          <div style="padding:0.5rem 0">{{ $item->duration_minutes ? $item->duration_minutes . ' menit' : '—' }}</div>
        </div>
        @endif
      </div>
      @endif

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
          <label style="color:var(--muted);font-size:0.75rem">TANGGAL PUBLISH</label>
          <div style="padding:0.5rem 0;font-size:0.88rem">
            {{ $item->published_date ? \Carbon\Carbon::parse($item->published_date)->format('d/m/Y') : '—' }}
          </div>
        </div>
      </div>

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">RINGKASAN</label>
        <div style="padding:0.5rem 0;font-size:0.88rem">{{ $item->excerpt ?? '—' }}</div>
      </div>

      @if($item->content)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">KONTEN</label>
        <div style="padding:0.5rem 0;font-size:0.88rem;line-height:1.7;border:1px solid var(--border);border-radius:8px;padding:1rem;background:var(--bg)">
          {!! $item->content !!}
        </div>
      </div>
      @endif

      @if($item->external_url)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">{{ $label === 'KOMIK' ? 'LINK INSTAGRAM' : 'LINK AUDIO' }}</label>
        <div style="padding:0.5rem 0">
          <a href="{{ $item->external_url }}" target="_blank" rel="noopener"
            style="color:var(--accent);font-size:0.88rem">{{ $item->external_url }}</a>
        </div>
      </div>
      @endif

    </div>
  </div>

  <div style="padding:0.75rem;background:var(--bg);border-radius:8px;border:1px solid var(--border);font-size:0.82rem;color:var(--muted);margin-bottom:1rem">
    ⚠️ Konten ini sudah dipublikasikan. Hubungi Admin untuk melakukan perubahan.
  </div>

  <a href="{{ route('admin.konten.index', $label) }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
