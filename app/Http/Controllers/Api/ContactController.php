<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'opd'     => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Sanitize
        foreach ($data as $k => $v) {
            $data[$k] = strip_tags($v);
        }

        // Simpan ke log (opsional — bisa kirim email juga)
        // Untuk sekarang cukup return success
        // Jika ingin kirim email, tambahkan Mail::to(...)->send(...)

        return response()->json(['success' => true, 'message' => 'Pesan berhasil dikirim.']);
    }
}
