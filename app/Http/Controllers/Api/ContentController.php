<?php
// app/Http/Controllers/Api/ContentController.php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    public function index()
    {
        $data = Cache::remember('public_content', 300, function () {
            return [
                'kabar'   => DB::table('kabar')
                    ->whereNull('deleted_at')->where('status','published')
                    ->orderByDesc('published_at')->limit(10)
                    ->select(['id','slug','title','excerpt','category','published_at','read_minutes','thumbnail'])->get(),
                'layanan' => DB::table('layanan')
                    ->where('is_active',true)->orderBy('sort_order')
                    ->select(['id','icon','title','short_desc'])->get(),
                'workshop'=> DB::table('workshop')
                    ->whereNull('deleted_at')->where('status','upcoming')
                    ->orderBy('event_date')->limit(5)
                    ->select(['id','title','event_date','location','status'])->get(),
                'settings'=> DB::table('site_settings')
                    ->get()->pluck('value','key'),
            ];
        });

        return response()->json($data)->header('Cache-Control', 'public, max-age=300');
    }
}
