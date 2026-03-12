<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PodcastController extends Controller
{
    public function index()
    {
        $items = DB::table('podcast')
            ->whereNull('deleted_at')
            ->orderByDesc('episode_number')
            ->get();
        return view('admin.podcast.index', compact('items'));
    }

    public function create()
    {
        return view('admin.podcast.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = session('auth_user_id');
        $data['thumbnail']  = $this->handleUpload($request);

        $id = DB::table('podcast')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('podcast.create', 'podcast', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.podcast.index')->with('success', 'Podcast berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        return view('admin.podcast.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        $data = $this->validated($request);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        DB::table('podcast')->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        AuditLog::record('podcast.update', 'podcast', $id, (array) $item, $data);
        return redirect()->route('admin.podcast.index')->with('success', 'Podcast berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('podcast')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('podcast.delete', 'podcast', $id);
        return redirect()->route('admin.podcast.index')->with('success', 'Podcast dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'episode_number'   => ['nullable', 'string', 'max:20'],
            'description'      => ['nullable', 'string'],
            'audio_url'        => ['nullable', 'url', 'max:500'],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'is_published'     => ['nullable', 'boolean'],
            'published_date'   => ['nullable', 'date'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        return $data;
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('thumbnail')) return null;

        $file  = $request->file('thumbnail');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            abort(422, 'Tipe file tidak valid.');
        }
        if ($file->getSize() > 2048 * 1024) {
            abort(422, 'File terlalu besar. Maks 2MB.');
        }

        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/podcast', $name, 'public');

        return 'uploads/podcast/' . $name;
    }
}
