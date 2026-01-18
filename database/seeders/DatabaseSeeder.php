<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use App\Models\PengelolaParkir;
use App\Models\RiwayatParkir;

// BACON QR CODE (SVG ONLY — TANPA IMAGICK)
use BaconQrCode\Writer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Pengelola Parkir
        |--------------------------------------------------------------------------
        */
        PengelolaParkir::create([
            'id_pengelola' => '1234567890',
            'nama' => 'Admin Parkir',
            'email' => 'adminparkir@gmail.com',
            'password' => 'Admin123!',
            'foto' => 'profil/default.png',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Pengguna Parkir
        |--------------------------------------------------------------------------
        */
        $penggunaData = [
            ['id_pengguna' => '4342211050', 'nama' => 'Tamaris Roulina S', 'email' => 'tama@gmail.com', 'password' => 'Tama123!', 'foto' => 'profil/Tamaris.png', 'kategori' => 'mahasiswa', 'status' => 'aktif'],
            ['id_pengguna' => '4342211036', 'nama' => 'Elsa Marina S', 'email' => 'elsa@gmail.com', 'password' => 'Elsa123!', 'foto' => 'profil/Elsa.jpg', 'kategori' => 'mahasiswa', 'status' => 'aktif'],
            ['id_pengguna' => '4342211041', 'nama' => 'Elicia Sandova', 'email' => 'elicia@gmail.com', 'password' => 'Elicia123!', 'foto' => 'profil/Elicia.jpg', 'kategori' => 'mahasiswa', 'status' => 'aktif'],
            ['id_pengguna' => '4342211045', 'nama' => 'Alifzidan Rizky', 'email' => 'alif@gmail.com', 'password' => 'Alifzidan123!', 'foto' => 'profil/Alifzidan.jpg', 'kategori' => 'mahasiswa', 'status' => 'aktif'],
            ['id_pengguna' => '4342211046', 'nama' => 'Maulana Arianto', 'email' => 'maulana@gmail.com', 'password' => 'Maulana123!', 'foto' => 'profil/Maulana.jpg', 'kategori' => 'mahasiswa', 'status' => 'aktif'],
        ];

        foreach ($penggunaData as $data) {
            PenggunaParkir::create($data);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Kendaraan + QR Code (SVG – PURE BACON)
        |--------------------------------------------------------------------------
        */
        $kendaraanData = [
            ['plat_nomor' => 'BP 1234 TA', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor1.jpg', 'id_pengguna' => '4342211050'],
            ['plat_nomor' => 'BP 5678 EL', 'jenis' => 'mobil', 'warna' => 'putih', 'foto' => 'kendaraan/mobil1.jpg', 'id_pengguna' => '4342211036'],
            ['plat_nomor' => 'BP 9101 EC', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor2.jpg', 'id_pengguna' => '4342211041'],
            ['plat_nomor' => 'BP 1121 AZ', 'jenis' => 'mobil', 'warna' => 'kuning', 'foto' => 'kendaraan/mobil2.jpg', 'id_pengguna' => '4342211045'],
            ['plat_nomor' => 'BP 3141 MA', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor3.jpg', 'id_pengguna' => '4342211046'],
        ];

        Storage::disk('public')->makeDirectory('images/qrcodes');

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        foreach ($kendaraanData as $data) {
            $kendaraan = Kendaraan::create($data);

            $filename = str_replace(' ', '_', $kendaraan->plat_nomor) . '.svg';
            $path = 'images/qrcodes/' . $filename;

            $svg = $writer->writeString($kendaraan->plat_nomor);

            Storage::disk('public')->put($path, $svg);

            $kendaraan->qr_code = $path;
            $kendaraan->save();
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Riwayat Parkir
        |--------------------------------------------------------------------------
        */
        $riwayatParkirData = [
            ['id_pengguna' => '4342211050', 'plat_nomor' => 'BP 1234 TA', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(3), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211036', 'plat_nomor' => 'BP 5678 EL', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(2), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211050', 'plat_nomor' => 'BP 1234 TA', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now(), 'waktu_keluar' => null, 'status_parkir' => 'masuk'],
        ];

        foreach ($riwayatParkirData as $data) {
            RiwayatParkir::create($data);
        }
    }
}
