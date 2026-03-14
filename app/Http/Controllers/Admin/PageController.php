<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use ChecksRole;

    // Label yang valid
    private const LABELS = ['GTL', 'WORKSHOP', 'ROADSHOW', 'JSC', 'TENTANG', 'KABAR', 'PODCAST', 'KOMIK'];

    // Kolom yang boleh di-sort per label
    private const SORTABLE = ['title', 'published_date', 'status'];

    // ── Resolve label dari route ──────────────────────────
    private function resolveLabel(string $label): string
    {
        $label = strtoupper($label);
        abort_if(!in_array($label, self::LABELS), 404);
        return $label;
    }

    // ── Guard: staf tidak bisa aksi pada konten published ─
    private function guardPublished(object $item): void
    {
        if ($this->isStaf() && $item->status === 'published') {
            abort(403, 'Konten yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    // ── Index ─────────────────────────────────────────────
    public function index(Request $request, string $label)
    {
        $label  = $this->resolveLabel($label);
        $sort   = in_array($request->get('sort'), self::SORTABLE) ? $request->get('sort') : 'published_date';
        $dir    = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $search = trim($request->get('q', ''));

        $query = DB::table('staticpage')->where('label', $label);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $query->orderBy($sort, $dir)->orderBy('id', 'desc');

        $items = $query->paginate(10)->withQueryString();

        return view('admin.page.index', compact('items', 'label', 'sort', 'dir', 'search'));
    }

    // ── Create ────────────────────────────────────────────
    public function create(Request $request, string $label)
    {
        $label = $this->resolveLabel($label);
        return view('admin.page.form', ['item' => null, 'label' => $label]);
    }

    // ── Store ─────────────────────────────────────────────
    public function store(Request $request, string $label)
    {
        $label = $this->resolveLabel($label);
        $data  = $this->validated($request, $label);

        if ($this->isStaf()) {
            $data['status']   = 'draft';
        }

        $data['label']      = $label;
        $data['created_by'] = session('auth_user_id');
        $data['updated_by'] = session('auth_user_id');

        // if ($label === 'KABAR') {
        $data['slug'] = $this->makeSlug($data['title']);
        // }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->handleUpload($request, $label);
        }

        $id = DB::table('staticpage')->insertGetId(array_merge($data, [
            'view_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('page.create', 'staticpage', $id, null, ['label' => $label, 'title' => $data['title']]);
        return redirect()->route('admin.page.index', $label)
            ->with('success', $this->labelName($label) . ' berhasil disimpan.');
    }

    // ── Show (view-only staf pada konten published) ───────
    public function show(Request $request, string $label, int $id)
    {
        $label = $this->resolveLabel($label);
        $item  = DB::table('staticpage')->where('label', $label)->where('id', $id)->first();
        abort_if(!$item, 404);

        if ($this->isAdmin()) {
            return redirect()->route('admin.page.edit', [$label, $id]);
        }

        abort_if($item->status !== 'published', 403);
        return view('admin.page.show', compact('item', 'label'));
    }

    // ── Edit ──────────────────────────────────────────────
    public function edit(Request $request, string $label, int $id)
    {
        $label = $this->resolveLabel($label);
        $item  = DB::table('staticpage')->where('label', $label)->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);
        return view('admin.page.form', compact('item', 'label'));
    }

    // ── Update ────────────────────────────────────────────
    public function update(Request $request, string $label, int $id)
    {
        $label = $this->resolveLabel($label);
        $item  = DB::table('staticpage')->where('label', $label)->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        $data = $this->validated($request, $label);

        if ($this->isStaf()) {
            $data['status']   = 'draft';
        }

        if ($request->hasFile('cover_image')) {
            // Hapus file lama
            if ($item->cover_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($item->cover_image);
            }
            $data['cover_image'] = $this->handleUpload($request, $label);
        }

        DB::table('staticpage')->where('id', $id)->update(array_merge($data, [
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]));

        AuditLog::record('page.update', 'staticpage', $id, (array) $item, $data);
        return redirect()->route('admin.page.index', $label)
            ->with('success', $this->labelName($label) . ' berhasil diperbarui.');
    }

    // ── Destroy ───────────────────────────────────────────
    public function destroy(Request $request, string $label, int $id)
    {
        $label = $this->resolveLabel($label);
        $item  = DB::table('staticpage')->where('label', $label)->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        // Hapus file gambar
        if ($item->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($item->cover_image);
        }

        DB::table('staticpage')->where('id', $id)->delete();
        AuditLog::record('page.delete', 'staticpage', $id, (array) $item);
        return redirect()->route('admin.page.index', $label)
            ->with('success', $this->labelName($label) . ' dihapus.');
    }

    // ── Publish / Unpublish (admin only) ──────────────────
    public function publish(Request $request, string $label, int $id)
    {
        $this->requireAdmin();
        $label = $this->resolveLabel($label);
        $item  = DB::table('staticpage')->where('label', $label)->where('id', $id)->first();
        abort_if(!$item, 404);

        DB::table('staticpage')->where('id', $id)->update([
            'status'       => 'published',
            'published_at' => now(),
            'updated_by'   => session('auth_user_id'),
            'updated_at'   => now(),
        ]);

        AuditLog::record('page.publish', 'staticpage', $id);
        return redirect()->route('admin.page.index', $label)
            ->with('success', $this->labelName($label) . ' dipublikasikan.');
    }

    public function unpublish(Request $request, string $label, int $id)
    {
        $this->requireAdmin();
        $label = $this->resolveLabel($label);

        DB::table('staticpage')->where('id', $id)->update([
            'status'     => 'draft',
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]);

        AuditLog::record('page.unpublish', 'staticpage', $id);
        return redirect()->route('admin.page.index', $label)
            ->with('success', $this->labelName($label) . ' dikembalikan ke draft.');
    }

    // ── Validated ─────────────────────────────────────────
    private function validated(Request $request, string $label): array
    {
        $isAdmin = $this->isAdmin();

        $rules = [
            'title'          => ['required', 'string', 'max:255'],
            'excerpt'        => ['required', 'string', 'max:150'],
            'content'        => ['nullable', 'string'],
            'published_date' => ['required', 'date'],
            'status'         => ['nullable', 'in:draft,published'],
        ];


        $data = $request->validate($rules);

        // Sanitize
        $data['title']   = strip_tags($data['title']);
        $data['excerpt'] = strip_tags($data['excerpt']);


        return $data;
    }

    // ── Helpers ───────────────────────────────────────────
    private function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        $orig = $slug;
        $i    = 1;
        while (DB::table('staticpage')->where('slug', $slug)->exists()) {
            $slug = $orig . '-' . $i++;
        }
        return $slug;
    }

    private function handleUpload(Request $request, string $label): ?string
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

        $folder = 'uploads/' . strtolower($label);
        $name   = Str::random(40) . '.' . $allowed[$mime];
        $file->storeAs($folder, $name, 'public');
        return $folder . '/' . $name;
    }

    public function labelName(string $label): string
    {
        return match ($label) {
            'JSC'   => 'Jsc',
            'WORKSHOP'   => 'Workshop',
            'ROADSHOW' => 'Roadshow',
            'GTL' => 'Gtl',
            default   => $label,
        };
    }
}
