<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MarqueeController extends Controller
{
    public function index()
    {
        $items = DB::table('marquees')
            ->where('status', 'published')
            ->orderBy('id')
            ->get(['id', 'title']);

        return response()->json($items);
    }
}
