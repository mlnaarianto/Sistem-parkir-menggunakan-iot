<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        @guest
        @if (Route::has('login'))
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars" style="color: black;"></i> <!-- Ikon menu tiga garis hitam -->
            </a>
        </li>
        @endif
        @else
        <li class="nav-item">
            <a data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars" style="color: black;"></i> <!-- Ikon menu tiga garis hitam -->
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                <img src="{{ asset('storage/' . (Auth::user()->foto)) }}" alt="" class="rounded-circle" height="30" width="30">
                {{ Auth::user()->nama }}
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <!-- Pengecekan tipe pengguna dan menyesuaikan rute profil -->
                @if (Auth::guard('pengelola')->check())
                <a class="dropdown-item" href="{{ route('pengelola.profile.show') }}">
                    <i class="fas fa-user"></i> {{ __('Profil') }}
                </a>
                @elseif (Auth::guard('pengguna')->check())
                <a class="dropdown-item" href="{{ route('pengguna.profile.show') }}">
                    <i class="fas fa-user"></i> {{ __('Profil') }}
                </a>
                @endif
                <a class="dropdown-item lo" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
        @endguest
    </ul>
</nav>

<style>
    /* CSS untuk navbar */
    .nav-link {
        color: black !important;
        /* Warna teks navbar hitam */
    }

    .nav-link:hover {
        color: black !important;
        /* Tetap hitam saat hover */
    }

    .dropdown-item {
        color: black !important;
        /* Warna teks dropdown hitam */
    }

    .dropdown-item:hover {
        background-color: rgba(0, 0, 0, 0.1);
        /* Efek hover untuk dropdown */
        color: black !important;
        /* Tetap hitam saat hover di dropdown */
    }

    /* Responsif: menyembunyikan sidebar dan menampilkan tombol */
    @media (max-width: 768px) {
        .main-header .navbar-nav .nav-item .nav-link {
            color: black !important;
            /* Warna hitam untuk ikon saat kecil */
        }
    }
</style>