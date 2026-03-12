<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
    private const MAX_SIZE_KB  = 2048;

    public function index()
    {
        $items = DB::table('layanan')->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.layanan.index', compact('items'));
    }

    public function create()
    {
        return view('admin.layanan.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = session('auth_user_id');
        $data['cover_image'] = $this->handleUpload($request);

        $id = DB::table('layanan')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('layanan.create', 'layanan', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('layanan')->where('id', $id)->first();
        abort_if(!$item, 404);
        // Decode JSON features back to array for form
        $item->features = $item->features ? json_decode($item->features, true) : [];
        return view('admin.layanan.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('layanan')->where('id', $id)->first();
        abort_if(!$item, 404);

        $data = $this->validated($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->handleUpload($request);
        }

        DB::table('layanan')->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        AuditLog::record('layanan.update', 'layanan', $id, (array) $item, $data);
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('layanan')->where('id', $id)->delete();
        AuditLog::record('layanan.delete', 'layanan', $id);
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'program_number'  => ['required', 'string', 'max:10'],
            'title'           => ['required', 'string', 'max:150'],
            'title_plain'     => ['nullable', 'string', 'max:150'],
            'title_highlight' => ['nullable', 'string', 'max:150'],
            'short_desc'      => ['nullable', 'string', 'max:500'],
            'features'   => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:100'],
            'badge_label'     => ['nullable', 'string', 'max:60'],
            'full_content'    => ['nullable', 'string'],
            'is_active'       => ['nullable', 'boolean'],
            'sort_order'      => ['nullable', 'integer', 'min:0', 'max:99'],
            'target_label' => ['nullable', 'string', 'max:60'],
            'target_value' => ['nullable', 'string', 'max:150'],
            'card_style' => ['nullable', 'in:workshop,roadshow,latsar,sentinel,default'],
            'box_label'     => ['nullable', 'string', 'max:80'],
            'box_value'     => ['nullable', 'string', 'max:200'],
            'stats'         => ['nullable', 'array'],
            'stats.*.value' => ['nullable', 'string', 'max:20'],
            'stats.*.label' => ['nullable', 'string', 'max:40'],
            'cta_text'      => ['nullable', 'string', 'max:80'],
            'cta_url'       => ['nullable', 'url', 'max:500'],
            'section_label' => ['nullable', 'string', 'max:80'],
        ]);


        if (!empty($data['stats'])) {
            $filtered = array_filter($data['stats'], function ($s) {
                return !empty(trim($s['value'] ?? '')) || !empty(trim($s['label'] ?? ''));
            });
            $data['stats'] = !empty($filtered) ? json_encode(array_values($filtered)) : null;
        } else {
            $data['stats'] = null;
        }

        if (!empty($data['features'])) {
            $filtered = array_filter($data['features'], fn($f) => !is_null($f) && trim($f) !== '');
            $data['features'] = !empty($filtered) ? json_encode(array_values($filtered)) : null;
        } else {
            $data['features'] = null;
        }
        // Encode features ke JSON untuk DB
        $data['features']  = !empty($data['features']) ? json_encode($data['features']) : null;
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('cover_image')) return null;

        $file  = $request->file('cover_image');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        if (!in_array($mime, self::ALLOWED_MIME)) {
            abort(422, 'Tipe file tidak diizinkan.');
        }
        if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
            abort(422, 'Ukuran file melebihi batas 2MB.');
        }

        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/layanan', $name, 'public');

        return 'uploads/layanan/' . $name;
    }
}
