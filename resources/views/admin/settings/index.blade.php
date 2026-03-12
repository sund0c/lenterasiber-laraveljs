@extends('admin.layout')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-sub', 'Kelola konfigurasi situs Lentera Siber')

@section('content')

    @php
        $s = $settings; // shorthand
        $get = fn($key, $default = '') => $s->get($key)?->value ?? $default;
        $tab = request('tab', 'umum');
    @endphp

    @if (session('success'))
        <div
            style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:8px;padding:0.8rem 1.2rem;margin-bottom:1.2rem;font-size:0.85rem;color:#065f46">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- ── Tab Navigation ───────────────────────────────────── --}}
    <div style="display:flex;gap:4px;border-bottom:2px solid var(--border);margin-bottom:1.5rem">
        @foreach ([['key' => 'umum', 'label' => '⚙️ Umum'], ['key' => 'medsos', 'label' => '📱 Media Sosial'], ['key' => 'halaman', 'label' => '📄 Halaman Statis'], ['key' => 'statistik', 'label' => '📊 Statistik']] as $t)
            <a href="?tab={{ $t['key'] }}"
                style="padding:0.5rem 1rem;font-size:0.84rem;font-weight:600;text-decoration:none;border-bottom:2px solid {{ $tab === $t['key'] ? 'var(--accent)' : 'transparent' }};color:{{ $tab === $t['key'] ? 'var(--accent)' : 'var(--muted)' }};margin-bottom:-2px">
                {{ $t['label'] }}
            </a>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf

        {{-- ── TAB: UMUM ─────────────────────────────────────── --}}
        @if ($tab === 'umum')
            <div class="card" style="max-width:600px">
                <div class="card-header"><strong>Informasi Situs</strong></div>
                <div class="card-body">

                    <div class="form-group">
                        <label>Nama Situs</label>
                        <input type="text" name="site_name" class="form-input"
                            value="{{ old('site_name', $get('site_name', 'Lentera Siber')) }}" placeholder="Lentera Siber"
                            maxlength="150">
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Singkat</label>
                        <input type="text" name="site_description" class="form-input"
                            value="{{ old('site_description', $get('site_description')) }}"
                            placeholder="Platform literasi keamanan siber ASN Pemprov Bali" maxlength="300">
                        <p class="field-hint">Tampil di meta description dan footer.</p>
                    </div>

                    <div class="form-group">
                        <label>Email Kontak</label>
                        <input type="email" name="contact_email" class="form-input"
                            value="{{ old('contact_email', $get('contact_email')) }}" placeholder="baliprovcsirt@gmail.com">
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon / WhatsApp</label>
                        <input type="text" name="contact_phone" class="form-input"
                            value="{{ old('contact_phone', $get('contact_phone')) }}" placeholder="+62 361 xxxxxxx"
                            maxlength="30">
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="contact_address" class="form-input" rows="2" placeholder="Jl. Basuki Rahmat No.1, Denpasar, Bali"
                            maxlength="300">{{ old('contact_address', $get('contact_address')) }}</textarea>
                    </div>

                </div>
            </div>
        @endif

        {{-- ── TAB: MEDIA SOSIAL ─────────────────────────────── --}}
        @if ($tab === 'medsos')
            <div class="card" style="max-width:600px">
                <div class="card-header"><strong>Link Media Sosial</strong></div>
                <div class="card-body">

                    @foreach ([['key' => 'social_instagram', 'label' => 'Instagram', 'icon' => '📸', 'placeholder' => 'https://instagram.com/...'], ['key' => 'social_twitter', 'label' => 'Twitter / X', 'icon' => '🐦', 'placeholder' => 'https://x.com/...'], ['key' => 'social_youtube', 'label' => 'YouTube', 'icon' => '▶️', 'placeholder' => 'https://youtube.com/@...'], ['key' => 'social_facebook', 'label' => 'Facebook', 'icon' => '📘', 'placeholder' => 'https://facebook.com/...'], ['key' => 'social_linkedin', 'label' => 'LinkedIn', 'icon' => '💼', 'placeholder' => 'https://linkedin.com/in/...']] as $field)
                        <div class="form-group">
                            <label>{{ $field['icon'] }} {{ $field['label'] }}</label>
                            <input type="url" name="{{ $field['key'] }}"
                                class="form-input @error($field['key']) is-error @enderror"
                                value="{{ old($field['key'], $get($field['key'])) }}"
                                placeholder="{{ $field['placeholder'] }}">
                        </div>
                    @endforeach

                </div>
            </div>
        @endif

        {{-- ── TAB: HALAMAN STATIS ───────────────────────────── --}}
        @if ($tab === 'halaman')
            <div style="display:grid;gap:1.5rem">

                <div class="card">
                    <div class="card-header"><strong>📄 Syarat & Ketentuan</strong></div>
                    <div class="card-body">
                        <p class="field-hint" style="margin-bottom:0.8rem">Konten ini tampil di halaman /syarat-ketentuan di
                            situs publik. Mendukung format HTML dasar.</p>
                        <div class="form-group">
                            <label>Berlaku Sejak</label>
                            <input type="text" name="page_tos_date" class="form-input"
                                value="{{ old('page_tos_date', $get('page_tos_date')) }}" placeholder="1 Januari 2025"
                                maxlength="50">
                        </div>
                        <textarea name="page_tos" class="form-input" rows="16" placeholder="<h2>Syarat dan Ketentuan</h2>&#10;<p>...</p>"
                            style="font-family:monospace;font-size:0.82rem">{{ old('page_tos', $get('page_tos')) }}</textarea>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><strong>🔒 Kebijakan Privasi</strong></div>
                    <div class="card-body">
                        <p class="field-hint" style="margin-bottom:0.8rem">Konten ini tampil di halaman /kebijakan-privasi
                            di situs publik. Mendukung format HTML dasar.</p>
                        {{-- Kebijakan Privasi --}}
                        <div class="form-group">
                            <label>Berlaku Sejak</label>
                            <input type="text" name="page_privacy_date" class="form-input"
                                value="{{ old('page_privacy_date', $get('page_privacy_date')) }}"
                                placeholder="1 Januari 2025" maxlength="50">
                        </div>
                        <textarea name="page_privacy" class="form-input" rows="16" placeholder="<h2>Kebijakan Privasi</h2>&#10;<p>...</p>"
                            style="font-family:monospace;font-size:0.82rem">{{ old('page_privacy', $get('page_privacy')) }}</textarea>
                    </div>
                </div>

            </div>
        @endif

        {{-- ── TAB: STATISTIK ───────────────────────────────── --}}
        @if ($tab === 'statistik')
            <div class="card" style="max-width:400px">
                <div class="card-header"><strong>Angka Statistik Halaman Publik</strong></div>
                <div class="card-body">
                    <p class="field-hint" style="margin-bottom:1rem">Angka ini tampil di hero/banner halaman utama sebagai
                        pencapaian program.</p>

                    <div class="form-group">
                        <label>Jumlah Workshop</label>
                        <input type="number" name="stat_workshop" class="form-input"
                            value="{{ old('stat_workshop', $get('stat_workshop', '0')) }}" min="0"
                            placeholder="0">
                    </div>

                    <div class="form-group">
                        <label>Jumlah ASN Terlatih</label>
                        <input type="number" name="stat_asn" class="form-input"
                            value="{{ old('stat_asn', $get('stat_asn', '0')) }}" min="0" placeholder="0">
                    </div>

                    <div class="form-group">
                        <label>Jumlah Artikel / Konten</label>
                        <input type="number" name="stat_article" class="form-input"
                            value="{{ old('stat_article', $get('stat_article', '0')) }}" min="0" placeholder="0">
                    </div>

                </div>
            </div>
        @endif

        <div style="margin-top:1.2rem">
            <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.8rem">
                Simpan Pengaturan
            </button>
        </div>

    </form>

@endsection
