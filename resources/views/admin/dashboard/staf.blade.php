@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-sub', 'Selamat datang, ' . $user->full_name)

@section('content')

{{-- ── Info role ────────────────────────────────────────── --}}
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:0.9rem 1.2rem;margin-bottom:1.5rem;font-size:0.84rem;color:#1e40af">
  ℹ️ Anda login sebagai <strong>Staf</strong>. Data yang ditambahkan akan berstatus <strong>draft</strong> dan perlu diapprove Admin untuk dipublish.
</div>

{{-- ── Stat Cards ───────────────────────────────────────── --}}
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
        <a href="{{ route('admin.konten.index', $item['key']) }}"
          style="font-size:0.75rem;font-weight:600;color:{{ $item['color'] }};background:{{ $item['color'] }}18;padding:3px 10px;border-radius:20px;text-decoration:none">
          {{ $item['label'] }} →
        </a>
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

{{-- ── Aksi Cepat ──────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem">

  <div class="card">
    <div class="card-header"><strong>⚡ Tambah Konten</strong></div>
    <div class="card-body" style="padding:1rem;display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
      <a href="{{ route('admin.konten.create', 'KABAR') }}"   class="btn-primary" style="text-align:center;font-size:0.78rem;width:auto">+ Artikel</a>
      <a href="{{ route('admin.konten.create', 'PODCAST') }}" class="btn-primary" style="text-align:center;font-size:0.78rem;width:auto">+ Podcast</a>
      <a href="{{ route('admin.konten.create', 'KOMIK') }}"   class="btn-primary" style="text-align:center;font-size:0.78rem;width:auto">+ Komik</a>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><strong>⏳ Draft Saya</strong></div>
    <div class="card-body" style="padding:1rem">
      @php
        $myDrafts = collect([
          ['label'=>'Kabar',   'count'=>$stats['kabar']['draft'],   'key'=>'KABAR'],
          ['label'=>'Podcast', 'count'=>$stats['podcast']['draft'], 'key'=>'PODCAST'],
          ['label'=>'Komik',   'count'=>$stats['komik']['draft'],   'key'=>'KOMIK'],
        ])->filter(fn($d) => $d['count'] > 0);
      @endphp
      @forelse($myDrafts as $d)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid var(--border)">
          <span style="font-size:0.85rem">{{ $d['label'] }}</span>
          <a href="{{ route('admin.konten.index', $d['key']) }}"
            style="font-size:0.78rem;background:#fef3c720;color:#f59e0b;padding:2px 10px;border-radius:4px;border:1px solid #fbbf24;text-decoration:none">
            {{ $d['count'] }} draft →
          </a>
        </div>
      @empty
        <p style="font-size:0.82rem;color:var(--muted);margin:0">Tidak ada draft. Semua sudah dipublish admin. 🎉</p>
      @endforelse
    </div>
  </div>

</div>

{{-- ── Aktivitas Saya ──────────────────────────────────── --}}
<div class="card">
  <div class="card-header"><strong>🕓 Aktivitas Terakhir Saya</strong></div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Waktu</th>
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
        <tr><td colspan="4" style="text-align:center;padding:1.5rem;color:var(--muted)">Belum ada aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
