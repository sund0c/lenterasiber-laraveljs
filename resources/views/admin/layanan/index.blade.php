@extends('admin.layout')
@section('title', 'Layanan')
@section('page-title', 'Layanan')
@section('page-sub', 'Kelola data layanan yang tampil di halaman publik')

@section('topbar-actions')
  <a href="{{ route('admin.layanan.create') }}" class="btn-primary" style="width:auto;padding:0.5rem 1.2rem">
    + Tambah Layanan
  </a>
@endsection

@section('content')
<div class="card">
  @if($items->isEmpty())
    <div class="card-body" style="text-align:center;padding:3rem;color:var(--muted)">
      Belum ada layanan. <a href="{{ route('admin.layanan.create') }}">Tambah sekarang</a>.
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <th style="width:60px">Cover</th>
          <th>Judul</th>
          <th>Style</th>
          <th>Fitur / Info</th>
          <th>Status</th>
          <th style="width:60px">Urut</th>
          <th style="width:120px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
        <tr>
          <td style="color:var(--muted);font-size:0.8rem">{{ $item->program_number }}</td>

          <td>
            @if($item->cover_image)
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:44px;height:44px;object-fit:cover;border-radius:6px">
            @else
              <div style="width:44px;height:44px;background:var(--bg);border-radius:6px;border:1px solid var(--border)"></div>
            @endif
          </td>

          <td>
            <div style="font-weight:600;font-size:0.88rem">
              @if($item->title_plain)
                {{ $item->title_plain }}
                @if($item->title_highlight)
                  <span style="color:var(--accent)">{{ $item->title_highlight }}</span>
                @endif
              @else
                {{ $item->title }}
              @endif
            </div>
            @if($item->short_desc)
              <div style="font-size:0.75rem;color:var(--muted);margin-top:2px">
                {{ Str::limit($item->short_desc, 60) }}
              </div>
            @endif
          </td>

          <td>
            @php
              $styleLabels = [
                'workshop' => ['label' => 'Workshop',  'color' => '#3b82f6'],
                'roadshow' => ['label' => 'Roadshow',  'color' => '#10b981'],
                'latsar'   => ['label' => 'Latsar',    'color' => '#8b5cf6'],
                'sentinel' => ['label' => 'Sentinel',  'color' => '#f59e0b'],
                'default'  => ['label' => 'Default',   'color' => '#6b7280'],
              ];
              $style = $styleLabels[$item->card_style] ?? $styleLabels['default'];
            @endphp
            <span style="font-size:0.72rem;font-weight:600;padding:2px 8px;border-radius:4px;background:{{ $style['color'] }}20;color:{{ $style['color'] }}">
              {{ $style['label'] }}
            </span>
          </td>

          <td style="font-size:0.75rem;color:var(--muted)">
            @php
              $featureList = [];
              if (!empty($item->features)) {
                $decoded = json_decode($item->features, true);
                if (is_array($decoded)) $featureList = $decoded;
              }
            @endphp
            @if($featureList)
              @foreach($featureList as $f)
                <div>✓ {{ $f }}</div>
              @endforeach
            @elseif($item->box_label)
              <div style="font-size:0.72rem">
                <span style="color:var(--muted)">{{ $item->box_label }}:</span>
                {{ $item->box_value }}
              </div>
            @elseif($item->stats)
              @php
                $statList = json_decode($item->stats, true) ?? [];
              @endphp
              @foreach($statList as $s)
                <div>{{ $s['value'] ?? '' }} {{ $s['label'] ?? '' }}</div>
              @endforeach
            @else
              <span style="color:var(--border)">—</span>
            @endif
          </td>

          <td>
            @if($item->is_active)
              <span class="badge badge-green">Aktif</span>
            @else
              <span class="badge badge-gray">Nonaktif</span>
            @endif
          </td>

          <td style="font-size:0.85rem;color:var(--muted)">{{ $item->sort_order }}</td>

          <td>
            <div style="display:flex;gap:6px">
              <a href="{{ route('admin.layanan.edit', $item->id) }}"
                class="btn-secondary" style="padding:4px 10px;font-size:0.75rem">Edit</a>
              <button type="button" class="btn-danger btn-delete"
                data-action="{{ route('admin.layanan.destroy', $item->id) }}"
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
