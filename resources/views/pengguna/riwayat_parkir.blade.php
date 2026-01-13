@extends('layouts.pengguna')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .card {
        margin-bottom: 20px;
        border: 1px solid #000;
        /* Border hitam tebal */
    }

    .card-body {
        padding: 15px;
    }

    .table th {
        width: 30%;
        font-weight: 500;
        /* Medium weight */
        text-align: left;
        white-space: nowrap;
        /* Mencegah teks terpotong di layar kecil */
    }

    .table td:first-child {
        width: 5%;
        text-align: center;
    }

    .table td:last-child {
        width: 65%;
        word-break: break-word;
        /* Menyesuaikan teks jika terlalu panjang */
    }

    .riwayat-title {
        /* Default font size for desktop */
        font-weight: bold;

    }

    /* Responsivitas untuk tampilan mobile */
    @media (max-width: 576px) {

        .table th,
        .table td {
            font-size: 13px;
            /* Mengurangi ukuran font */
            white-space: nowrap;
            /* Tetap di satu baris */
        }

        .table {
            font-size: 14px;
            /* Memperkecil keseluruhan font tabel di layar kecil */
        }

        .card-body {
            padding: 10px;
            /* Mengurangi padding pada mobile */
        }

        .riwayat-title {
            font-size: 1.5rem;
        }
    }


    @media (max-width: 576px) {

        /* Adjust font size for mobile screens */
        .riwayat-title {
            font-size: 1.25rem;
        }
    }
</style>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h4 class="riwayat-title black">Riwayat Parkir Anda</h4>
    </div>
    <!-- Contoh data riwayat parkir -->
    <div class="card">
        <div class="card-body">
            <div class="details">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Nomor Plat</th>
                            <td>:</td>
                            <td>BP 7890 IU</td>
                        </tr>
                        <tr>
                            <th>Waktu Masuk</th>
                            <td>:</td>
                            <td>01-10-2024 08:00:00</td>
                        </tr>
                        <tr>
                            <th>Waktu Keluar</th>
                            <td>:</td>
                            <td>01-10-2024 11:15:00</td>
                        </tr>
                        <tr>
                            <th>Lama Parkir</th>
                            <td>:</td>
                            <td>03:15:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="details">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Nomor Plat</th>
                            <td>:</td>
                            <td>BP 1234 AB</td>
                        </tr>
                        <tr>
                            <th>Waktu Masuk</th>
                            <td>:</td>
                            <td>01-10-2024 09:00:00</td>
                        </tr>
                        <tr>
                            <th>Waktu Keluar</th>
                            <td>:</td>
                            <td>01-10-2024 12:00:00</td>
                        </tr>
                        <tr>
                            <th>Lama Parkir</th>
                            <td>:</td>
                            <td>03:00:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="details">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Nomor Plat</th>
                            <td>:</td>
                            <td>BP 1234 AB</td>
                        </tr>
                        <tr>
                            <th>Waktu Masuk</th>
                            <td>:</td>
                            <td>01-10-2024 09:00:00</td>
                        </tr>
                        <tr>
                            <th>Waktu Keluar</th>
                            <td>:</td>
                            <td>01-10-2024 12:00:00</td>
                        </tr>
                        <tr>
                            <th>Lama Parkir</th>
                            <td>:</td>
                            <td>03:00:00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tambahkan kartu tambahan sesuai kebutuhan -->
</div>

@endsection