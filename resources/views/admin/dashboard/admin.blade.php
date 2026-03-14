@extends('admin.layout')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-sub', 'Selamat datang, ' . $user->full_name)

@section('content')

{{-- ── Stat Cards Konten ───────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">

  @foreach([
    ['label'=>'Kabar Lentera', 'icon'=>'📰', 'data'=>$stats['kabar'],  'color'=>'#3b82f6', 'key'=>'KABAR'],
    ['label'=>'Podcast',       'icon'=>'🎙', 'data'=>$stats['podcast'],'color'=>'#8b5cf6', 'key'=>'PODCAST'],
    ['label'=>'Komik',         'icon'=>'📖', 'data'=>$stats['komik'],  'color'=>'#10b981', 'key'=>'KOMIK'],
  ] as $item)
  <div class="card" style="border-top:3px solid {{ $item['color'] }}">
    <div class="card-body" style="padding:1.2rem">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
        <span style="font-size:1.4rem">{{ $item['icon'] }}</span>
        <span style="font-size:0.75rem;font-weight:600;color:{{ $item['color'] }};background:{{ $item['color'] }}18;padding:3px 10px;border-radius:20px">
          {{ $item['label'] }}
        </span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;text-align:center">
        <div>
          <div style="font-size:1.6rem;font-weight:700;color:var(--text)">{{ $item['data']['total'] }}</div>
          <div style="font-size:0.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Total</div>
        </div>
        <div>
          <div style="font-size:1.6rem;font-weight:700;color:#10b981">{{ $item['data']['published'] }}</div>
          <div style="font-size:0.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Publish</div>
        </div>
        <div>
          <div style="font-size:1.6rem;font-weight:700;color:#f59e0b">{{ $item['data']['draft'] }}</div>
          <div style="font-size:0.68rem;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Draft</div>
        </div>
      </div>
    </div>
  </div>
  @endforeach

</div>

{{-- ── Stat Cards Lainnya ──────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem">

  <div class="card">
    <div class="card-body" style="padding:1.2rem;display:flex;align-items:center;gap:1rem">
      <span style="font-size:2rem">🎓</span>
      <div>
        <div style="font-size:1.8rem;font-weight:700">{{ $stats['workshop'] }}</div>
        <div style="font-size:0.78rem;color:var(--muted)">Workshop</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body" style="padding:1.2rem;display:flex;align-items:center;gap:1rem">
      <span style="font-size:2rem">👥</span>
      <div>
        <div style="font-size:1.8rem;font-weight:700">{{ $stats['staf'] }}</div>
        <div style="font-size:0.78rem;color:var(--muted)">Akun Staf</div>
      </div>
    </div>
  </div>

</div>

{{-- ── Aksi Cepat ──────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem">

  {{-- Draft menunggu publish --}}
  <div class="card">
    <div class="card-header"><strong>⏳ Draft Menunggu Publish</strong></div>
    <div class="card-body" style="padding:1rem">
      @php
        $drafts = collect([
          ['label'=>'Kabar',   'count'=>$stats['kabar']['draft'],   'key'=>'KABAR'],
          ['label'=>'Podcast', 'count'=>$stats['podcast']['draft'], 'key'=>'PODCAST'],
          ['label'=>'Komik',   'count'=>$stats['komik']['draft'],   'key'=>'KOMIK'],
        ])->filter(fn($d) => $d['count'] > 0);
      @endphp
      @forelse($drafts as $d)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)">
          <span style="font-size:0.85rem">{{ $d['label'] }}</span>
          <a href="{{ route('admin.konten.index', $d['key']) }}"
            style="font-size:0.78rem;background:#fef3c720;color:#f59e0b;padding:2px 10px;border-radius:4px;border:1px solid #fbbf24;text-decoration:none">
            {{ $d['count'] }} draft →
          </a>
        </div>
      @empty
        <p style="font-size:0.82rem;color:var(--muted);margin:0">Semua konten sudah dipublish. 🎉</p>
      @endforelse
    </div>
  </div>

  {{-- Akses cepat --}}
  <div class="card">
    <div class="card-header"><strong>⚡ Akses Cepat</strong></div>
    <div class="card-body" style="padding:1rem;display:grid;grid-template-columns:1fr 1fr;gap:8px">
      <a href="{{ route('admin.konten.create', 'KABAR') }}"   class="btn-secondary" style="text-align:center;font-size:0.78rem">+ Artikel</a>
      <a href="{{ route('admin.konten.create', 'PODCAST') }}" class="btn-secondary" style="text-align:center;font-size:0.78rem">+ Podcast</a>
      <a href="{{ route('admin.konten.create', 'KOMIK') }}"   class="btn-secondary" style="text-align:center;font-size:0.78rem">+ Komik</a>
      <a href="{{ route('admin.users.create') }}"              class="btn-secondary" style="text-align:center;font-size:0.78rem">+ Staf</a>
    </div>
  </div>

</div>

{{-- ── Audit Log Terbaru ───────────────────────────────── --}}
<div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
    <strong>🔍 Aktivitas Terbaru</strong>
    <a href="{{ route('admin.audit.index') }}" style="font-size:0.78rem;color:var(--accent)">Lihat semua →</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Waktu</th>
          <th>Pengguna</th>
          <th>Aksi</th>
          <th>Entitas</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentAudit as $log)
        <tr>
          <td style="font-size:0.75rem;white-space:nowrap;color:var(--muted)">
            {{ \Carbon\Carbon::parse($log->created_at)->format('d/m H:i') }}
          </td>
          <td style="font-size:0.78rem">{{ $log->username ?? '—' }}</td>
          <td>
            @php
              $c = str_contains($log->action,'delete') ? '#ef4444'
                : (str_contains($log->action,'create') ? '#10b981'
                : (str_contains($log->action,'login')  ? '#3b82f6' : '#f59e0b'));
            @endphp
            <code style="font-size:0.72rem;background:{{ $c }}18;color:{{ $c }};padding:2px 6px;border-radius:4px">{{ $log->action }}</code>
          </td>
          <td style="font-size:0.75rem;color:var(--muted)">{{ $log->entity_type ? $log->entity_type.'#'.$log->entity_id : '—' }}</td>
          <td style="font-size:0.75rem;color:var(--muted);font-family:monospace">{{ $log->ip_address ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;padding:1.5rem;color:var(--muted)">Belum ada aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
