<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    public $timestamps = false;

    protected $primaryKey = 'plat_nomor';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pengguna',
        'jenis',
        'warna',
        'plat_nomor',
        'qr_code',
        'foto',
    ];

    // Relasi dengan PenggunaParkir
    public function penggunaParkir()
    {
        return $this->belongsTo(PenggunaParkir::class, 'id_pengguna', 'id_pengguna');
    }

    // Relasi dengan Riwayat Parkir
    public function riwayatParkir()
    {
        return $this->hasMany(RiwayatParkir::class, 'plat_nomor', 'plat_nomor');
    }
}
