<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('admin_users')->value('id');
        $now     = now();

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
            DB::table('komik')->insert([
                'title'          => $title,
                'episode_number' => $episode,
                'category'       => $category,
                'instagram_url'  => 'https://www.instagram.com/p/example' . ($i + 1),
                'description'    => 'Komik edukatif keamanan siber: ' . $title,
                'cover_image'    => null,
                'is_published'   => ($i % 3 !== 0), // sebagian draft
                'published_date' => now()->subDays($i * 7)->format('Y-m-d'),
                'created_by'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        // ── PODCAST ───────────────────────────────────────────
        $podcastData = [
            ['Ransomware: Ancaman Nyata bagi Data Pemerintah',        'EP.01', 45],
            ['Phishing: Kenali Sebelum Terjebak',                     'EP.02', 38],
            ['Zero Trust Architecture di Lingkungan Pemerintah',      'EP.03', 52],
            ['Pengelolaan Password yang Aman dan Praktis',            'EP.04', 30],
            ['Social Engineering dan Cara Melawannya',                'EP.05', 41],
            ['Keamanan Cloud untuk ASN Bali',                         'EP.06', 48],
            ['Incident Response: Langkah Ketika Terjadi Serangan',    'EP.07', 55],
            ['Privasi Data dan UU PDP',                               'EP.08', 43],
            ['Mobile Security: Aman Bekerja dari Mana Saja',          'EP.09', 35],
            ['Enkripsi Data: Dari Teori ke Praktik',                  'EP.10', 47],
            ['Vulnerability Management di Instansi Pemerintah',       'EP.11', 50],
            ['Open Source Intelligence (OSINT) untuk Pegawai',        'EP.12', 39],
            ['Backup dan Disaster Recovery Planning',                 'EP.13', 44],
            ['Keamanan Email Dinas',                                  'EP.14', 33],
            ['Firewall dan IDS: Benteng Digital Kantor',              'EP.15', 46],
            ['Ancaman Insider: Waspadai Orang Dalam',                 'EP.16', 40],
            ['Digital Forensics: Jejak di Dunia Maya',                'EP.17', 58],
            ['Kebijakan Keamanan Siber Nasional',                     'EP.18', 36],
            ['SIEM: Memantau Ancaman Secara Real-Time',               'EP.19', 53],
            ['Pentest 101: Uji Keamanan Sistem Anda',                 'EP.20', 49],
            ['DevSecOps untuk Pengembang Pemerintah',                 'EP.21', 42],
            ['Keamanan IoT di Lingkungan Perkantoran',                'EP.22', 37],
            ['AI dan Ancaman Siber Masa Depan',                       'EP.23', 51],
            ['Budaya Keamanan Siber di Tempat Kerja',                 'EP.24', 34],
            ['Literasi Siber: Investasi SDM Digital Bali',            'EP.25', 56],
        ];

        foreach ($podcastData as $i => [$title, $episode, $duration]) {
            DB::table('podcast')->insert([
                'title'            => $title,
                'episode_number'   => $episode,
                'description'      => Str::limit('Membahas ' . lcfirst($title) . ' secara mendalam.', 100),
                'audio_url'        => 'https://open.spotify.com/episode/example' . ($i + 1),
                'thumbnail'        => null,
                'duration_minutes' => $duration,
                'is_published'     => ($i % 4 !== 0),
                'published_date'   => now()->subDays($i * 14)->format('Y-m-d'),
                'created_by'       => $adminId,
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
            DB::table('kabar')->insert([
                'slug'           => $slug,
                'title'          => $title,
                'excerpt'        => Str::limit('Artikel tentang ' . lcfirst($title) . ' untuk meningkatkan kesadaran keamanan siber ASN.', 100),
                'content'        => '<p>Ini adalah konten lengkap artikel <strong>' . $title . '</strong>.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Keamanan siber merupakan tanggung jawab bersama seluruh ASN Pemprov Bali.</p>',
                'category'       => $category,
                'status'         => ($i % 5 === 0) ? 'draft' : 'published',
                'published_at'   => ($i % 5 === 0) ? null : now()->subDays($i * 5),
                'published_date' => now()->subDays($i * 5)->format('Y-m-d'),
                'thumbnail'      => null,
                'view_count'     => rand(10, 500),
                'created_by'     => $adminId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }

        $this->command->info('✓ Seeder selesai: 25 komik, 25 podcast, 25 kabar.');
    }
}
