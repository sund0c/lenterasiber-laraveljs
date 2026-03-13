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

        // Helper: buat URL sort — toggle asc/desc kalau kolom sama
        $sortUrl = function (string $col) use ($sort, $dir, $search) {
            $newDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $col, 'dir' => $newDir, 'q' => $search, 'page' => 1]);
        };

        // Icon sort untuk header
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

    {{-- ── Search bar ─────────────────────────────────────────────── --}}
    <div style="margin-bottom:1rem;display:flex;gap:8px;align-items:center">
        <form method="GET" action="{{ route('admin.komik.index') }}" style="display:flex;gap:8px;flex:1;max-width:460px">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir" value="{{ $dir }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, episode, kategori..."
                class="form-input" style="flex:1;padding:0.45rem 0.75rem;font-size:0.88rem">
            <button type="submit" class="btn-primary"
                style="width:auto;padding:0.45rem 1rem;font-size:0.88rem">Cari</button>
            @if ($search)
                <a href="{{ route('admin.komik.index', ['sort' => $sort, 'dir' => $dir]) }}" class="btn-secondary"
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
                    Tidak ada komik yang cocok dengan <em>"{{ $search }}"</em>.
                    <a href="{{ route('admin.komik.index') }}">Tampilkan semua</a>
                @else
                    Belum ada komik. <a href="{{ route('admin.komik.create') }}">Tambah sekarang</a>.
                @endif
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:60px">Cover</th>
                            <th>
                                <a href="{{ $sortUrl('title') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Judul {!! $sortIcon('title') !!}
                                </a>
                            </th>
                            <th style="width:90px">
                                <a href="{{ $sortUrl('episode_number') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Episode {!! $sortIcon('episode_number') !!}
                                </a>
                            </th>
                            <th style="width:120px">
                                <a href="{{ $sortUrl('category') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Kategori {!! $sortIcon('category') !!}
                                </a>
                            </th>
                            <th style="width:110px">
                                <a href="{{ $sortUrl('published_date') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Tgl Publish {!! $sortIcon('published_date') !!}
                                </a>
                            </th>
                            <th style="width:70px">Instagram</th>
                            <th style="width:80px">
                                <a href="{{ $sortUrl('is_published') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Status {!! $sortIcon('is_published') !!}
                                </a>
                            </th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            @php $locked = !$isAdmin && $item->is_published; @endphp
                            <tr>
                                <td>
                                    @if ($item->cover_image)
                                        <img src="{{ asset('storage/' . $item->cover_image) }}"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:8px">
                                    @else
                                        <div
                                            style="width:48px;height:48px;background:var(--bg);border-radius:8px;border:1px solid var(--border)">
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
                                    @if ($item->description)
                                        <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">
                                            {{ Str::limit($item->description, 60) }}
                                        </div>
                                    @endif
                                </td>
                                <td style="font-size:0.82rem;color:var(--muted)">{{ $item->episode_number ?? '—' }}</td>
                                <td style="font-size:0.82rem;color:var(--muted)">{{ $item->category ?? '—' }}</td>
                                <td style="font-size:0.78rem;color:var(--muted)">
                                    {{ $item->published_date ? \Carbon\Carbon::parse($item->published_date)->format('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    @if ($item->instagram_url)
                                        <a href="{{ $item->instagram_url }}" target="_blank" rel="noopener"
                                            style="font-size:0.78rem;color:var(--accent)">Buka ↗</a>
                                    @else
                                        <span style="color:var(--muted);font-size:0.78rem">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_published)
                                        <span class="badge badge-green">Publik</span>
                                    @else
                                        <span class="badge badge-gray">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:6px">
                                        @if ($locked)
                                            <a href="{{ route('admin.komik.show', $item->id) }}" class="btn-secondary"
                                                style="padding:4px 10px;font-size:0.75rem">Lihat</a>
                                        @else
                                            <a href="{{ route('admin.komik.edit', $item->id) }}" class="btn-secondary"
                                                style="padding:4px 10px;font-size:0.75rem">Edit</a>
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

            {{-- ── Pagination ─────────────────────────────────────── --}}
            @if ($items->lastPage() > 1)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1rem;border-top:1px solid var(--border);font-size:0.82rem;color:var(--muted)">
                    <span>
                        Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }}
                        dari {{ $items->total() }} komik
                    </span>
                    <div style="display:flex;gap:4px;align-items:center">
                        {{-- Prev --}}
                        @if ($items->onFirstPage())
                            <span
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;opacity:0.4">‹</span>
                        @else
                            <a href="{{ $items->previousPageUrl() }}"
                                style="padding:4px 10px;border:1px solid var(--border);border-radius:6px;color:inherit;text-decoration:none">‹</a>
                        @endif

                        {{-- Page numbers --}}
                        @php
                            $current = $items->currentPage();
                            $last = $items->lastPage();
                            $window = 2;
                            $pages = collect(range(1, $last))->filter(
                                fn($p) => $p === 1 || $p === $last || abs($p - $current) <= $window,
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

                        {{-- Next --}}
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
                    Total {{ $items->total() }} komik
                </div>
            @endif

        @endif
    </div>
@endsection
