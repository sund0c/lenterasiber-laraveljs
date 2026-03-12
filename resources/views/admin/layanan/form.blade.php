@extends('admin.layout')
@section('title', $item ? 'Edit Layanan' : 'Tambah Layanan')
@section('page-title', $item ? 'Edit Layanan' : 'Tambah Layanan')
@section('page-sub', 'Data layanan yang tampil di halaman publik')

@section('content')
<div style="max-width:760px">
  <form
    method="POST"
    action="{{ $item ? route('admin.layanan.update', $item->id) : route('admin.layanan.store') }}"
    enctype="multipart/form-data"
  >
    @csrf
    @if($item) @method('PUT') @endif

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:1.2rem">
        <ul style="margin:0;padding-left:1rem">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif

    {{-- ── IDENTITAS ─────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Identitas Program</strong></div>
      <div class="card-body">

        <div class="form-row">
          <div class="form-group">
            <label>Nomor Program</label>
            <input type="text" name="program_number" class="form-input"
              value="{{ old('program_number', $item->program_number ?? '01') }}"
              placeholder="01" maxlength="10">
            <p class="field-hint">Tampil sebagai "PROGRAM 01" (khusus Workshop)</p>
          </div>
          <div class="form-group">
            <label>Urutan Tampil</label>
            <input type="number" name="sort_order" class="form-input"
              value="{{ old('sort_order', $item->sort_order ?? 0) }}"
              min="0" max="99">
          </div>
        </div>

        <div class="form-group">
          <label>Style Kartu <span style="color:var(--red)">*</span></label>
          <select name="card_style" id="card_style" class="form-input">
            <option value="default"  {{ old('card_style', $item->card_style ?? '') === 'default'  ? 'selected' : '' }}>Default</option>
            <option value="workshop" {{ old('card_style', $item->card_style ?? '') === 'workshop' ? 'selected' : '' }}>Workshop (highlight biru + fitur list)</option>
            <option value="roadshow" {{ old('card_style', $item->card_style ?? '') === 'roadshow' ? 'selected' : '' }}>Roadshow OPD (ikon + box target)</option>
            <option value="latsar"   {{ old('card_style', $item->card_style ?? '') === 'latsar'   ? 'selected' : '' }}>Goes to Latsar (ikon + box jadwal)</option>
            <option value="sentinel" {{ old('card_style', $item->card_style ?? '') === 'sentinel' ? 'selected' : '' }}>Junior Sentinel (stats + CTA button)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Section Label</label>
          <input type="text" name="section_label" class="form-input"
            value="{{ old('section_label', $item->section_label ?? '') }}"
            placeholder="KOMPETISI TAHUNAN atau PROGRAM LAINNYA" maxlength="80">
          <p class="field-hint">Teks kecil di atas judul</p>
        </div>

        <div class="form-group">
          <label>Judul Lengkap <span style="color:var(--red)">*</span></label>
          <input type="text" name="title" class="form-input @error('title') is-error @enderror"
            value="{{ old('title', $item->title ?? '') }}"
            placeholder="Workshop Keamanan Informasi">
          <p class="field-hint">Judul lengkap untuk API dan SEO</p>
        </div>

        {{-- Workshop only --}}
        <div class="form-group">
          <label>Judul — Bagian Normal <span style="color:var(--muted);font-weight:normal">(Workshop)</span></label>
          <input type="text" name="title_plain" class="form-input"
            value="{{ old('title_plain', $item->title_plain ?? '') }}"
            placeholder="Workshop">
        </div>

        <div class="form-group">
          <label>Judul — Bagian Highlight <span style="color:var(--accent)">(biru)</span> <span style="color:var(--muted);font-weight:normal">(Workshop)</span></label>
          <input type="text" name="title_highlight" class="form-input"
            value="{{ old('title_highlight', $item->title_highlight ?? '') }}"
            placeholder="Keamanan Informasi">
        </div>

        <div class="form-group">
          <label>Badge Label</label>
          <input type="text" name="badge_label" class="form-input"
            value="{{ old('badge_label', $item->badge_label ?? '') }}"
            placeholder="WORKSHOP // ACTIVE" maxlength="60">
          <p class="field-hint">Teks kecil di bawah ikon</p>
        </div>

      </div>
    </div>

    {{-- ── KONTEN ────────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Konten</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Deskripsi Singkat</label>
          <textarea name="short_desc" class="form-input" rows="3"
            placeholder="Deskripsi singkat layanan...">{{ old('short_desc', $item->short_desc ?? '') }}</textarea>
          <p class="field-hint">Maks 500 karakter. Tampil di kartu layanan.</p>
        </div>

        {{-- Fitur list — Workshop --}}
        <div class="form-group">
          <label>Fitur / Badge <span style="color:var(--muted);font-weight:normal">(Workshop — centang hijau)</span></label>
          <div id="features-list">
            @php
              $features = old('features', isset($item->features)
                ? (is_array($item->features) ? $item->features : json_decode($item->features, true))
                : []);
              $features = $features ?: [''];
            @endphp
            @foreach($features as $f)
            <div class="feature-row" style="display:flex;gap:8px;margin-bottom:8px">
              <input type="text" name="features[]" class="form-input"
                value="{{ $f }}" placeholder="Contoh: Hands-on practice" style="flex:1">
              <button type="button" class="btn-danger feature-remove"
                style="padding:0 12px;flex-shrink:0">×</button>
            </div>
            @endforeach
          </div>
          <button type="button" id="addFeature" class="btn-secondary" style="margin-top:4px">+ Tambah Fitur</button>
        </div>

        {{-- Box info — Roadshow & Latsar --}}
        <div class="form-row">
          <div class="form-group">
            <label>Box Label <span style="color:var(--muted);font-weight:normal">(Roadshow/Latsar)</span></label>
            <input type="text" name="box_label" class="form-input"
              value="{{ old('box_label', $item->box_label ?? '') }}"
              placeholder="JADWAL TERDEKAT" maxlength="80">
          </div>
          <div class="form-group">
            <label>Box Value</label>
            <input type="text" name="box_value" class="form-input"
              value="{{ old('box_value', $item->box_value ?? '') }}"
              placeholder="Latsar Gel. III — 2 Mei 2025" maxlength="200">
          </div>
        </div>

        {{-- Target — Roadshow --}}
        <div class="form-row">
          <div class="form-group">
            <label>Target Label <span style="color:var(--muted);font-weight:normal">(Roadshow)</span></label>
            <input type="text" name="target_label" class="form-input"
              value="{{ old('target_label', $item->target_label ?? '') }}"
              placeholder="TARGET 2025" maxlength="60">
          </div>
          <div class="form-group">
            <label>Target Value</label>
            <input type="text" name="target_value" class="form-input"
              value="{{ old('target_value', $item->target_value ?? '') }}"
              placeholder="42 OPD Pemprov Bali" maxlength="150">
          </div>
        </div>

        {{-- Stats — Junior Sentinel --}}
        <div class="form-group">
          <label>Stats <span style="color:var(--muted);font-weight:normal">(Junior Sentinel — maks 3)</span></label>
          <div id="stats-list">
            @php
              $stats = old('stats', isset($item->stats)
                ? (is_array($item->stats) ? $item->stats : json_decode($item->stats, true))
                : []);
              $stats = $stats ?: [['value'=>'','label'=>'']];
            @endphp
            @foreach($stats as $i => $s)
            <div class="stat-row" style="display:flex;gap:8px;margin-bottom:8px">
              <input type="text" name="stats[{{ $i }}][value]" class="form-input"
                value="{{ $s['value'] ?? '' }}" placeholder="30 APR" style="flex:1">
              <input type="text" name="stats[{{ $i }}][label]" class="form-input"
                value="{{ $s['label'] ?? '' }}" placeholder="BATAS DAFTAR" style="flex:1">
              <button type="button" class="btn-danger stat-remove" style="padding:0 12px;flex-shrink:0">×</button>
            </div>
            @endforeach
          </div>
          <button type="button" id="addStat" class="btn-secondary" style="margin-top:4px">+ Tambah Stat</button>
        </div>

        {{-- CTA — Junior Sentinel --}}
        <div class="form-row">
          <div class="form-group">
            <label>Teks Tombol CTA <span style="color:var(--muted);font-weight:normal">(Junior Sentinel)</span></label>
            <input type="text" name="cta_text" class="form-input"
              value="{{ old('cta_text', $item->cta_text ?? '') }}"
              placeholder="DAFTAR SEKARANG" maxlength="80">
          </div>
          <div class="form-group">
            <label>URL Tombol CTA</label>
            <input type="url" name="cta_url" class="form-input"
              value="{{ old('cta_url', $item->cta_url ?? '') }}"
              placeholder="https://...">
          </div>
        </div>

        <div class="form-group">
          <label>Konten Lengkap <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
          <textarea name="full_content" class="form-input" rows="4"
            placeholder="Deskripsi lengkap...">{{ old('full_content', $item->full_content ?? '') }}</textarea>
        </div>

      </div>
    </div>

    {{-- ── GAMBAR & STATUS ───────────────────────────────── --}}
    <div class="card" style="margin-bottom:1rem">
      <div class="card-header"><strong>Gambar & Status</strong></div>
      <div class="card-body">

        <div class="form-group">
          <label>Gambar / Ikon Layanan</label>
          @if(isset($item) && $item->cover_image)
            <div style="margin-bottom:8px">
              <img src="{{ asset('storage/' . $item->cover_image) }}"
                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
              <p class="field-hint">Upload baru untuk mengganti.</p>
            </div>
          @endif
          <input type="file" name="cover_image" class="form-input"
            accept="image/jpeg,image/png,image/webp,image/svg+xml">
          <p class="field-hint">JPG, PNG, WebP, SVG. Maks 2MB.</p>
        </div>

        <div class="form-group">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
              {{ old('is_active', $item->is_active ?? true) ? 'checked' : '' }}
              style="width:16px;height:16px;cursor:pointer">
            Layanan Aktif (tampil di halaman publik)
          </label>
        </div>

      </div>
    </div>

    <div style="display:flex;gap:8px">
      <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
        {{ $item ? 'Simpan Perubahan' : 'Simpan Layanan' }}
      </button>
      <a href="{{ route('admin.layanan.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {

  // ── Fitur list ─────────────────────────────────────────
  document.getElementById('addFeature').addEventListener('click', function() {
    var list = document.getElementById('features-list');
    var row  = document.createElement('div');
    row.className = 'feature-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';
    row.innerHTML =
      '<input type="text" name="features[]" class="form-input" placeholder="Contoh: Sertifikasi kehadiran" style="flex:1">' +
      '<button type="button" class="btn-danger feature-remove" style="padding:0 12px;flex-shrink:0">×</button>';
    list.appendChild(row);
    row.querySelector('input').focus();
  });

  document.getElementById('features-list').addEventListener('click', function(e) {
    if (e.target.classList.contains('feature-remove')) {
      var rows = document.querySelectorAll('.feature-row');
      if (rows.length > 1) {
        e.target.closest('.feature-row').remove();
      } else {
        e.target.closest('.feature-row').querySelector('input').value = '';
      }
    }
  });

  // ── Stats list ─────────────────────────────────────────
  var statCount = document.querySelectorAll('.stat-row').length;

  document.getElementById('addStat').addEventListener('click', function() {
    if (statCount >= 3) { alert('Maksimal 3 stat.'); return; }
    var list = document.getElementById('stats-list');
    var row  = document.createElement('div');
    row.className = 'stat-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px';
    row.innerHTML =
      '<input type="text" name="stats[' + statCount + '][value]" class="form-input" placeholder="50+" style="flex:1">' +
      '<input type="text" name="stats[' + statCount + '][label]" class="form-input" placeholder="PESERTA TARGET" style="flex:1">' +
      '<button type="button" class="btn-danger stat-remove" style="padding:0 12px;flex-shrink:0">×</button>';
    list.appendChild(row);
    statCount++;
  });

  document.getElementById('stats-list').addEventListener('click', function(e) {
    if (e.target.classList.contains('stat-remove')) {
      var rows = document.querySelectorAll('.stat-row');
      if (rows.length > 1) {
        e.target.closest('.stat-row').remove();
        statCount--;
      } else {
        e.target.closest('.stat-row').querySelectorAll('input').forEach(function(i) { i.value = ''; });
      }
    }
  });

});
</script>
@endsection
