@extends('admin.layout')
@section('title', 'Kabar Lentera')
@section('page-title', 'Kabar Lentera')
@section('page-sub', 'Kelola artikel dan berita yang tampil di halaman publik')

@section('topbar-actions')
  <a href="{{ route('admin.kabar.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">+ Tambah Kabar</a>
@endsection

@section('content')
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
          <th>Kategori</th>
          <th>Slug</th>
          <th>Status</th>
          <th style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td>
            @if($item->thumbnail)
              <img src="{{ asset('storage/' . $item->thumbnail) }}"
                style="width:48px;height:48px;object-fit:cover;border-radius:6px">
            @else
              <div style="width:48px;height:48px;background:var(--bg);border-radius:6px;border:1px solid var(--border)"></div>
            @endif
          </td>
          <td>
            <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
            @if($item->excerpt)
              <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">{{ Str::limit($item->excerpt, 60) }}</div>
            @endif
          </td>
          <td>
            @if($item->category)
              <span class="badge badge-blue">{{ $item->category }}</span>
            @else
              <span style="color:var(--muted);font-size:0.78rem">—</span>
            @endif
          </td>
          <td style="font-size:0.78rem;color:var(--muted)">{{ $item->slug }}</td>
          <td>
 @if($item->status === 'published')
  <span class="badge badge-green">Publik</span>
@else
  <span class="badge badge-gray">Draft</span>
@endif
          </td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.kabar.edit', $item->id) }}"
                class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
              <button type="button" class="btn-danger btn-delete"
                data-action="{{ route('admin.kabar.destroy', $item->id) }}"
                style="padding:4px 10px;font-size:0.75rem">Hapus</button>
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
