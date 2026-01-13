<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatParkir;
use Carbon\Carbon;


class MonitoringParkirController extends Controller
{
    /**
     * Menampilkan halaman monitoring parkir tanpa filter pencarian.
     */
    public function index(Request $request)
    {
        // Mendapatkan jumlah baris per halaman dari input pengguna, default 10
        $perPage = $request->input('rows', 10);

        // Menampilkan semua data riwayat parkir dengan pagination
        $riwayatParkir = RiwayatParkir::with(['pengguna', 'kendaraan'])->paginate($perPage);

        // Mengirim data ke view
        return view('pengelola.monitoring', compact('riwayatParkir', 'perPage'));
    }

    /**
     * Menampilkan hasil pencarian berdasarkan query.
     */
    public function search(Request $request)
    {
        $query = $request->get('query', ''); // Dapatkan input pencarian
        $perPage = $request->get('rows', 10); // Default 10 rows per page

        // Menampilkan data yang sesuai dengan pencarian
        $riwayatParkir = RiwayatParkir::with(['pengguna', 'kendaraan'])
            ->where('plat_nomor', 'like', "%$query%")
            ->orWhere('id_pengguna', 'like', "%$query%")
            ->paginate($perPage);

        // Mengirim data ke view
        return view('pengelola.monitoring', compact('riwayatParkir', 'query', 'perPage'));
    }



    /**
     * Memproses scan QR code untuk masuk.
     */
    public function scanMasuk(Request $request)
    {
        $data = $request->validate([
            'id_pengguna' => 'required',
            'plat_nomor' => 'required',
        ]);

        // Mengecek apakah pengguna sudah ada di dalam parkiran
        $existingEntry = RiwayatParkir::where('id_pengguna', $data['id_pengguna'])
            ->whereNull('waktu_keluar')
            ->first();

        if ($existingEntry) {
            return response()->json(['message' => 'Pengguna ini sudah berada di dalam parkiran.'], 400);
        }

        // Membuat catatan masuk baru
        RiwayatParkir::create([
            'id_pengguna' => $data['id_pengguna'],
            'plat_nomor' => $data['plat_nomor'],
            'waktu_masuk' => Carbon::now(),
            'status_parkir' => 'Masuk',
        ]);

        return response()->json(['message' => 'Berhasil masuk parkir'], 200);
    }

    /**
     * Memproses scan QR code untuk keluar.
     */
    public function scanKeluar(Request $request)
    {
        $data = $request->validate([
            'id_pengguna' => 'required',
            'plat_nomor' => 'required',
        ]);

        // Mendapatkan data riwayat parkir yang belum di-set waktu keluar
        $riwayat = RiwayatParkir::where('id_pengguna', $data['id_pengguna'])
            ->where('plat_nomor', $data['plat_nomor'])
            ->whereNull('waktu_keluar')
            ->first();

        if (!$riwayat) {
            return response()->json(['message' => 'Tidak ditemukan catatan masuk untuk pengguna ini.'], 404);
        }

        // Mengisi waktu keluar dan menghitung lama parkir
        $riwayat->update([
            'waktu_keluar' => Carbon::now(),
            'status_parkir' => 'Keluar',
        ]);

        return response()->json(['message' => 'Berhasil keluar parkir'], 200);
    }
}
