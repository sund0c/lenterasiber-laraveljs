<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KabarController extends Controller
{
    use ChecksRole;

    private function guardPublished(object $item): void
    {
        if ($this->isStaf() && $item->status === 'published') {
            abort(403, 'Artikel yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    public function index(Request $request)
    {
        $sortable = ['title', 'category', 'published_date', 'status'];
        $sort     = in_array($request->get('sort'), $sortable) ? $request->get('sort') : 'published_date';
        $dir      = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $search   = trim($request->get('q', ''));

        $query = DB::table('kabar')->whereNull('deleted_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $dir);
        if ($sort !== 'id') {
            $query->orderBy('id', 'desc');
        }

        $items = $query->paginate(10)->withQueryString();

        return view('admin.kabar.index', compact('items', 'sort', 'dir', 'search'));
    }

    public function create()
    {
        return view('admin.kabar.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($this->isStaf()) {
            $data['status'] = 'draft';
        }

        $data['created_by'] = session('auth_user_id');
        $data['slug']       = $this->makeSlug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        $id = DB::table('kabar')->insertGetId(array_merge($data, [
            'view_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('kabar.create', 'kabar', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil disimpan.');
    }

    public function show(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        if ($this->isAdmin()) {
            return redirect()->route('admin.kabar.edit', $id);
        }

        abort_if($item->status !== 'published', 403);
        return view('admin.kabar.show', compact('item'));
    }

    public function edit(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);
        return view('admin.kabar.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        $data = $this->validated($request);

        if ($this->isStaf()) {
            $data['status']   = 'draft';
            $data['category'] = $item->category;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        DB::table('kabar')->where('id', $id)->update(array_merge($data, [
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]));

        AuditLog::record('kabar.update', 'kabar', $id, (array) $item, $data);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        DB::table('kabar')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('kabar.delete', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dihapus.');
    }

    public function publish(int $id)
    {
        $this->requireAdmin();
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        DB::table('kabar')->where('id', $id)->update([
            'status'       => 'published',
            'published_at' => now(),
            'updated_by'   => session('auth_user_id'),
            'updated_at'   => now(),
        ]);

        AuditLog::record('kabar.publish', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dipublikasikan.');
    }

    public function unpublish(int $id)
    {
        $this->requireAdmin();

        DB::table('kabar')->where('id', $id)->update([
            'status'     => 'draft',
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]);

        AuditLog::record('kabar.unpublish', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dikembalikan ke draft.');
    }

    private function validated(Request $request): array
    {
        $isAdmin = $this->isAdmin();

        $rules = [
            'title'          => ['required', 'string', 'max:255'],
            'excerpt'        => ['required', 'string', 'max:100'],
            'content'        => ['required', 'string'],
            'published_date' => ['required', 'date'],
            'status'         => ['nullable', 'in:draft,published'],
        ];

        if ($isAdmin) {
            $rules['category'] = ['required', 'string', 'max:80'];
        } else {
            $rules['category'] = ['nullable', 'string', 'max:80'];
        }

        $data = $request->validate($rules);

        $data['title']   = strip_tags($data['title']);
        $data['excerpt'] = strip_tags($data['excerpt']);
        if (isset($data['category'])) {
            $data['category'] = strip_tags($data['category']);
        }

        return $data;
    }

    private function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        $orig = $slug;
        $i    = 1;
        while (DB::table('kabar')->where('slug', $slug)->exists()) {
            $slug = $orig . '-' . $i++;
        }
        return $slug;
    }

    private function handleUpload(Request $request): string
    {
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
        $file->storeAs('uploads/kabar', $name, 'public');
        return 'uploads/kabar/' . $name;
    }
}
