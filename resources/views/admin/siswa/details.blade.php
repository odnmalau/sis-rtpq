 @extends('layouts.admin')
@section('heading', 'Details Siswa')
@section('page')
    <li class="breadcrumb-item active"><a href="{{ route('siswa.index') }}">Siswa</a></li>
    <li class="breadcrumb-item active">Details Siswa</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-custom gutter-b">
                <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">@yield('heading')</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="dropdown dropdown-inline mr-2">
                            <a href="{{ route('siswa.kelas', Crypt::encrypt($siswa->kelas_id)) }}" class="btn btn-default btn-sm">
                                <span><i class="flaticon2-left-arrow-1"></i></span>Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row no-gutters ml-2 mb-2 mr-2">
                        <div class="col-md-4">
                            <img src="{{ asset($siswa->foto) }}" class="card-img img-details" alt="...">
                        </div>
                        <div class="col-md-1 mb-4"></div>
                        <div class="col-md-7">
                            <h5 class="card-title card-text mb-2">Nama : {{ $siswa->nama_siswa }}</h5>
                            <h5 class="card-title card-text mb-2">No. Induk : {{ $siswa->no_induk }}</h5>
                            <h5 class="card-title card-text mb-2">NIS : {{ $siswa->nis }}</h5>
                            <h5 class="card-title card-text mb-2">Kelas : {{ $siswa->kelas->nama_kelas }}</h5>
                            @if ($siswa->jk == 'L')
                            <h5 class="card-title card-text mb-2">Jenis Kelamin : Laki-laki</h5>
                            @else
                            <h5 class="card-title card-text mb-2">Jenis Kelamin : Perempuan</h5>
                            @endif
                            <h5 class="card-title card-text mb-2">Tempat Lahir : {{ $siswa->tmp_lahir }}</h5>
                            <h5 class="card-title card-text mb-2">Tanggal Lahir : {{ date('l, d F Y',
                                strtotime($siswa->tgl_lahir)) }}</h5>
                            <h5 class="card-title card-text mb-2">No. Telepon : {{ $siswa->telp }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $("#MasterData").addClass("active");
        $("#liMasterData").addClass("menu-open");
        $("#DataSiswa").addClass("active");
    </script>
@endsection