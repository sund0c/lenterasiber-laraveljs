<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PodcastController extends Controller
{
    private function currentUser(): object
    {
        $user = DB::table('admin_users')->where('id', session('auth_user_id'))->first();
        abort_if(!$user, 403);
        return $user;
    }

    private function isAdmin(): bool
    {
        return $this->currentUser()->role === 'admin';
    }

    private function guardPublished(object $item): void
    {
        if (!$this->isAdmin() && $item->is_published) {
            abort(403, 'Podcast yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    public function index(Request $request)
    {
        $sortable = ['title', 'episode_number', 'category', 'duration_minutes', 'published_date', 'is_published'];
        $sort     = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'published_date';
        $dir      = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->get('q', ''));

        $query = DB::table('podcast')->whereNull('deleted_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('episode_number', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $dir);
        if ($sort !== 'id') {
            $query->orderBy('id', 'desc');
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.podcast.index', compact('items', 'sort', 'dir', 'search'));
    }

    public function create()
    {
        return view('admin.podcast.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            $data['is_published'] = false;
        }

        $data['created_by'] = session('auth_user_id');
        $data['thumbnail']  = $this->handleUpload($request);

        $id = DB::table('podcast')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('podcast.create', 'podcast', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.podcast.index')->with('success', 'Podcast berhasil disimpan.');
    }

    public function show(int $id)
    {
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        if ($this->isAdmin()) {
            return redirect()->route('admin.podcast.edit', $id);
        }

        abort_if(!$item->is_published, 403);
        return view('admin.podcast.show', compact('item'));
    }

    public function edit(int $id)
    {
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);
        return view('admin.podcast.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            unset($data['is_published']);
        }

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
        $item = DB::table('podcast')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        DB::table('podcast')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('podcast.delete', 'podcast', $id);
        return redirect()->route('admin.podcast.index')->with('success', 'Podcast dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'episode_number'   => ['required', 'string', 'max:20'],
            'description'      => ['required', 'string', 'max:100'],
            'audio_url'        => ['required', 'url', 'max:500'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'published_date'   => ['required', 'date'],
            'is_published'     => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        foreach (['title', 'episode_number', 'description'] as $field) {
            $data[$field] = strip_tags($data[$field]);
        }

        return $data;
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('thumbnail')) return null;

        $file  = $request->file('thumbnail');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

        if (!array_key_exists($mime, $allowed)) {
            abort(422, 'Tipe file tidak valid. Hanya JPG dan PNG.');
        }
        if ($file->getSize() > 2048 * 1024) {
            abort(422, 'Ukuran file melebihi 2MB.');
        }

        $name = Str::random(40) . '.' . $allowed[$mime];
        $file->storeAs('uploads/podcast', $name, 'public');
        return 'uploads/podcast/' . $name;
    }
}
