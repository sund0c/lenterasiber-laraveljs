@extends('admin.layout')
@section('title', 'Komik')
@section('page-title', 'Komik')
@section('page-sub', 'Kelola episode komik literasi keamanan siber')

@section('topbar-actions')
  <a href="{{ route('admin.komik.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
    + Tambah Komik
  </a>
@endsection

@section('content')
@php
  $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
@endphp

<div class="card">
  @if($items->isEmpty())
    <div class="card-body" style="text-align:center;padding:3rem;color:var(--muted)">
      Belum ada komik. <a href="{{ route('admin.komik.create') }}">Tambah sekarang</a>.
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">Cover</th>
          <th>Judul</th>
          <th>Episode</th>
          <th>Kategori</th>
          <th>Status</th>
          <th style="width:60px">Urut</th>
          <th style="width:140px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        @php $locked = !$isAdmin && $item->is_published; @endphp
        <tr>
          <td>
            @if($item->cover_image)
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:48px;height:48px;object-fit:cover;border-radius:8px">
            @else
              <div style="width:48px;height:48px;background:var(--bg);border-radius:8px;border:1px solid var(--border)"></div>
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
          <td style="font-size:0.82rem;color:var(--muted)">{{ $item->category ?? '—' }}</td>
          <td>
            @if($item->is_published)
              <span class="badge badge-green">Publik</span>
            @else
              <span class="badge badge-gray">Draft</span>
            @endif
          </td>
          <td style="font-size:0.85rem;color:var(--muted)">{{ $item->sort_order }}</td>
          <td>
            <div style="display:flex;gap:6px">
              @if($locked)
                {{-- Staf: komik published → hanya tombol Lihat --}}
                <a href="{{ route('admin.komik.show', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Lihat</a>
              @else
                {{-- Admin atau staf pada draft --}}
                <a href="{{ route('admin.komik.edit', $item->id) }}"
                  class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
                <button type="button" class="btn-danger btn-delete"
                  data-action="{{ route('admin.komik.destroy', $item->id) }}"
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
