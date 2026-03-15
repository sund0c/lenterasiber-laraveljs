<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarqueeController extends Controller
{
    use ChecksRole;

    private const SORTABLE = ['title', 'status'];

    public function index(Request $request)
    {
        $this->requireAdmin();

        $sort   = in_array($request->get('sort'), self::SORTABLE) ? $request->get('sort') : 'id';
        $dir    = $request->get('dir') === 'asc' ? 'asc' : 'desc';
        $search = trim($request->get('q', ''));

        $query = DB::table('marquees');

        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        $query->orderBy($sort, $dir)->orderBy('id', 'desc');

        $items = $query->paginate(10)->withQueryString();

        return view('admin.marquee.index', compact('items', 'sort', 'dir', 'search'));
    }

    public function create()
    {
        $this->requireAdmin();
        return view('admin.marquee.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $data = $this->validated($request);

        $data['created_by'] = session('auth_user_id');
        $data['updated_by'] = session('auth_user_id');

        if (isset($data['status']) && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $id = DB::table('marquees')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('marquee.create', 'marquees', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.marquee.index')->with('success', 'Marquee berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $this->requireAdmin();
        $item = DB::table('marquees')->where('id', $id)->first();
        abort_if(!$item, 404);
        return view('admin.marquee.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $this->requireAdmin();
        $item = DB::table('marquees')->where('id', $id)->first();
        abort_if(!$item, 404);

        $data = $this->validated($request);
        $data['updated_by'] = session('auth_user_id');

        // Set published_at saat pertama kali dipublish
        if ($data['status'] === 'published' && $item->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        DB::table('marquees')->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        AuditLog::record('marquee.update', 'marquees', $id, (array) $item, $data);
        return redirect()->route('admin.marquee.index')->with('success', 'Marquee berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        $item = DB::table('marquees')->where('id', $id)->first();
        abort_if(!$item, 404);

        DB::table('marquees')->where('id', $id)->delete();
        AuditLog::record('marquee.delete', 'marquees', $id, (array) $item);
        return redirect()->route('admin.marquee.index')->with('success', 'Marquee dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'status'         => ['required', 'in:draft,published'],
        ]);

        $data['title'] = strip_tags($data['title']);

        return $data;
    }
}
