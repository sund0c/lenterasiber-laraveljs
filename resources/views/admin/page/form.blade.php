@extends('admin.layout')
@section('title',
    ($item ? 'Edit ' : 'Tambah ') .
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
@section('page-title',
    ($item ? 'Edit ' : 'Tambah ') .
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
    'JSC' => 'Junior Sentinel Challenge',
    'WORKSHOP' => 'Workshop Lentera',
    'ROADSHOW' => 'Roadshow Lentera',
    'GTL' => 'Lentera Goes to Latsar',
    'TENTANG' => 'Tentang Lentera Siber',
    'KABAR' => 'Kabar Lentera',
    'PODCAST' => 'Podcast Lentera',
    'KOMIK' => 'Komik Lentera',
    default => '',
    })

@section('content')
    @php
        $isAdmin = DB::table('admin_users')->where('id', session('auth_user_id'))->value('role') === 'admin';
    @endphp

    <div style="max-width:760px">
        <form method="POST"
            action="{{ $item ? route('admin.page.update', [$label, $item->id]) : route('admin.page.store', $label) }}"
            enctype="multipart/form-data">
            @csrf
            @if ($item)
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="alert alert-error"
                    style="margin-bottom:1.5rem; border-radius:8px; padding:1rem 1.25rem; background:#fef2f2; border-left:4px solid #ef4444; display:flex; align-items:flex-start; gap:12px;">
                    <div style="flex:1;"><strong>Mohon periksa kembali isian berikut:</strong>
                        <ul style="margin:0; padding-left:1.2rem; color:#7f1d1d; font-size:0.9rem; line-height:1.6;">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif






            {{-- ── INFORMASI ─────────────────────────────────────── --}}
            <div class="card" style="margin-bottom:1rem">
                <div class="card-header"><strong>Informasi Halaman
                        {{ match ($label) {
                            'JSC' => 'Junior Sentinel Challenge',
                            'WORKSHOP' => 'Workshop Lentera',
                            'ROADSHOW' => 'Roadshow Lentera',
                            'GTL' => 'Lentera Goes to Latsar',
                            'TENTANG' => 'Tentang',
                            'KOMIK' => 'Komik Lentera',
                            'PODCAST' => 'Podcast Lentera',
                            'KABAR' => 'Kabar Lentera',
                            default => '',
                        } }}</strong>
                </div>
                <div class="card-body">





                    <div class="form-group">
                        <label>Tanggal Publish <span style="color:var(--red)">*</span></label>
                        <input type="date" name="published_date"
                            class="form-input @error('published_date') is-error @enderror"
                            value="{{ old('published_date', isset($item->published_date) ? \Carbon\Carbon::parse($item->published_date)->format('Y-m-d') : date('Y-m-d')) }}"
                            required>
                        @error('published_date')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Judul
                            <span style="color:var(--red)">*</span></label>
                        <input type="text" name="title" id="page_title"
                            class="form-input @error('title') is-error @enderror"
                            value="{{ old('title', $item->title ?? '') }}" placeholder="Judul page" maxlength="255"
                            required>
                        @error('title')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Slug URL <span style="color:var(--muted);font-weight:normal">(opsional)</span></label>
                        <input type="text" name="slug" id="page_slug" class="form-input"
                            value="{{ old('slug', $item->slug ?? '') }}" placeholder="otomatis dari judul">
                        <p class="field-hint">Kosongkan untuk generate otomatis.</p>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="category" class="form-input @error('category') is-error @enderror"
                            value="{{ old('category', $item->category ?? '') }}" maxlength="100">

                        @error('category')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>



                    <div class="form-group">
                        <label>Ringkasan <span style="color:var(--red)">*</span></label>
                        <textarea name="excerpt" rows="2" class="form-input @error('excerpt') is-error @enderror" maxlength="150"
                            required>{{ old('excerpt', $item->excerpt ?? '') }}</textarea>
                        <p class="field-hint">Maksimal 150 karakter. Tampil di kartu halaman publik.</p>
                        @error('excerpt')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Konten Lengkap

                            <span style="color:var(--red)">*</span>

                        </label>
                        <textarea name="content" rows="18" class="form-input @error('content') is-error @enderror"
                            placeholder="Tulis konten lengkap di sini..." required>{{ old('content', $item->content ?? '') }}</textarea>
                        <p class="field-hint">Mendukung HTML: &lt;p&gt; &lt;h2&gt; &lt;h3&gt; &lt;ul&gt; &lt;li&gt;
                            &lt;strong&gt; &lt;em&gt; &lt;a href=""&gt; &lt;blockquote&gt;</p>
                        @error('content')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:0.88rem">
                            <input type="hidden" name="status" value="draft">
                            <input type="checkbox" name="status" value="published"
                                {{ old('status', $item->status ?? '') === 'published' ? 'checked' : '' }}
                                style="width:16px;height:16px;cursor:pointer">
                            Publikasikan
                        </label>
                    </div>
                </div>
            </div>





            <div style="display:flex;gap:8px">
                <button type="submit" class="btn-primary" style="width:auto;padding:0.6rem 1.5rem">
                    {{ $item? 'Simpan Perubahan': 'Simpan ' .match ($label) {'KABAR' => 'Kabar','KOMIK' => 'Komik','PODCAST' => 'Episode',default => ''} }}
                </button>
                <a href="{{ route('admin.page.index', $label) }}" class="btn-secondary">Batal</a>
            </div>

        </form>
    </div>


    <script nonce="{{ $cspNonce }}">
        document.addEventListener('DOMContentLoaded', function() {
            var titleInput = document.getElementById('page_title');
            var slugInput = document.getElementById('page_slug');
            if (!titleInput || !slugInput) return;
            var slugEdited = slugInput.value !== '';
            slugInput.addEventListener('input', function() {
                slugEdited = true;
            });
            titleInput.addEventListener('input', function() {
                if (slugEdited) return;
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            });
        });
    </script>

@endsection
