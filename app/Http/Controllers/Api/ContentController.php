<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function index(Request $request)
    {
        $label = strtoupper($request->get('label', 'KOMIK'));
        $allowed = ['KABAR', 'KOMIK', 'PODCAST'];

        if (!in_array($label, $allowed)) {
            return response()->json(['message' => 'Label tidak valid.'], 422);
        }

        $perPage = min((int) $request->get('per_page', 12), 50);
        $category = $request->get('category');

        $query = DB::table('konten')
            ->where('label', $label)
            ->where('status', 'published')
            ->orderBy('published_date', 'desc')
            ->orderBy('id', 'desc');

        if ($category) {
            $query->where('category', $category);
        }

        $items = $query->paginate($perPage);

        $items->getCollection()->transform(function ($item) {
            return [
                'id'               => $item->id,
                'title'            => $item->title,
                'ai'            => $item->ai,
                'slug'             => $item->slug,
                'episode_number'   => $item->episode_number,
                'category'         => $item->category,
                'excerpt'          => $item->excerpt,
                'content'          => $item->content,
                'cover_image'      => $item->cover_image
                    ? url('storage/' . $item->cover_image)
                    : null,
                'external_url'     => $item->external_url,
                'duration_minutes' => $item->duration_minutes,
                'published_date'   => $item->published_date,
                'view_count'       => $item->view_count,
            ];
        });

        return response()->json($items);
    }

    public function show(string $label, int $id)
    {
        $label = strtoupper($label);

        $item = DB::table('konten')
            ->where('label', $label)
            ->where('id', $id)
            ->where('status', 'published')
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Konten tidak ditemukan.'], 404);
        }

        // Increment view count
        DB::table('konten')->where('id', $id)->increment('view_count');

        return response()->json([
            'id'               => $item->id,
            'title'            => $item->title,
            'ai'            => $item->ai,
            'slug'             => $item->slug,
            'episode_number'   => $item->episode_number,
            'category'         => $item->category,
            'excerpt'          => $item->excerpt,
            'content'          => $item->content,
            'cover_image'      => $item->cover_image
                ? url('storage/' . $item->cover_image)
                : null,
            'external_url'     => $item->external_url,
            'duration_minutes' => $item->duration_minutes,
            'published_date'   => $item->published_date,
            'view_count'       => $item->view_count,
        ]);
    }
}
