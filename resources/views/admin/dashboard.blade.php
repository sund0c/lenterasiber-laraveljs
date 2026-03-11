@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Selamat datang, ' . $currentUser->full_name)

@section('content')
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg></div>
    <div class="stat-label">Kabar Lentera</div>
    <div class="stat-value">{{ $stats['kabar'] }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></div>
    <div class="stat-label">Layanan</div>
    <div class="stat-value">{{ $stats['layanan'] }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/></svg></div>
    <div class="stat-label">Workshop</div>
    <div class="stat-value">{{ $stats['workshop'] }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/></svg></div>
    <div class="stat-label">Pesan Belum Dibaca</div>
    <div class="stat-value">{{ $stats['pesan'] }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 18v-6a9 9 0 0 1 18 0v6"/></svg></div>
    <div class="stat-label">Podcast</div>
    <div class="stat-value">{{ $stats['podcast'] }}</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg></div>
    <div class="stat-label">Komik</div>
    <div class="stat-value">{{ $stats['komik'] }}</div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <strong>Aktivitas Terbaru</strong>
    <a href="{{ route('admin.audit.index') }}">Lihat semua →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Waktu</th><th>Pengguna</th><th>Aksi</th><th>IP</th></tr></thead>
      <tbody>
        @forelse($recentAudit as $log)
        <tr>
          <td style="font-size:0.78rem;color:var(--muted)">{{ $log->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $log->adminUser?->username ?? '—' }}</td>
          <td><code style="font-size:0.78rem">{{ $log->action }}</code></td>
          <td style="font-size:0.78rem;color:var(--muted)">{{ $log->ip_address }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:2rem">Belum ada aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
