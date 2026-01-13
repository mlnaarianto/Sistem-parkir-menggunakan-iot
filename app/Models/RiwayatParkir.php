<?php

// app/Models/RiwayatParkir.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RiwayatParkir extends Model
{
    use HasFactory;

    protected $table = 'riwayat_parkir';
    protected $primaryKey = 'id_arkir';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'plat_nomor',
        'id_pengelola',
        'waktu_masuk',
        'waktu_keluar',
        'status_parkir',
    ];

    /**
     * Relasi ke model PenggunaParkir
     */
    public function pengguna()
    {
        return $this->belongsTo(PenggunaParkir::class, 'id_pengguna', 'id_pengguna');
    }

    /**
     * Relasi ke model Kendaraan
     */
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'plat_nomor', 'plat_nomor');
    }

    /**
     * Mendapatkan lama parkir berdasarkan waktu_masuk dan waktu_keluar
     */
    public function getLamaParkirAttribute()
    {
        if ($this->waktu_keluar) {
            $waktuMasuk = Carbon::parse($this->waktu_masuk);
            $waktuKeluar = Carbon::parse($this->waktu_keluar);
            return $waktuMasuk->diff($waktuKeluar)->format('%H:%I:%S');
        }

        return null;
    }
}
