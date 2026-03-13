@extends('admin.layout')
@section('title', 'Kabar Lentera')
@section('page-title', 'Kabar Lentera')
@section('page-sub', 'Kelola artikel dan berita yang tampil di halaman publik')

@section('topbar-actions')
  <a href="{{ route('admin.kabar.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
    + Tambah Kabar
  </a>
@endsection

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div class="card">
  @if($items->isEmpty())
    <div class="card-body" style="text-align:center;padding:3rem;color:var(--muted)">
      Belum ada kabar. <a href="{{ route('admin.kabar.create') }}">Tambah sekarang</a>.
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">Cover</th>
          <th>Judul</th>
          <th style="width:120px">Kategori</th>
          <th style="width:80px">Baca</th>
          <th style="width:90px">Status</th>
          <th style="width:150px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        @php $locked = !$isAdmin && $item->status === 'published'; @endphp
        <tr>
          <td>
            @if($item->thumbnail)
              <img src="{{ asset('storage/' . $item->thumbnail) }}"
                style="width:56px;height:40px;object-fit:cover;border-radius:6px">
            @else
              <div style="width:56px;height:40px;background:var(--bg);border-radius:6px;border:1px solid var(--border)"></div>
            @endif
          </td>
          <td>
            <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
            @if($item->excerpt)
              <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">
                {{ Str::limit($item->excerpt, 70) }}
              </div>
            @endif
            <div style="font-size:0.7rem;color:var(--border);margin-top:2px;font-family:monospace">
              {{ $item->slug }}
            </div>
          </td>
          <td>
            @if($item->category)
              <span style="font-size:0.72rem;font-weight:600;padding:2px 8px;border-radius:4px;background:#3b82f620;color:#3b82f6">
                {{ $item->category }}
              </span>
            @else
              <span style="color:var(--muted);font-size:0.78rem">—</span>
            @endif
          </td>
          <td style="font-size:0.82rem;color:var(--muted)">
            {{ $item->read_minutes ?? '—' }} mnt
          </td>
          <td>
            @if($item->status === 'published')
              <span class="badge badge-green">Publik</span>
            @else
              <span class="badge badge-gray">Draft</span>
            @endif
          </td>
          <td>
            <div style="display:flex;gap:6px;flex-wrap:wrap">
              @if($locked)
                <a href="{{ route('admin.kabar.show', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Lihat</a>
              @else
                <a href="{{ route('admin.kabar.edit', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
                <button type="button" class="btn-danger btn-delete"
                  data-action="{{ route('admin.kabar.destroy', $item->id) }}"
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
