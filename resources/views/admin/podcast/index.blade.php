@extends('admin.layout')
@section('title', 'Podcast')
@section('page-title', 'Podcast')
@section('page-sub', 'Kelola episode podcast literasi keamanan siber')

@section('topbar-actions')
  <a href="{{ route('admin.podcast.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
    + Tambah Episode
  </a>
@endsection

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div class="card">
  @if($items->isEmpty())
    <div class="card-body" style="text-align:center;padding:3rem;color:var(--muted)">
      Belum ada episode. <a href="{{ route('admin.podcast.create') }}">Tambah sekarang</a>.
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">Cover</th>
          <th>Judul</th>
          <th>Episode</th>
          <th>Durasi</th>
          <th>Link Audio</th>
          <th>Status</th>
          <th style="width:140px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        @php $locked = !$isAdmin && $item->is_published; @endphp
        <tr>
          <td>
            @if($item->thumbnail)
              <img src="{{ asset('storage/' . $item->thumbnail) }}"
                style="width:48px;height:48px;object-fit:cover;border-radius:8px">
            @else
              <div style="width:48px;height:48px;background:var(--bg);border-radius:8px;border:1px solid var(--border);display:flex;align-items:center;justify-content:center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2">
                  <circle cx="12" cy="12" r="10"/><polygon points="10,8 16,12 10,16"/>
                </svg>
              </div>
            @endif
          </td>
          <td>
            <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
            @if($item->description)
              <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">
                {{ Str::limit($item->description, 60) }}
              </div>
            @endif
          </td>
          <td style="font-size:0.82rem;color:var(--muted)">{{ $item->episode_number ?? '—' }}</td>
          <td style="font-size:0.82rem;color:var(--muted)">
            {{ $item->duration_minutes ? $item->duration_minutes . ' menit' : '—' }}
          </td>
          <td>
            @if($item->audio_url)
              <a href="{{ $item->audio_url }}" target="_blank" rel="noopener"
                style="font-size:0.78rem;color:var(--accent)">Buka ↗</a>
            @else
              <span style="color:var(--muted);font-size:0.78rem">—</span>
            @endif
          </td>
          <td>
            @if($item->is_published)
              <span class="badge badge-green">Publik</span>
            @else
              <span class="badge badge-gray">Draft</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px">
              @if($locked)
                <a href="{{ route('admin.podcast.show', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Lihat</a>
              @else
                <a href="{{ route('admin.podcast.edit', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
                <button type="button" class="btn-danger btn-delete"
                  data-action="{{ route('admin.podcast.destroy', $item->id) }}"
                  style="padding:4px 10px;font-size:0.75rem">Hapus</button>
              @endif
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
