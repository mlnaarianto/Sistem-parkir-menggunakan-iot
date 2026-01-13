<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PenggunaParkir; // Menggunakan model PenggunaParkir
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\KendaraanRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class KelolaKendaraanController extends Controller
{
    protected $jenisArray;
    protected $warnaArray;

    public function __construct()
    {
        // Inisialisasi nilai enum jenis kendaraan dan warna kendaraan
        $this->jenisArray = $this->getEnumValues('kendaraan', 'jenis');
        $this->warnaArray = $this->getEnumValues('kendaraan', 'warna');
    }

    /**
     * Display a listing of all vehicles.
     */
    public function index(Request $request)
    {

        $perPage = $request->input('rows', 10);

        // Ambil semua kendaraan dengan pagination
        $kendaraan = Kendaraan::with('penggunaParkir')->paginate($perPage);

        // Kirim ke view dengan data yang dibutuhkan untuk pagination dan lainnya
        return view('pengelola.kelola_kendaraan', [
            'kendaraan' => $kendaraan,
            'jenisArray' => $this->jenisArray,
            'warnaArray' => $this->warnaArray,
            'perPage' => $perPage
        ]);
    }

    public function search(Request $request)
    {
        $perPage = $request->input('rows', 10);
        $query = $request->get('query');

        // Mencari kendaraan berdasarkan id_pengguna, plat_nomor, atau jenis
        $kendaraan = Kendaraan::where('id_pengguna', 'LIKE', "%$query%")
            ->orWhere('plat_nomor', 'LIKE', "%$query%")
            ->orWhere('jenis', 'LIKE', "%$query%")
            ->paginate($perPage);

        // Kembalikan hasil pencarian ke tampilan
        return view('pengelola.kelola_kendaraan', [
            'kendaraan' => $kendaraan,
            'query' => $query,
            'perPage' => $perPage,
            'jenisArray' => $this->jenisArray,
            'warnaArray' => $this->warnaArray,
        ]);
    }


    /**
     * Method for finding user by their ID.
     */
    public function findPenggunaById(Request $request)
    {
        // Ambil ID pengguna dari request
        $idPengguna = $request->input('id_pengguna');

        // Cari pengguna berdasarkan ID
        $pengguna = PenggunaParkir::where('id_pengguna', $idPengguna)->first();

        if ($pengguna) {
            // Jika pengguna ditemukan, kirimkan nama
            return response()->json([
                'status' => 'success',
                'nama' => $pengguna->nama
            ]);
        } else {
            // Jika pengguna tidak ditemukan, kirimkan pesan error
            return response()->json([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan'
            ]);
        }
    }

    /**
     * Show the form for creating a new vehicle.
     */
    public function create()
    {
        // Get a list of all pengguna_parkir (assuming you have a PenggunaParkir model)
        $penggunaParkir = PenggunaParkir::all(); // You can add more filtering if necessary

        return view('pengelola.kelola_kendaraan.create', compact('penggunaParkir'));
    }

    /**
     * Store a newly created vehicle in the database.
     */
    public function store(KendaraanRequest $request)
    {
        try {
            // Validate and store vehicle details
            $kendaraan = new Kendaraan();
            $kendaraan->plat_nomor = $request->plat_nomor;
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));

            // Assign the pengguna_parkir ID to the vehicle
            $kendaraan->id_pengguna_parkir = $request->id_pengguna_parkir;

            // Handle file upload for vehicle photo
            if ($request->hasFile('foto_kendaraan')) {
                $kendaraan->foto = $request->file('foto_kendaraan')->store('kendaraan', 'public');
            }

            // Generate QR code for the vehicle
            $this->generateQrCode($kendaraan);

            // Save the vehicle
            $kendaraan->save();

            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Failed to store vehicle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan kendaraan');
        }
    }



    /**
     * Show the form for editing a specific vehicle.
     */
    public function edit($platNomor)
    {
        // Find the vehicle by plat nomor and load the related pengguna_parkir
        $kendaraan = Kendaraan::with('penggunaParkir')->where('plat_nomor', $platNomor)->firstOrFail();
        $penggunaParkir = PenggunaParkir::all(); // Get all pengguna_parkir for the dropdown/select

        return view('pengelola.kelola_kendaraan.edit', compact('kendaraan', 'penggunaParkir'));
    }

    /**
     * Update the vehicle data.
     */
    public function update(KendaraanRequest $request, $platNomor)
    {
        // Find the vehicle by plat nomor
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->firstOrFail();

        try {
            // Update vehicle details
            $kendaraan->jenis = $request->jenis;
            $kendaraan->warna = ucwords(strtolower($request->warna));

            // Assign the pengguna_parkir ID if changed
            $kendaraan->id_pengguna = $request->id_pengguna;

            // If a new photo is uploaded, handle the old photo deletion and save the new one
            if ($request->hasFile('foto_kendaraan')) {
                // Delete old photo if a new one is uploaded
                if ($kendaraan->foto) {
                    Storage::disk('public')->delete($kendaraan->foto);
                }
                $kendaraan->foto = $request->file('foto_kendaraan')->store('kendaraan', 'public');
            }

            // Only regenerate the QR code if it doesn't exist
            if (!$kendaraan->qr_code_url) {
                $this->generateQrCode($kendaraan);
            }

            // Save the updated vehicle
            $kendaraan->save();

            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Failed to update vehicle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data kendaraan');
        }
    }

    /**
     * Delete the specified vehicle.
     */
    public function destroy($platNomor)
    {
        // Find the vehicle by plat nomor
        $kendaraan = Kendaraan::where('plat_nomor', $platNomor)->firstOrFail();

        try {
            // Delete the vehicle's photo if it exists
            if ($kendaraan->foto) {
                Storage::disk('public')->delete($kendaraan->foto);
            }

            // Delete the QR code if it exists
            if ($kendaraan->qr_code_url) {
                Storage::disk('public')->delete($kendaraan->qr_code_url);
            }

            // Delete the vehicle record
            $kendaraan->delete();

            return redirect()->route('pengelola.kelola_kendaraan.index')->with('success', 'Kendaraan berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Failed to delete vehicle: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kendaraan');
        }
    }

    /**
     * Generate QR code for the vehicle.
     */
    protected function generateQrCode(Kendaraan $kendaraan)
    {
        try {
            // Generate QR code content for the vehicle
            $qrCode = QrCode::size(200)->generate(url("/kendaraan/{$kendaraan->plat_nomor}"));

            // Store the QR code in the public storage
            $qrCodePath = 'qr_codes/' . $kendaraan->plat_nomor . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCode);

            // Save the URL of the QR code
            $kendaraan->qr_code_url = Storage::url($qrCodePath);
            $kendaraan->save();
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code for vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Get enum values from a given table and column.
     */
    protected function getEnumValues($table, $column)
    {
        $result = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);
        if (count($result) > 0) {
            $type = $result[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $enum = [];

            foreach (explode(',', $matches[1]) as $value) {
                $enum[] = trim($value, "'");
            }

            return $enum;
        }

        return []; // Return an empty array if no result
    }
}
