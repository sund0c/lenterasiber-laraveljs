<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // Definisi semua key yang valid beserta type-nya
    private array $schema = [
        // Umum
        'site_name'         => 'string',
        'site_description'  => 'string',
        'contact_email'     => 'string',
        'contact_phone'     => 'string',
        'contact_address'   => 'string',
        // Medsos
        'social_instagram'  => 'string',
        'social_twitter'    => 'string',
        'social_youtube'    => 'string',
        'social_facebook'   => 'string',
        'social_linkedin'   => 'string',
        // Halaman Statis
        'page_tos'          => 'text',
        'page_privacy'      => 'text',
        'page_tos_date'     => 'string',
        'page_privacy_date' => 'string',
        // Statistik
        'stat_workshop'     => 'int',
        'stat_asn'          => 'int',
        'stat_article'      => 'int',
    ];

    public function index()
    {
        $settings = DB::table('site_settings')->get()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'        => ['nullable', 'string', 'max:150'],
            'site_description' => ['nullable', 'string', 'max:300'],
            'contact_email'    => ['nullable', 'email', 'max:255'],
            'contact_phone'    => ['nullable', 'string', 'max:30'],
            'contact_address'  => ['nullable', 'string', 'max:300'],
            'social_instagram' => ['nullable', 'url', 'max:500'],
            'social_twitter'   => ['nullable', 'url', 'max:500'],
            'social_youtube'   => ['nullable', 'url', 'max:500'],
            'social_facebook'  => ['nullable', 'url', 'max:500'],
            'social_linkedin'  => ['nullable', 'url', 'max:500'],
            'page_tos'         => ['nullable', 'string'],
            'page_privacy'     => ['nullable', 'string'],
            'stat_workshop'    => ['nullable', 'integer', 'min:0'],
            'stat_asn'         => ['nullable', 'integer', 'min:0'],
            'stat_article'     => ['nullable', 'integer', 'min:0'],
            'page_tos_date'     => ['nullable', 'string', 'max:50'],
            'page_privacy_date' => ['nullable', 'string', 'max:50'],
        ]);

        // Hanya simpan key yang ada di schema (whitelist)
        foreach ($this->schema as $key => $type) {
            if ($request->has($key)) {
                DB::table('site_settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value'      => $request->input($key),
                        'type'       => $type,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        AuditLog::record('settings.update');
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
