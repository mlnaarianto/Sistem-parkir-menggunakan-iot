@extends('layouts.pengelola')

@section('title', 'Dashboard Pengelola')

@section('content')
<style>
    body {
        background-color: #f5f5f5;
        font-family: 'Roboto', Arial, sans-serif;
    }

    .card-spot {
        margin-bottom: 20px;
        border: 1px solid black;
        border-radius: 0;
    }

    .icon-spot {
        font-size: 2rem;
        color: black;
        margin-bottom: 10px;
    }

    .card-title-spot {
        font-size: 1.25rem;
        font-weight: bold;
        color: black;
        margin-bottom: 5px;
    }

    .card-text-spot {
        font-size: 1rem;
        color: black;
    }

    .highlight {
        background-color: #FFDC40;
        padding: 10px;
        border: 1px solid black;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }

    .card-body-spot {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-color: white;
        border: 1px solid black;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .chart {
        height: 200px;
    }

    @media (max-width: 767.98px) {
        .card-body {
            flex-direction: column;
            text-align: center;
        }

        .icon {
            margin-top: 10px;
        }
    }

    .greeting-message {
        color: #FFDC40;
        /* Text color */
        background-color: black;
        /* Background color */
        padding: 8px;
        /* Add some padding */
        border-radius: 5px;
        /* Rounded corners */
        display: flex;
        /* Aligns icon and text */
        align-items: center;
        /* Vertically center icon with text */
    }

    .greeting-message i {
        margin-left: 5px;
        /* Space between text and icon */
    }



    /* hp */
    .date-display {
        margin-left: auto;
        /* Pushes the date to the far right */
        text-align: right;
        /* Ensures the text is aligned to the right */
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3>Dashboard</h3>
            <p class="date-display mb-0">{{ $date }}</p>
        </div>
        <div class="col-12">
            <p class="greeting-message">
                Hallo, {{ Auth::user()->nama }} <i class="fas fa-smile"></i>
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-users"></i></div>
                <h6 class="card-title-spot">{{ $jumlahPengguna }}</h6>
                <p class="card-text-spot">Jumlah Pengguna</p>
            </div>
            <div class="highlight"></div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-car"></i></div>
                <h6 class="card-title-spot">{{ $jumlahParkirMasuk }}</h6>
                <p class="card-text-spot">Jumlah Parkir Masuk</p>
            </div>
            <div class="highlight"></div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card-body-spot">
                <div class="icon-spot"><i class="fas fa-car-side"></i></div>
                <h6 class="card-title-spot">{{ $jumlahParkirKeluar }}</h6>
                <p class="card-text-spot">Jumlah Parkir Keluar</p>
            </div>
            <div class="highlight"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Pengguna Parkir Hari ini</div>
                <div class="card-body">
                    <canvas class="chart" id="chart1"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Waktu Puncak Penggunaan</div>
                <div class="card-body">
                    <canvas class="chart" id="chart2"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">

                    Presentase Jenis Kendaraan</div>
                <div class="card-body">
                    <canvas class="chart" id="chart3"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-spot">
                <div class="card-header text-center" style="border-bottom: 1px solid black; background-color: white; font-weight: 500px; color: black;">
                    Statistik Kendaraan Masuk
                </div>


                <div class="card-body">
                    <canvas class="chart" id="chart4"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Success Message -->
@if (session('status') === 'success' && session('message'))
<div id="successModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document"> <!-- Center the modal -->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title" id="successModalLabel">Berhasil Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div>
                    <i class="fas fa-check-circle" style="color: green; font-size: 3em; animation: bounce 1s infinite; padding-bottom:10px;"></i>
                </div>
                <p>Selamat datang, Anda berhasil masuk!<br>"{{ Auth::user()->nama }}"</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="btn" style="background-color: #FFDC40; color: black; width: 100px;" data-dismiss="modal">Oke</button>

            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<!-- nampilin chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var ctx1 = document.getElementById('chart1').getContext('2d');
    var chart1 = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Mahasiswa', 'Dosen/Karyawan', 'Tamu'],
            datasets: [{
                label: 'Pengguna',
                data: [300, 200, 100],
                backgroundColor: ['#ff6384', '#36a2eb', '#4bc0c0']
            }]
        }
    });

    var ctx2 = document.getElementById('chart2').getContext('2d');
    var chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['00:00', '06:00', '12:00', '18:00', '24:00'],
            datasets: [{
                label: 'Mahasiswa',
                data: [10, 50, 25, 70, 30],
                borderColor: '#ff6384',
                fill: false
            }, {
                label: 'Dosen/Karyawan',
                data: [20, 30, 40, 60, 50],
                borderColor: '#36a2eb',
                fill: false
            }, {
                label: 'Tamu',
                data: [5, 15, 10, 20, 10],
                borderColor: '#4bc0c0',
                fill: false
            }]
        }
    });

    var ctx3 = document.getElementById('chart3').getContext('2d');
    var chart3 = new Chart(ctx3, {
        type: 'pie',
        data: {
            labels: ['Mobil', 'Motor'],
            datasets: [{
                data: [60, 40],
                backgroundColor: ['#ff6384', '#36a2eb']
            }]
        }
    });

    var ctx4 = document.getElementById('chart4').getContext('2d');
    var chart4 = new Chart(ctx4, {
        type: 'doughnut',
        data: {
            labels: ['Mobil', 'Motor', 'Sepeda'],
            datasets: [{
                data: [50, 30, 20],
                backgroundColor: ['#ff6384', '#36a2eb', '#4bc0c0']
            }]
        }
    });


    // <!-- nampilin modal berhasil masuk -->
    $(document).ready(function() {
        console.log('Page loaded');
        $('#successModal').modal('show');
    });
</script>
<!-- Tambahkan pustaka -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

@endsection