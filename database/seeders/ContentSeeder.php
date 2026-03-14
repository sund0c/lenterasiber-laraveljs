<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ── ADMIN USERS ───────────────────────────────────────
        $adminId = DB::table('admin_users')->insertGetId([
            'username'              => 'superadmin',
            'email'                 => 'admin@lenterasiber.id',
            'full_name'             => 'Super Administrator',
            'password'              => Hash::make('Admin@2025!', ['rounds' => 13]),
            'role'                  => 'admin',
            'password_changed_at'   => $now,
            'password_history'      => json_encode([]),
            'totp_enabled'          => false,
            'failed_attempts'       => 0,
            'force_password_change' => false,
            'created_at'            => $now,
            'updated_at'            => $now,
        ]);

        DB::table('admin_users')->insert([
            'username'              => 'staf01',
            'email'                 => 'staf@lenterasiber.id',
            'full_name'             => 'Staf Konten',
            'password'              => Hash::make('Staf@2025!', ['rounds' => 13]),
            'role'                  => 'staf',
            'password_changed_at'   => $now,
            'password_history'      => json_encode([]),
            'totp_enabled'          => false,
            'failed_attempts'       => 0,
            'force_password_change' => true,
            'created_at'            => $now,
            'updated_at'            => $now,
        ]);

        // ── KOMIK ─────────────────────────────────────────────
        $komikData = [
            ['Si Pancing — Kisah Phishing di Kotak Masuk',   'Episode 1',  'Keamanan Email'],
            ['Kunci Kuat, Akun Selamat',                      'Episode 2',  'Password & Autentikasi'],
            ['Waspada Jaringan Palsu',                        'Episode 3',  'Keamanan WiFi'],
            ['Ransomware Si Penyandera Data',                 'Episode 4',  'Malware'],
            ['Update atau Menyesal',                          'Episode 5',  'Patch Management'],
            ['Rekayasa Sosial: Manipulasi Manusia',           'Episode 6',  'Social Engineering'],
            ['Backup: Jimat Data Anda',                       'Episode 7',  'Data Protection'],
            ['Penipuan Belanja Online',                       'Episode 8',  'E-Commerce Safety'],
            ['Verifikasi Dua Langkah',                        'Episode 9',  'Autentikasi'],
            ['Privasi di Media Sosial',                       'Episode 10', 'Privasi Digital'],
            ['Bahaya Link Sembarangan',                       'Episode 11', 'Keamanan Email'],
            ['Sandi yang Tak Terlupakan',                     'Episode 12', 'Password'],
            ['Data Pribadi Berharga',                         'Episode 13', 'Privasi Digital'],
            ['VPN: Tameng Digital',                           'Episode 14', 'Keamanan Jaringan'],
            ['Deepfake dan Hoaks Visual',                     'Episode 15', 'Literasi Digital'],
            ['Zero Trust: Jangan Percaya Siapapun',           'Episode 16', 'Kebijakan Keamanan'],
            ['Kejahatan Siber di Kantor',                     'Episode 17', 'Keamanan Korporat'],
            ['Scam Berkedok Hadiah',                          'Episode 18', 'Penipuan Online'],
            ['Keamanan Perangkat Mobile',                     'Episode 19', 'Mobile Security'],
            ['Enkripsi: Penjaga Rahasia',                     'Episode 20', 'Kriptografi'],
            ['Identitas Digital ASN',                         'Episode 21', 'Kebijakan Keamanan'],
            ['Insider Threat: Ancaman dari Dalam',            'Episode 22', 'Keamanan Korporat'],
            ['Incident Response: Ketika Diserang',            'Episode 23', 'Respons Insiden'],
            ['Cloud Security: Aman di Awan',                  'Episode 24', 'Cloud Computing'],
            ['Literasi Siber untuk Semua',                    'Episode 25', 'Edukasi Umum'],
        ];

        foreach ($komikData as $i => [$title, $episode, $category]) {
            DB::table('konten')->insert([
                'label'          => 'KOMIK',
                'slug'           => 'komik-' . Str::slug($episode) . '-' . ($i + 1),
                'title'          => $title,
                'episode_number' => $episode,
                'category'       => $category,
                'external_url'   => 'https://www.instagram.com/p/example' . ($i + 1),
                'excerpt'        => Str::limit('Komik edukatif keamanan siber: ' . $title, 100),
                'content'        => '<p>Konten lengkap episode <strong>' . $title . '</strong>.</p><p>Pelajari keamanan siber melalui komik edukatif Lentera Siber.</p>',
                'cover_image'    => null,
                'status'         => ($i % 3 !== 0) ? 'published' : 'draft',
                'published_date' => now()->subDays($i * 7)->format('Y-m-d'),
                'published_at'   => ($i % 3 !== 0) ? now()->subDays($i * 7) : null,
                'view_count'     => rand(10, 300),
                'created_by'     => $adminId,
                'updated_by'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ── PODCAST ───────────────────────────────────────────
        $podcastData = [
            ['Ransomware: Ancaman Nyata bagi Data Pemerintah',        'EP.01', 45, 'Malware'],
            ['Phishing: Kenali Sebelum Terjebak',                     'EP.02', 38, 'Keamanan Email'],
            ['Zero Trust Architecture di Lingkungan Pemerintah',      'EP.03', 52, 'Kebijakan Keamanan'],
            ['Pengelolaan Password yang Aman dan Praktis',            'EP.04', 30, 'Password'],
            ['Social Engineering dan Cara Melawannya',                'EP.05', 41, 'Social Engineering'],
            ['Keamanan Cloud untuk ASN Bali',                         'EP.06', 48, 'Cloud Computing'],
            ['Incident Response: Langkah Ketika Terjadi Serangan',    'EP.07', 55, 'Respons Insiden'],
            ['Privasi Data dan UU PDP',                               'EP.08', 43, 'Kebijakan'],
            ['Mobile Security: Aman Bekerja dari Mana Saja',          'EP.09', 35, 'Mobile Security'],
            ['Enkripsi Data: Dari Teori ke Praktik',                  'EP.10', 47, 'Kriptografi'],
            ['Vulnerability Management di Instansi Pemerintah',       'EP.11', 50, 'Manajemen Risiko'],
            ['Open Source Intelligence (OSINT) untuk Pegawai',        'EP.12', 39, 'Intelijen Siber'],
            ['Backup dan Disaster Recovery Planning',                 'EP.13', 44, 'Data Protection'],
            ['Keamanan Email Dinas',                                  'EP.14', 33, 'Keamanan Email'],
            ['Firewall dan IDS: Benteng Digital Kantor',              'EP.15', 46, 'Keamanan Jaringan'],
            ['Ancaman Insider: Waspadai Orang Dalam',                 'EP.16', 40, 'Keamanan Korporat'],
            ['Digital Forensics: Jejak di Dunia Maya',                'EP.17', 58, 'Forensik Digital'],
            ['Kebijakan Keamanan Siber Nasional',                     'EP.18', 36, 'Kebijakan'],
            ['SIEM: Memantau Ancaman Secara Real-Time',               'EP.19', 53, 'Monitoring'],
            ['Pentest 101: Uji Keamanan Sistem Anda',                 'EP.20', 49, 'Pengujian Keamanan'],
            ['DevSecOps untuk Pengembang Pemerintah',                 'EP.21', 42, 'Pengembangan Aman'],
            ['Keamanan IoT di Lingkungan Perkantoran',                'EP.22', 37, 'IoT Security'],
            ['AI dan Ancaman Siber Masa Depan',                       'EP.23', 51, 'Tren Keamanan'],
            ['Budaya Keamanan Siber di Tempat Kerja',                 'EP.24', 34, 'Edukasi'],
            ['Literasi Siber: Investasi SDM Digital Bali',            'EP.25', 56, 'Edukasi'],
        ];

        foreach ($podcastData as $i => [$title, $episode, $duration, $category]) {
            DB::table('konten')->insert([
                'label'            => 'PODCAST',
                'slug'             => 'podcast-' . Str::slug($episode) . '-' . ($i + 1),
                'title'            => $title,
                'episode_number'   => $episode,
                'category'         => $category,
                'external_url'     => 'https://open.spotify.com/episode/example' . ($i + 1),
                'excerpt'          => Str::limit('Membahas ' . lcfirst($title) . ' secara mendalam.', 100),
                'content'          => '<p>Catatan episode <strong>' . $title . '</strong>.</p><p>Dengarkan podcast ini untuk memahami keamanan siber bagi ASN Pemprov Bali.</p>',
                'duration_minutes' => $duration,
                'status'           => ($i % 4 !== 0) ? 'published' : 'draft',
                'published_date'   => now()->subDays($i * 14)->format('Y-m-d'),
                'published_at'     => ($i % 4 !== 0) ? now()->subDays($i * 14) : null,
                'view_count'       => rand(20, 500),
                'created_by'       => $adminId,
                'updated_by'       => $adminId,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        // ── KABAR ─────────────────────────────────────────────
        $kabarData = [
            ['Waspadai Phishing Berkedok Email Resmi Instansi',        'WASPADA'],
            ['Tips Membuat Password Kuat yang Mudah Diingat',          'TIPS KEAMANAN'],
            ['Pemprov Bali Perkuat Keamanan Siber ASN 2025',           'PROGRAM'],
            ['Mengenal Ransomware dan Cara Pencegahannya',             'EDUKASI'],
            ['Update Sistem Operasi: Mengapa Sangat Penting?',         'TIPS KEAMANAN'],
            ['Workshop Keamanan Siber Diikuti 200 ASN Bali',           'PROGRAM'],
            ['Bahaya Menggunakan WiFi Publik untuk Urusan Dinas',      'WASPADA'],
            ['Kebijakan BYOD di Lingkungan Pemerintah',                'KEBIJAKAN'],
            ['Cara Mengenali Situs Web Palsu',                         'TIPS KEAMANAN'],
            ['Roadshow Literasi Siber Sasar 42 OPD',                   'PROGRAM'],
            ['Autentikasi Dua Faktor: Lapisan Keamanan Ekstra',        'EDUKASI'],
            ['Insiden Kebocoran Data: Pelajaran Berharga',             'BERITA'],
            ['Panduan Aman Menggunakan Email Dinas',                   'TIPS KEAMANAN'],
            ['Keamanan Data Pribadi di Era Digital',                   'EDUKASI'],
            ['JSC Batch III Resmi Dibuka',                             'PROGRAM'],
            ['Mengenal Social Engineering dan Modusnya',               'EDUKASI'],
            ['Pentingnya Enkripsi Data bagi ASN',                      'EDUKASI'],
            ['Regulasi Keamanan Siber Terbaru yang Wajib Diketahui',   'KEBIJAKAN'],
            ['Backup Data: Kebiasaan Kecil Manfaat Besar',             'TIPS KEAMANAN'],
            ['Laporan Tahunan Ancaman Siber Indonesia 2024',           'BERITA'],
            ['Cara Aman Bekerja dari Rumah (WFH)',                     'TIPS KEAMANAN'],
            ['Komik Lentera Siber Capai 10.000 Pembaca',               'BERITA'],
            ['Mengenal UU PDP dan Implikasinya bagi ASN',              'KEBIJAKAN'],
            ['Keamanan Aplikasi Mobile Dinas',                         'EDUKASI'],
            ['Lentera Siber: Setahun Membangun Literasi Siber Bali',   'PROGRAM'],
        ];

        foreach ($kabarData as $i => [$title, $category]) {
            $slug = Str::slug($title) . '-' . ($i + 1);
            $published = ($i % 5 !== 0);
            DB::table('konten')->insert([
                'label'          => 'KABAR',
                'title'          => $title,
                'slug'           => $slug,
                'category'       => $category,
                'excerpt'        => Str::limit('Artikel tentang ' . lcfirst($title) . ' untuk meningkatkan kesadaran keamanan siber ASN.', 100),
                'content'        => '<p>Konten lengkap artikel <strong>' . $title . '</strong>.</p><p>Keamanan siber merupakan tanggung jawab bersama seluruh ASN Pemprov Bali.</p>',
                'status'         => $published ? 'published' : 'draft',
                'published_date' => now()->subDays($i * 5)->format('Y-m-d'),
                'published_at'   => $published ? now()->subDays($i * 5) : null,
                'view_count'     => rand(10, 500),
                'created_by'     => $adminId,
                'updated_by'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->command->info('');
        $this->command->info('✓ Seeder selesai: 2 user, 25 komik, 25 podcast, 25 kabar (tabel konten).');
        $this->command->info('');
        $this->command->info('  ┌─────────────────────────────────────────────┐');
        $this->command->info('  │  Admin  │ superadmin  │ Admin@2025!          │');
        $this->command->info('  │  Staf   │ staf01      │ Staf@2025!           │');
        $this->command->info('  └─────────────────────────────────────────────┘');
        $this->command->info('');
        $this->command->info('  Kedua akun wajib setup 2FA saat login pertama.');
        $this->command->info('');
    }
}
