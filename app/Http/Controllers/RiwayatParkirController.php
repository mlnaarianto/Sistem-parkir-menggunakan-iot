<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatParkirController extends Controller
{
    public function riwayatParkir()
    {
        // Just return the view without any data for now
        return view('pengguna.riwayat_parkir');
    }
}
