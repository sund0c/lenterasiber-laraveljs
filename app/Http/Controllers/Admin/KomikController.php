<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomikController extends Controller
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
            abort(403, 'Komik yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    public function index(Request $request)
    {
        // ── Kolom yang boleh di-sort ──
        $sortable = ['title', 'episode_number', 'category', 'published_date', 'is_published'];
        $sort     = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'published_date';
        $dir      = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->get('q', ''));

        $query = DB::table('komik')->whereNull('deleted_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('episode_number', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $dir);
        if ($sort !== 'id') {
            $query->orderBy('id', 'desc'); // tiebreaker
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.komik.index', compact('items', 'sort', 'dir', 'search'));
    }

    public function create()
    {
        return view('admin.komik.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            $data['is_published'] = false;
            $data['category']     = null;
        }

        $data['created_by']  = session('auth_user_id');
        $data['cover_image'] = $this->handleUpload($request);

        $id = DB::table('komik')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('komik.create', 'komik', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil disimpan.');
    }

    public function show(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        if ($this->isAdmin()) {
            return redirect()->route('admin.komik.edit', $id);
        }

        abort_if(!$item->is_published, 403);
        return view('admin.komik.show', compact('item'));
    }

    public function edit(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);
        return view('admin.komik.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            unset($data['is_published']);
            $data['category'] = $item->category;
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->handleUpload($request);
        }

        DB::table('komik')->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        AuditLog::record('komik.update', 'komik', $id, (array) $item, $data);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        DB::table('komik')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('komik.delete', 'komik', $id);
        return redirect()->route('admin.komik.index')->with('success', 'Komik dihapus.');
    }

    private function validated(Request $request): array
    {
        $isAdmin = $this->isAdmin();

        $rules = [
            'title'          => ['required', 'string', 'max:255'],
            'episode_number' => ['required', 'string', 'max:20'],
            'excerpt'        => ['required', 'string', 'max:100'],
            'content'        => ['nullable', 'string'],
            'instagram_url'  => ['required', 'url', 'max:500'],
            'published_date' => ['required', 'date'],
            'is_published'   => ['nullable', 'boolean'],
        ];

        if ($isAdmin) {
            $rules['category'] = ['nullable', 'string', 'max:100'];
        }

        $data = $request->validate($rules);

        $data['is_published'] = $request->boolean('is_published');

        foreach (['title', 'episode_number', 'excerpt'] as $field) {
            if (isset($data[$field])) {
                $data[$field] = strip_tags($data[$field]);
            }
        }
        if (isset($data['category'])) {
            $data['category'] = strip_tags($data['category']);
        }

        return $data;
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('cover_image')) return null;

        $file  = $request->file('cover_image');
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
        $file->storeAs('uploads/komik', $name, 'public');
        return 'uploads/komik/' . $name;
    }
}
