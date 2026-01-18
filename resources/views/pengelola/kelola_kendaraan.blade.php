
@extends('layouts.pengelola')
@section('title', 'Kelola Kendaraan')

@section('content')
<style>
    .border-black { border-color: black; }
    .bg-putih { background-color: #ffff; }
    .jarak-button { margin-right: 10px; }
    #qrCodeImage { max-width: 100%; max-height: 300px; }
    
    /* Upload Area Styling */
    .upload-area {
        border: 1px solid black; border-radius: 5px; padding: 20px;
        margin-bottom: 20px; text-align: center; cursor: pointer;
        position: relative; height: 250px; display: flex;
        align-items: center; justify-content: center; background-color: #f8f9fa;
    }
    .upload-area img {
        max-width: 100%; max-height: 100%; object-fit: cover;
        border-radius: 5px; display: none;
    }
    
    /* Input Group Styling */
    .input-group-text { border: 1px solid black; border-radius: 5px 0 0 5px; }
    .input-group-text i { border-radius: 0px 5px 5px 0; color: black; padding: 0 5px; }
    .form-control { border: 1px solid black; color: black; }
    .input-group .input-group-text { background-color: #f8f9fa; border: 1px solid black; }
    .form-control:focus { box-shadow: none; border-color: black; }
</style>

<div class="container">
    <h3>Kelola Kendaraan</h3>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3 d-flex justify-content-end">
                <button class="btn" style="background-color: #ffdc40; font-weight:bold;" onclick="openFormTambah()">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>

            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card border-black">
                <div class="card-body">
                    <div class="header-container d-flex justify-content-between mb-3">
                        <div>
                            <span class="ml-2">Tampilkan</span>
                            <form id="paginationForm" method="GET" action="{{ route('pengelola.kelola_kendaraan.index') }}" class="d-inline">
                                <select id="rows" name="rows" class="custom-select d-inline border-black" style="width: auto;" onchange="this.form.submit()">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                                <span class="ml-2">Baris</span>
                            </form>
                        </div>
                        <div class="search-container">
                            <form method="GET" action="{{ route('pengelola.kelola_kendaraan.search') }}" class="d-flex align-items-center">
                                <input type="text" name="query" class="form-control border-black" placeholder="Pencarian" style="width: auto;" value="{{ request()->get('query') }}">
                                <button class="btn search-btn ml-2" style="background-color: #ffdc40;" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($kendaraan->isEmpty())
                    <p class="mt-3">Tidak ada data yang ditemukan untuk "{{ request()->get('query') }}"</p>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: #ffdc40;">
                                <tr>
                                    <th>No</th>
                                    <th>ID Pengguna</th>
                                    <th>Plat Nomor</th>
                                    <th>Jenis Kendaraan</th>
                                    <th>QR Code</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-putih">
                                @foreach ($kendaraan as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data->penggunaParkir->id_pengguna }}</td>
                                    <td>{{ $data->plat_nomor }}</td>
                                    <td>{{ $data->jenis }}</td>
                                    <td>
                                        <button class="btn btn-success btn-sm" onclick="lihatQR('{{ Storage::url($data->qr_code) }}')">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-sm" 
                                            onclick="openFormEdit(
                                                '{{ $data->plat_nomor }}', 
                                                '{{ $data->jenis }}', 
                                                '{{ $data->warna }}', 
                                                '{{ $data->id_pengguna }}', 
                                                '{{ Storage::url($data->foto) }}'
                                            )">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        
                                        <button class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $data->plat_nomor }}')">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div>
                        <ul class="pagination d-flex justify-content-end">
                            <li class="page-item {{ $kendaraan->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $kendaraan->previousPageUrl() }}">&laquo;</a>
                            </li>
                            @foreach ($kendaraan->getUrlRange(1, $kendaraan->lastPage()) as $page => $url)
                            <li class="page-item {{ $kendaraan->currentPage() == $page ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach
                            <li class="page-item {{ $kendaraan->hasMorePages() ? '' : 'disabled' }}">
                                <a class="page-link" href="{{ $kendaraan->nextPageUrl() }}">&raquo;</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================================= --}}
{{-- AREA MODAL (DI LUAR LOOP) --}}
{{-- ================================================================================= --}}

<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog text-center" style="display: flex; justify-content: center; align-items: center;">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title">QR Code Kendaraan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center d-flex flex-column align-items-center">
                <div class="d-flex justify-content-center align-items-center" style="padding-bottom:10px;">
                    <i class="fas fa-question-circle" style="color: red; font-size: 3em; animation: bounce 1s infinite;"></i>
                </div>
                <p>Apakah Anda yakin ingin menghapus <br>data kendaraan pengguna ini?</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn" style="background-color: #FFFFFF; color: black; border: 1px solid black; width: 100px;" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background-color: #FFDC40; color: black; width: 100px;">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title">Tambah Kendaraan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('pengelola.kelola_kendaraan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 text-center" style="border-right: 1px solid black;">
                            <div class="upload-area" onclick="document.getElementById('uploadPhotoVehicle').click()">
                                <img id="previewVehicle" src="https://tse1.mm.bing.net/th?id=OIP.Mmwcms1DWRWNLhXw8uEEhgHaFo&pid=Api&P=0&h=180" style="display:block; max-width:50%;" class="img-fluid mb-3" />
                                <div id="labelPhotoVehicle" style="position: absolute; bottom: 0; width:100%; background-color: #FFDC40; color: black; padding: 10px;">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                                <input type="file" id="uploadPhotoVehicle" name="foto_kendaraan" style="display: none;" accept="image/*" onchange="previewImage(event, 'previewVehicle', 'labelPhotoVehicle')" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" id="id_pengguna_add" name="id_pengguna" class="form-control" placeholder="Masukkan ID Pengguna" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-car"></i></span>
                                    <input type="text" name="plat_nomor" class="form-control" placeholder="Masukkan Plat Nomor" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                                    <select name="jenis" class="form-control" required>
                                        <option value="">Pilih Jenis Kendaraan</option>
                                        @foreach($jenisArray as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-paint-brush"></i></span>
                                    <select name="warna" class="form-control" required>
                                        <option value="">Pilih Warna Kendaraan</option>
                                        @foreach($warnaArray as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid black;">
                        <button type="button" class="btn" style="background-color: #fff; border: 1px solid #000; width: 100px;" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" style="background-color: #FFDC40; width: 100px">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FFDC40;">
                <h5 class="modal-title">Edit Kendaraan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 text-center" style="border-right: 1px solid black;">
                            <div class="upload-area" onclick="document.getElementById('uploadPhotoVehicleEdit').click()">
                                <img id="previewVehicleEdit" src="" style="display:block; max-width:50%;" class="img-fluid mb-3" />
                                <div id="labelPhotoVehicleEdit" style="position: absolute; bottom: 0; width:100%; background-color: #FFDC40; color: black; padding: 10px;">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                                <input type="file" id="uploadPhotoVehicleEdit" name="foto_kendaraan" style="display: none;" accept="image/*" onchange="previewImage(event, 'previewVehicleEdit', 'labelPhotoVehicleEdit')" />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" id="id_pengguna_edit" name="id_pengguna" class="form-control" required readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-car"></i></span>
                                    <input type="text" id="plat_nomor_edit" name="plat_nomor" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                                    <select id="jenis_edit" name="jenis" class="form-control" required>
                                        <option value="">Pilih Jenis Kendaraan</option>
                                        @foreach($jenisArray as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-paint-brush"></i></span>
                                    <select id="warna_edit" name="warna" class="form-control" required>
                                        <option value="">Pilih Warna Kendaraan</option>
                                        @foreach($warnaArray as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid black;">
                        <button type="button" class="btn" style="background-color: #fff; border: 1px solid #000; width: 100px;" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn" style="background-color: #FFDC40; width: 100px">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Tampilkan Modal Tambah
    function openFormTambah() {
        $('#tambahModal').modal('show');
    }

    // 2. Tampilkan Modal Edit dan Isi Data
    function openFormEdit(platNomor, jenis, warna, idPengguna, fotoUrl) {
        // Isi nilai input form
        $('#plat_nomor_edit').val(platNomor);
        $('#jenis_edit').val(jenis);
        $('#warna_edit').val(warna);
        $('#id_pengguna_edit').val(idPengguna);
        
        // Atur Preview Foto
        if(fotoUrl) {
            $('#previewVehicleEdit').attr('src', fotoUrl).show();
            $('#labelPhotoVehicleEdit').hide();
        } else {
             $('#previewVehicleEdit').hide();
             $('#labelPhotoVehicleEdit').show();
        }

        // --- SOLUSI ERROR ROUTE ---
        // Kita gunakan placeholder '__plat_nomor__' agar Laravel tidak error saat render awal
        var urlTemplate = "{{ route('pengelola.kelola_kendaraan.update', '__plat_nomor__') }}";
        
        // Ganti placeholder dengan plat nomor asli menggunakan JavaScript
        var finalUrl = urlTemplate.replace('__plat_nomor__', platNomor);
        
        // Set action form
        $('#editForm').attr('action', finalUrl);

        // Tampilkan modal
        $('#editModal').modal('show');
    }

    // 3. Tampilkan QR Code
    function lihatQR(qrCodeUrl) {
        $('#qrCodeImage').attr('src', qrCodeUrl);
        $('#qrCodeModal').modal('show');
    }

    // 4. Konfirmasi Hapus (SOLUSI ROUTE DELETE)
    function confirmDelete(plat_nomor) {
        // Gunakan placeholder 'placeholder_plat'
        var urlTemplate = "{{ route('pengelola.kelola_kendaraan.delete', 'placeholder_plat') }}";
        
        // Ganti placeholder dengan data asli
        var finalUrl = urlTemplate.replace('placeholder_plat', plat_nomor);
        
        // Update form action dan tampilkan modal
        $('#deleteForm').attr('action', finalUrl);
        $('#deleteModal').modal('show');
    }

    // 5. Preview Image Logic
    function previewImage(event, previewId, labelId) {
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
            document.getElementById(labelId).style.display = 'none';
        };
        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // 6. Ajax Cari Pengguna (Hanya pada form Tambah)
    $(document).ready(function() {
        $('#id_pengguna_add').on('input', function() {
            let idPengguna = $(this).val();
            if (idPengguna.length > 3) { // Hanya cari jika karakter > 3 untuk hemat request
                $.ajax({
                    url: '{{ route("find.pengguna") }}', 
                    type: 'GET',
                    data: { id_pengguna: idPengguna },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Opsional: Beri indikator bahwa user ditemukan (misal border hijau)
                            $('#id_pengguna_add').css('border-color', 'green');
                        } else {
                            $('#id_pengguna_add').css('border-color', 'red');
                        }
                    },
                    error: function() {
                        console.log('Error checking user');
                    }
                });
            }
        });
    });
</script>
@endsection