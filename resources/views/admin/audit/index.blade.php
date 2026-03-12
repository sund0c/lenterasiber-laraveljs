@extends('admin.layout')
@section('title', 'Audit Log')
@section('page-title', 'Audit Log')
@section('page-sub', 'Rekaman semua aktivitas admin')

@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:150px">Waktu</th>
          <th style="width:120px">Pengguna</th>
          <th>Aksi</th>
          <th>Entitas</th>
          <th style="width:120px">IP</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-size:0.78rem;white-space:nowrap;color:var(--muted)">
            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
          </td>
          <td style="font-size:0.82rem">{{ $log->username ?? '—' }}</td>
          <td>
            @php
              $color = str_contains($log->action, 'delete') ? '#ef4444'
                : (str_contains($log->action, 'create') ? '#10b981'
                : (str_contains($log->action, 'login') ? '#3b82f6'
                : '#f59e0b'));
            @endphp
            <code style="font-size:0.75rem;background:{{ $color }}18;color:{{ $color }};padding:2px 7px;border-radius:4px">
              {{ $log->action }}
            </code>
          </td>
          <td style="font-size:0.78rem;color:var(--muted)">
            {{ $log->entity_type ? $log->entity_type . ' #' . $log->entity_id : '—' }}
          </td>
          <td style="font-size:0.78rem;color:var(--muted);font-family:monospace">
            {{ $log->ip_address ?? '—' }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">
            Belum ada log.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination custom --}}
  @if($logs->lastPage() > 1)
  <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.2rem;border-top:1px solid var(--border)">
    <div style="font-size:0.78rem;color:var(--muted)">
      Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
    </div>
    <div style="display:flex;gap:4px">
      {{-- Prev --}}
      @if($logs->onFirstPage())
        <span style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--border)">← Prev</span>
      @else
        <a href="{{ $logs->previousPageUrl() }}"
          style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--text);text-decoration:none">← Prev</a>
      @endif

      {{-- Page numbers --}}
      @php
        $current  = $logs->currentPage();
        $last     = $logs->lastPage();
        $start    = max(1, $current - 2);
        $end      = min($last, $current + 2);
      @endphp

      @if($start > 1)
        <a href="{{ $logs->url(1) }}"
          style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--text);text-decoration:none">1</a>
        @if($start > 2)
          <span style="padding:5px 6px;font-size:0.78rem;color:var(--muted)">…</span>
        @endif
      @endif

      @for($p = $start; $p <= $end; $p++)
        @if($p === $current)
          <span style="padding:5px 10px;border:1px solid var(--accent);border-radius:6px;font-size:0.78rem;background:var(--accent);color:#fff">{{ $p }}</span>
        @else
          <a href="{{ $logs->url($p) }}"
            style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--text);text-decoration:none">{{ $p }}</a>
        @endif
      @endfor

      @if($end < $last)
        @if($end < $last - 1)
          <span style="padding:5px 6px;font-size:0.78rem;color:var(--muted)">…</span>
        @endif
        <a href="{{ $logs->url($last) }}"
          style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--text);text-decoration:none">{{ $last }}</a>
      @endif

      {{-- Next --}}
      @if($logs->hasMorePages())
        <a href="{{ $logs->nextPageUrl() }}"
          style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--text);text-decoration:none">Next →</a>
      @else
        <span style="padding:5px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.78rem;color:var(--border)">Next →</span>
      @endif
    </div>
  </div>
  @endif

</div>
@endsection
