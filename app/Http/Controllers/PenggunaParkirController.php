<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PenggunaParkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PenggunaParkirController extends Controller
{
    protected $kategoriArray;

    public function __construct()
    {
        // Inisialisasi nilai enum kategori
        $this->kategoriArray = $this->getEnumValues('pengguna_parkir', 'kategori');
    }
    public function index(Request $request)
    {
        // Mendapatkan jumlah item per halaman dari parameter 'rows' (default ke 10)
        $perPage = $request->input('rows', 10);

        // Mendapatkan data pengguna dengan status aktif dan paginasi
        $pengguna = PenggunaParkir::where('status', 'aktif')->paginate($perPage);

        // Misalkan kategoriArray sudah didefinisikan di dalam controller
        $kategoriArray = [
            1 => 'Mahasiswa',
            2 => 'Dosen/Karyawan',
            3 => 'Tamu'
        ];

        // Mengirimkan data pengguna dan kategori ke view
        return view('pengelola.kelola_pengguna', [
            'pengguna' => $pengguna,
            'kategoriArray' => $kategoriArray, // Mengirimkan kategori ke view
            'perPage' => $perPage // Menyertakan perPage untuk form filter jumlah per halaman
        ]);
    }

    public function search(Request $request)
    {
        // Mengambil query dari input pencarian
        $perPage = $request->get('rows', 10); // Default 10 rows per page
        $query = $request->input('query');

        // Mencari data pengguna berdasarkan nama atau email
        $pengguna = PenggunaParkir::where('nama', 'LIKE', "%$query%")
            ->orWhere('email', 'LIKE', "%$query%")
            ->paginate($perPage);

        // Mengembalikan hasil pencarian ke view yang sama
        return view('pengelola.kelola_pengguna', compact('pengguna', 'query', 'perPage'));
    }


    protected function getEnumValues($table, $column)
    {
        // Ambil nilai enum dari kolom yang diberikan
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

        return []; // Kembalikan array kosong jika tidak ada hasil
    }

    public function create()
    {
        return view('pengelola.modal.edit_pengguna', [
            'kategoriArray' => $this->kategoriArray // Pass the kategoriArray for form options
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima
        $validated = $request->validate([
            'id_pengguna' => 'required_if:kategori,!Tamu|string|max:255|unique:pengguna_parkir,id_pengguna',
            'kategori' => 'required|string|in:' . implode(',', $this->getEnumValues('pengguna_parkir', 'kategori')),
            'nama' => 'required|string|regex:/^[A-Z][a-zA-Z\s]*$/|max:50',
            'email' => 'required|email|unique:pengguna_parkir,email|max:255',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Menangani upload foto jika ada
            $fotoPath = $request->hasFile('foto') ? $request->file('foto')->store('profil', 'public') : null;

            // Membuat instance PenggunaParkir baru
            $pengguna = new PenggunaParkir();
            $pengguna->id_pengguna = $request->kategori !== 'Tamu' ? $request->id_pengguna : 'Tamu_' . uniqid();
            $pengguna->nama = $request->nama;
            $pengguna->email = $request->email;
            $pengguna->password = $request->password; // Enkripsi password
            $pengguna->foto = $fotoPath;
            $pengguna->kategori = $request->kategori;
            $pengguna->status = 'aktif';
            $pengguna->save();

            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            // Jika terjadi error, log error dan tampilkan pesan kegagalan
            Log::error('Error saving pengguna: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengguna.');
        }
    }


    public function edit($id_pengguna)
    {
        // Menggunakan find untuk mencari berdasarkan id_pengguna
        $pengguna = PenggunaParkir::findOrFail($id_pengguna);

        return response()->json([
            'view' => view('pengelola.kelola_pengguna.edit', compact('pengguna'))->render()
        ]);
    }

    public function update(Request $request, $id_pengguna)
    {
        // Validasi data yang diterima
        $validated = $request->validate([
            'kategori' => 'required|string|in:' . implode(',', $this->getEnumValues('pengguna_parkir', 'kategori')),
            'nama' => 'required|string|regex:/^[A-Z][a-zA-Z\s]*$/|max:50',
            'email' => 'required|email|unique:pengguna_parkir,email,' . $id_pengguna . ',id_pengguna|max:255',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/', // Password bisa kosong jika tidak diubah
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Foto bisa kosong jika tidak diubah
        ]);

        try {
            // Menemukan pengguna berdasarkan id_pengguna
            $pengguna = PenggunaParkir::findOrFail($id_pengguna);

            // Menangani perubahan password jika ada
            if ($request->filled('password')) {
                $pengguna->password = $request->password; // Enkripsi password jika diubah
            }

            // Menangani upload foto jika ada file baru
            if ($request->hasFile('foto')) {
                // Cek dan hapus foto lama jika ada
                if ($pengguna->foto && Storage::exists('public/' . $pengguna->foto)) {
                    // Hapus foto lama dari penyimpanan
                    Storage::delete('public/' . $pengguna->foto);
                }

                // Upload foto baru ke lokasi yang sama
                $fotoPath = $request->file('foto')->store('profil', 'public');
                $pengguna->foto = $fotoPath; // Update path foto dengan file baru
            }

            // Update data pengguna selain ID Pengguna
            $pengguna->update($validated); // Update pengguna dengan data yang telah divalidasi

            // Redirect setelah sukses
            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            // Log error jika terjadi kesalahan
            Log::error('Error updating pengguna: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui pengguna.');
        }
    }


    public function destroy($id_pengguna)
    {
        Log::info("Menghapus pengguna dengan ID: $id_pengguna");

        // Pastikan id_pengguna adalah string dan cari dengan benar
        $pengguna = PenggunaParkir::find($id_pengguna);

        if ($pengguna) {
            // Jika ada foto terkait, hapus foto tersebut
            if ($pengguna->foto && Storage::exists('public/' . $pengguna->foto)) {
                Storage::delete('public/' . $pengguna->foto);
            }

            $pengguna->delete();
            Log::info("Pengguna berhasil dihapus.");
            return redirect()->route('pengelola.kelola_pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
        } else {
            Log::error("Pengguna dengan ID $id_pengguna tidak ditemukan.");
            return redirect()->route('pengelola.kelola_pengguna.index')->with('error', 'Pengguna tidak ditemukan.');
        }
    }
}
