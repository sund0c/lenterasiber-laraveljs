@extends('admin.layout')
@section('title', 'Detail Podcast')
@section('page-title', 'Detail Podcast')
@section('page-sub', 'Podcast yang sudah dipublikasikan — hanya bisa dilihat')

@section('content')
<div style="max-width:640px">
  <div class="card" style="margin-bottom:1rem">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
      <strong>Informasi Episode</strong>
      <span class="badge badge-green">Publik</span>
    </div>
    <div class="card-body">

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">NOMOR EPISODE</label>
          <div style="padding:0.5rem 0">{{ $item->episode_number ?? '—' }}</div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DURASI</label>
          <div style="padding:0.5rem 0">{{ $item->duration_minutes ? $item->duration_minutes . ' menit' : '—' }}</div>
        </div>
      </div>

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">JUDUL</label>
        <div style="font-size:0.95rem;font-weight:600;padding:0.5rem 0">{{ $item->title }}</div>
      </div>

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">DESKRIPSI</label>
        <div style="padding:0.5rem 0;font-size:0.88rem;line-height:1.6">{{ $item->description ?? '—' }}</div>
      </div>

      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">LINK AUDIO</label>
        <div style="padding:0.5rem 0">
          <a href="{{ $item->audio_url }}" target="_blank" rel="noopener"
            style="color:var(--accent);font-size:0.88rem">{{ $item->audio_url }}</a>
        </div>
      </div>

    </div>
  </div>

  <div class="card" style="margin-bottom:1rem">
    <div class="card-header"><strong>Cover & Pengaturan</strong></div>
    <div class="card-body">

      @if($item->thumbnail)
      <div class="form-group">
        <label style="color:var(--muted);font-size:0.75rem">THUMBNAIL</label>
        <div style="padding:0.5rem 0">
          <img src="{{ asset('storage/' . $item->thumbnail) }}"
            style="width:120px;height:120px;object-fit:cover;border-radius:10px;border:1px solid var(--border)">
        </div>
      </div>
      @endif

      <div class="form-row">
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">TANGGAL PUBLISH</label>
          <div style="padding:0.5rem 0">{{ $item->published_date ?? '—' }}</div>
        </div>
        <div class="form-group">
          <label style="color:var(--muted);font-size:0.75rem">DIBUAT</label>
          <div style="padding:0.5rem 0;font-size:0.82rem;color:var(--muted)">
            {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
          </div>
        </div>
      </div>

      <div style="padding:0.75rem;background:var(--bg);border-radius:8px;border:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
        ⚠️ Podcast ini sudah dipublikasikan. Hubungi Admin untuk melakukan perubahan.
      </div>

    </div>
  </div>

  <a href="{{ route('admin.podcast.index') }}" class="btn-secondary">← Kembali</a>
</div>
@endsection
