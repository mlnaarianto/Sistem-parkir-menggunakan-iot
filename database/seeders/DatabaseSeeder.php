<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenggunaParkir;
use App\Models\Kendaraan;
use App\Models\PengelolaParkir;
use App\Models\RiwayatParkir;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Data Pengelola Parkir (Harus dimasukkan terlebih dahulu)
        PengelolaParkir::create([
            'id_pengelola' => '1234567890',
            'nama' => 'Admin Parkir',
            'email' => 'adminparkir@gmail.com',
            'password' => 'Admin123!',
            'foto' => 'profil/default.png',
        ]);

        // 2. Data Pengguna
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

        // 3. Data Kendaraan
        $kendaraanData = [
            ['plat_nomor' => 'BP 1234 TA', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor1.jpg', 'id_pengguna' => '4342211050'],
            ['plat_nomor' => 'BP 5678 EL', 'jenis' => 'mobil', 'warna' => 'putih', 'foto' => 'kendaraan/mobil1.jpg', 'id_pengguna' => '4342211036'],
            ['plat_nomor' => 'BP 9101 EC', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor2.jpg', 'id_pengguna' => '4342211041'],
            ['plat_nomor' => 'BP 1121 AZ', 'jenis' => 'mobil', 'warna' => 'kuning', 'foto' => 'kendaraan/mobil2.jpg', 'id_pengguna' => '4342211045'],
            ['plat_nomor' => 'BP 3141 MA', 'jenis' => 'motor', 'warna' => 'putih', 'foto' => 'kendaraan/motor3.jpg', 'id_pengguna' => '4342211046'],
        ];

        if (!Storage::disk('public')->exists('images/qrcodes')) {
            Storage::disk('public')->makeDirectory('images/qrcodes');
        }

        foreach ($kendaraanData as $data) {
            $kendaraan = Kendaraan::create($data);

            // Membuat QR Code untuk kendaraan
            $qrCodePath = 'images/qrcodes/' . $kendaraan->plat_nomor . '.png';
            QrCode::format('png')->size(300)->generate($kendaraan->plat_nomor, storage_path('app/public/' . $qrCodePath));
            $kendaraan->qr_code = $qrCodePath;
            $kendaraan->save();
        }

        // 4. Data Riwayat Parkir (Pastikan data Pengelola sudah ada sebelum melakukan insert data Riwayat Parkir)
        // 4. Data Riwayat Parkir (Pastikan data Pengelola sudah ada sebelum melakukan insert data Riwayat Parkir)
        $riwayatParkirData = [
            ['id_pengguna' => '4342211050', 'plat_nomor' => 'BP 1234 TA', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(3), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211036', 'plat_nomor' => 'BP 5678 EL', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(2), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211050', 'plat_nomor' => 'BP 1234 TA', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now(), 'waktu_keluar' => null, 'status_parkir' => 'masuk'],
            ['id_pengguna' => '4342211036', 'plat_nomor' => 'BP 5678 EL', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now(), 'waktu_keluar' => null, 'status_parkir' => 'masuk'],
            ['id_pengguna' => '4342211041', 'plat_nomor' => 'BP 9101 EC', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(1), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211045', 'plat_nomor' => 'BP 1121 AZ', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(4), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            ['id_pengguna' => '4342211046', 'plat_nomor' => 'BP 3141 MA', 'id_pengelola' => '1234567890', 'waktu_masuk' => Carbon::now()->subHours(5), 'waktu_keluar' => Carbon::now(), 'status_parkir' => 'keluar'],
            // Data baru dengan status "masuk"
        ];

        foreach ($riwayatParkirData as $data) {
            RiwayatParkir::create($data);
        }
    }
}
