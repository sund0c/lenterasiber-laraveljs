@extends('admin.layout')
@section('title', ucfirst(strtolower($label)))
@section('page-title',
    match ($label) {
    'JSC' => 'Junior Sentinel Challenge',
    'WORKSHOP' => 'Workshop Lentera',
    'ROADSHOW' => 'Roadshow Lentera',
    'GTL' => 'Lentera Goes to Latsar',
    'TENTANG' => 'Tentang Lentera Siber',
    'KABAR' => 'Kabar Lentera',
    'PODCAST' => 'Podcast Lentera',
    'KOMIK' => 'Komik Lentera',
    default => $label,
    })
@section('page-sub',
    match ($label) {
    'JSC' => 'Kelola halaman Junior Sentinel Challenge',
    'WORKSHOP' => 'Kelola halaman Workshop Lentera',
    'ROADSHOW' => 'Kelola halaman Roadshow Lentera',
    'GTL' => 'Kelola halaman Lentera Goes to Latsar',
    'TENTANG' => 'Kelola halaman Tentang Lentera Siber',
    'KABAR' => 'Kelola halaman Kabar Lentera',
    'PODCAST' => 'Kelola halaman Podcast Lentera',
    'KOMIK' => 'Kelola halaman Komik Lentera',
    default => '',
    })

@section('topbar-actions')
    <a href="{{ route('admin.page.create', $label) }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
        + Tambah
        {{ match ($label) {'JSC' => 'Jsc','WORKSHOP' => 'Workshop','ROADSHOW' => 'Roadshow','GTL' => 'Gtl','TENTANG' => 'Tentang','KABAR' => 'Kabar','PODCAST' => 'Podcast','KOMIK' => 'Komik',default => $label} }}
    </a>
@endsection

@section('content')
    @php
        $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';

        $sortUrl = function (string $col) use ($sort, $dir, $search, $label) {
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
        <form method="GET" action="{{ route('admin.page.index', $label) }}"
            style="display:flex;gap:8px;flex:1;max-width:460px">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="dir" value="{{ $dir }}">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari judul, kategori, excerpt..."
                class="form-input" style="flex:1;padding:0.45rem 0.75rem;font-size:0.88rem">
            <button type="submit" class="btn-primary"
                style="width:auto;padding:0.45rem 1rem;font-size:0.88rem">Cari</button>
            @if ($search)
                <a href="{{ route('admin.page.index', [$label, 'sort' => $sort, 'dir' => $dir]) }}" class="btn-secondary"
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
                    Tidak ada hasil untuk <em>"{{ $search }}"</em>.
                    <a href="{{ route('admin.page.index', $label) }}">Tampilkan semua</a>
                @else
                    Belum ada data. <a href="{{ route('admin.page.create', $label) }}">Tambah sekarang</a>.
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
                                    Judul {!! $sortIcon('title') !!}
                                </a>
                            </th>
                            <th style="width:105px">
                                <a href="{{ $sortUrl('published_date') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Tgl Publish {!! $sortIcon('published_date') !!}
                                </a>
                            </th>
                            <th style="width:80px">
                                <a href="{{ $sortUrl('status') }}"
                                    style="color:inherit;text-decoration:none;white-space:nowrap">
                                    Status {!! $sortIcon('status') !!}
                                </a>
                            </th>
                            <th style="width:60px">Editor</th>
                            <th style="width:180px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            @php $locked = !$isAdmin && $item->status === 'published'; @endphp
                            <tr>

                                <td>
                                    <div style="font-weight:600;font-size:0.88rem">{{ $item->title }}</div>
                                    @if ($item->excerpt)
                                        <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">
                                            {{ Str::limit($item->excerpt, 65) }}
                                        </div>
                                    @endif
                                    @if ($item->slug)
                                        <div
                                            style="font-size:0.7rem;color:var(--border);margin-top:2px;font-family:monospace">
                                            {{ $item->slug }}
                                        </div>
                                    @endif
                                </td>


                                <td style="font-size:0.78rem;color:var(--muted)">
                                    {{ $item->published_date ? \Carbon\Carbon::parse($item->published_date)->format('d/m/Y') : '—' }}
                                </td>
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
                                    <div style="display:flex;gap:4px;flex-wrap:wrap">
                                        @if ($locked)
                                            <a href="{{ route('admin.page.show', [$label, $item->id]) }}"
                                                class="btn-secondary" style="padding:4px 8px;font-size:0.72rem">Lihat</a>
                                        @else
                                            <a href="{{ route('admin.page.edit', [$label, $item->id]) }}"
                                                class="btn-secondary" style="padding:4px 8px;font-size:0.72rem">Edit</a>

                                            <button type="button" class="btn-danger btn-delete"
                                                data-action="{{ route('admin.page.destroy', [$label, $item->id]) }}"
                                                style="padding:4px 8px;font-size:0.72rem">Hapus</button>
                                        @endif
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
                        data</span>
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
                    Total {{ $items->total() }} data
                </div>
            @endif
        @endif
    </div>
@endsection
