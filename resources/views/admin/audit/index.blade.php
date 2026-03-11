{{-- resources/views/admin/audit/index.blade.php --}}
@extends('admin.layout')
@section('title','Audit Log')
@section('page-title','Audit Log')
@section('page-sub','Rekaman semua aktivitas admin')

@section('content')
<div class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>Entitas</th><th>IP</th></tr></thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td style="font-size:0.78rem;white-space:nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
          <td>{{ $log->adminUser?->username ?? '—' }}</td>
          <td><code style="font-size:0.75rem">{{ $log->action }}</code></td>
          <td style="font-size:0.78rem;color:var(--muted)">
            {{ $log->entity_type ? $log->entity_type.'#'.$log->entity_id : '—' }}
          </td>
          <td style="font-size:0.78rem;color:var(--muted)">{{ $log->ip_address }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--muted)">Belum ada log.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body">{{ $logs->links() }}</div>
</div>
@endsection
