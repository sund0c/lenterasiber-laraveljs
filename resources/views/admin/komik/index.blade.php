@extends('admin.layout')
@section('title', 'Komik')
@section('page-title', 'Komik')
@section('page-sub', 'Kelola episode komik literasi siber')

@section('topbar-actions')
  <a href="{{ route('admin.komik.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">+ Tambah Episode</a>
@endsection

@section('content')
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
          <th>Instagram</th>
          <th>Status</th>
          <th>Urutan</th>
          <th style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td>
            @if($item->cover_image)
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:48px;height:48px;object-fit:cover;border-radius:6px">
            @else
              <div style="width:48px;height:48px;background:var(--bg);border-radius:6px;border:1px solid var(--border)"></div>
            @endif
          </td>
          <td>
            <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
          </td>
          <td style="font-size:0.82rem;color:var(--muted)">{{ $item->episode_number ?? '—' }}</td>
          <td style="font-size:0.82rem;color:var(--muted)">{{ $item->category ?? '—' }}</td>
          <td>
            @if($item->instagram_url)
              <a href="{{ $item->instagram_url }}" target="_blank" rel="noopener"
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
          <td style="font-size:0.85rem">{{ $item->sort_order }}</td>
          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.komik.edit', $item->id) }}"
                class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
              <button type="button" class="btn-danger btn-delete"
                data-action="{{ route('admin.komik.destroy', $item->id) }}"
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
