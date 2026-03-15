@extends('admin.layout')
@section('title', 'Marquee Info')
@section('page-title', 'Marquee Info')
@section('page-sub', 'Kelola teks berjalan yang tampil di halaman publik')

@section('topbar-actions')
    <a href="{{ route('admin.marquee.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">+ Tambah
        Marquee</a>
@endsection

@section('content')
    @php
        $sortUrl = function (string $col) use ($sort, $dir, $search) {
            $newDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newDir, 'q' => $search, 'page' => 1]);
        };
        $sortIcon = function (string $col) use ($sort, $dir) {
            if ($sort !== $col) {
                return '<span style="opacity:0.3;font-size:0.7rem;margin-left:3px">↕</span>';
            }
            return $dir === 'asc'
                ? '<span style="color:var(--accent);font-size:0.7rem;margin-left:3px">↑</span>'
                : '<span style="color:var(--accent);font-size:0.7rem;margin-left:3px">↓</span>';
        };
    @endphp

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem">{{ session('success') }}</div>
    @endif

    {{-- Search --}}
    <div style="margin-bottom:1rem;display:flex;gap:8px;align-items:center">
        <form method="GET" action="{{ route('admin.marquee.index') }}" style="display:flex;gap:8px;flex:1;max-width:460px">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir" value="{{ $dir }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari teks marquee..."
                class="form-input" style="flex:1;padding:0.45rem 0.75rem;font-size:0.88rem">
            <button type="submit" class="btn-primary"
                style="width:auto;padding:0.45rem 1rem;font-size:0.88rem">Cari</button>
            @if ($search)
                <a href="{{ route('admin.marquee.index', ['sort' => $sort, 'dir' => $dir]) }}" class="btn-secondary"
                    style="padding:0.45rem 0.75rem;font-size:0.88rem">✕</a>
            @endif
        </form>
        @if ($search)
            <span style="font-size:0.82rem;color:var(--muted)">
                {{ $items->total() }} hasil untuk <em>"{{ $search }}"</em>
            </span>
        @endif
    </div>

    <div class="card">
        @if ($items->isEmpty())
            <div class="card-body" style="text-align:center;padding:3rem;color:var(--muted)">
                @if ($search)
                    Tidak ada marquee yang cocok dengan <em>"{{ $search }}"</em>.
                    <a href="{{ route('admin.marquee.index') }}">Tampilkan semua</a>
                @else
                    Belum ada marquee. <a href="{{ route('admin.marquee.create') }}">Tambah sekarang</a>.
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>
                                <a href="{{ $sortUrl('title') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Teks Marquee {!! $sortIcon('title') !!}
                                </a>
                            </th>
                            <th style="width:90px">
                                <a href="{{ $sortUrl('status') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Status {!! $sortIcon('status') !!}
                                </a>
                            </th>
                            <th style="width:60px">Oleh</th>
                            <th style="width:130px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td style="font-size:0.88rem;font-weight:500">{{ $item->title }}</td>
                                <td>
                                    @if ($item->status === 'published')
                                        <span class="badge badge-green">Publik</span>
                                    @else
                                        <span class="badge badge-gray">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $editorId = $item->updated_by ?? $item->created_by;
                                        $editor = $editorId
                                            ? DB::table('admin_users')->where('id', $editorId)->value('username')
                                            : null;
                                    @endphp
                                    <span style="font-size:0.75rem;color:var(--muted)">{{ $editor ?? '—' }}</span>
                                </td>
                                <td>
                                    <div style="display:flex;gap:4px">
                                        <a href="{{ route('admin.marquee.edit', $item->id) }}" class="btn-secondary"
                                            style="padding:4px 10px;font-size:0.75rem">Edit</a>
                                        <button type="button" class="btn-danger btn-delete"
                                            data-action="{{ route('admin.marquee.destroy', $item->id) }}"
                                            style="padding:4px 10px;font-size:0.75rem">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($items->lastPage() > 1)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1rem;border-top:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
                    <span>Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }}
                        marquee</span>
                    <div style="display:flex;gap:4px;align-items:center">
                        @if ($items->onFirstPage())
                            <span
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;opacity:0.4">‹</span>
                        @else
                            <a href="{{ $items->previousPageUrl() }}"
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;color:inherit;text-decoration:none">‹</a>
                        @endif
                        @php
                            $current = $items->currentPage();
                            $last = $items->lastPage();
                            $pages = collect(range(1, $last))->filter(
                                fn($p) => $p === 1 || $p === $last || abs($p - $current) <= 2,
                            );
                        @endphp
                        @php $prev = null; @endphp
                        @foreach ($pages as $page)
                            @if ($prev !== null && $page - $prev > 1)
                                <span style="padding:4px 6px;color:var(--muted)">…</span>
                            @endif
                            @if ($page === $current)
                                <span
                                    style="padding:4px 10px;border:1px solid var(--accent);border-radius:6px;background:var(--accent);color:#fff;font-weight:600">{{ $page }}</span>
                            @else
                                <a href="{{ $items->url($page) }}"
                                    style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;color:inherit;text-decoration:none">{{ $page }}</a>
                            @endif
                            @php $prev = $page; @endphp
                        @endforeach
                        @if ($items->hasMorePages())
                            <a href="{{ $items->nextPageUrl() }}"
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;color:inherit;text-decoration:none">›</a>
                        @else
                            <span
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;opacity:0.4">›</span>
                        @endif
                    </div>
                </div>
            @else
                <div style="padding:0.6rem 1rem;border-top:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
                    Total {{ $items->total() }} marquee
                </div>
            @endif
        @endif
    </div>
@endsection
