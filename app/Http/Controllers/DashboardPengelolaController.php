<?php

// app/Http/Controllers/DashboardPengelolaController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PenggunaParkir;
use Carbon\Carbon;


class DashboardPengelolaController extends Controller
{
    public function dashboard()
    {
        // Ambil data pengelola yang diperlukan
        $user = auth()->user();
        $date = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y');

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


        return view('pengelola.dashboard', compact('user', 'date', 'jumlahPengguna', 'jumlahParkirMasuk', 'jumlahParkirKeluar'));
    }

    public function kelolaPengguna()
    {
        // Ambil data pengguna parkir dari database
        $pengguna = PenggunaParkir::all(); // Sesuaikan query dengan kebutuhan

        // Tampilkan halaman dengan data pengguna
        return view('pengelola.kelolaPengguna', compact('pengguna'));
    }
}
