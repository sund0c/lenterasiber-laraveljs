<?php
// app/Http/Controllers/Api/ContactController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        // Rate limit: 3 per hour per IP
        $key = 'contact:' . sha1($request->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json(['message' => 'Terlalu banyak permintaan.'], 429);
        }

        // Honeypot (bot trap — should be empty)
        if ($request->filled('website')) {
            return response()->json(['message' => 'OK']); // silently discard
        }

        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'subjek'   => ['required', 'string', 'max:255'],
            'pesan'    => ['required', 'string', 'max:2000'],
        ]);


        RateLimiter::hit($key, 3600);

        return response()->json(['message' => 'Pesan berhasil dikirim.']);
    }
}
