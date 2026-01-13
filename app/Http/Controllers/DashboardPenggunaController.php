<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardPenggunaController extends Controller
{
    public function dashboard()
    {
        $date = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');


        // Ambil data pengguna yang diperlukan
        $user = auth()->user();

        // Mengambil detail pengguna dari tabel pengguna_parkir
        $penggunaDetail = DB::table('pengguna_parkir')
            ->where('id_pengguna', $user->id_pengguna) // Asumsikan 'id_pengguna' adalah kolom kunci utama
            ->first(['id_pengguna', 'nama', 'kategori', 'email', 'foto']); // Mengambil detail yang diperlukan

        // Mengambil kendaraan yang terkait dengan pengguna
        $kendaraan = DB::table('kendaraan')
            ->where('id_pengguna', $user->id_pengguna) // Ambil kendaraan berdasarkan pengguna
            ->first(['plat_nomor']); // Ambil plat nomor kendaraan

        // Buat path QR code
        $qrCodePath = isset($kendaraan) ? 'images/qrcodes/' . $kendaraan->plat_nomor . '.png' : null;

        // Ambil jumlah pengguna dari tabel pengguna_parkir
        $jumlahPengguna = DB::table('pengguna_parkir')->count();

        // Ambil jumlah parkir yang statusnya 'masuk' dari tabel riwayat_parkir
        $jumlahParkirMasuk = DB::table('riwayat_parkir')
            ->where('status_parkir', 'masuk') // Filter by status_parkir 'masuk'
            ->count(); // Count the entries

        // Ambil jumlah parkir yang statusnya 'keluar' dari tabel riwayat_parkir
        $jumlahParkirKeluar = DB::table('riwayat_parkir')
            ->where('status_parkir', 'keluar') // Filter by status_parkir 'keluar'
            ->count(); // Count the entries

        // Pastikan untuk mengirim semua variabel yang diperlukan ke tampilan
        return view('pengguna.dashboard', compact('user', 'date', 'penggunaDetail', 'jumlahPengguna', 'jumlahParkirMasuk', 'jumlahParkirKeluar', 'qrCodePath'));
    }
}
