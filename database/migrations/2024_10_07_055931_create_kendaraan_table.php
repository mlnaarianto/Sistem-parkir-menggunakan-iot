<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKendaraanTable extends Migration
{
    public function up()
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            // Primary key
            $table->string('plat_nomor')->primary();

            // Foreign key
            $table->string('id_pengguna');

            // Enum jenis kendaraan
            $table->enum('jenis', ['Mobil', 'Motor']);

            // QR Code BOLEH NULL
            $table->string('qr_code')->nullable();

            // Foto kendaraan
            $table->string('foto');

            // Enum warna kendaraan
            $table->enum('warna', [
                'Merah',
                'Biru',
                'Hijau',
                'Kuning',
                'Hitam',
                'Putih',
                'Abu-abu',
                'Silver',
                'Oranye',
                'Cokelat',
                'Ungu',
                'Emas',
                'Pink'
            ]);

            // Foreign key constraint
            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna_parkir')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kendaraan');
    }
}
