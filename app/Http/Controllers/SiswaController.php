<?php

namespace App\Http\Controllers;

use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class SiswaController extends Controller
{
    public function index()
    {
        $kelas = Kelas::OrderBy('nama_kelas', 'asc')->get();

        return view('admin.siswa.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'no_induk' => 'required|string|unique:siswa',
            'nama_siswa' => 'required',
            'jk' => 'required',
            'kelas_id' => 'required',
        ]);

        if ($request->foto) {
            $foto = $request->foto;
            $new_foto = date('siHdmY').'_'.$foto->getClientOriginalName();
            $foto->move('uploads/siswa/', $new_foto);
            $nameFoto = 'uploads/siswa/'.$new_foto;
        } else {
            if ($request->jk == 'L') {
                $nameFoto = 'uploads/siswa/52471919042020_male.jpg';
            } else {
                $nameFoto = 'uploads/siswa/50271431012020_female.jpg';
            }
        }

        Siswa::create([
            'no_induk' => $request->no_induk,
            'nis' => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'jk' => $request->jk,
            'kelas_id' => $request->kelas_id,
            'telp' => $request->telp,
            'tmp_lahir' => $request->tmp_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'foto' => $nameFoto,
        ]);

        return redirect()->back()->with('success', 'Berhasil menambahkan data siswa baru!');
    }

    public function show($id)
    {
        $id = Crypt::decrypt($id);
        $siswa = Siswa::findorfail($id);

        return view('admin.siswa.details', compact('siswa'));
    }

    public function edit($id)
    {
        $id = Crypt::decrypt($id);
        $siswa = Siswa::findorfail($id);
        $kelas = Kelas::all();

        return view('admin.siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nama_siswa' => 'required',
            'jk' => 'required',
            'kelas_id' => 'required',
        ]);

        $siswa = Siswa::findorfail($id);
        $user = User::where('no_induk', $siswa->no_induk)->first();
        if ($user) {
            $user_data = [
                'name' => $request->nama_siswa,
            ];
            $user->update($user_data);
        } else {
        }
        $siswa_data = [
            'nis' => $request->nis,
            'nama_siswa' => $request->nama_siswa,
            'jk' => $request->jk,
            'kelas_id' => $request->kelas_id,
            'telp' => $request->telp,
            'tmp_lahir' => $request->tmp_lahir,
            'tgl_lahir' => $request->tgl_lahir,
        ];
        $siswa->update($siswa_data);

        return redirect()->route('siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findorfail($id);
        $countUser = User::where('no_induk', $siswa->no_induk)->count();
        if ($countUser >= 1) {
            $user = User::where('no_induk', $siswa->no_induk)->first();
            $siswa->delete();
            $user->delete();

            return redirect()->back()->with('warning', 'Data siswa berhasil dihapus! (Silahkan cek trash data siswa)');
        } else {
            $siswa->delete();

            return redirect()->back()->with('warning', 'Data siswa berhasil dihapus! (Silahkan cek trash data siswa)');
        }
    }

    public function ubah_foto($id)
    {
        $id = Crypt::decrypt($id);
        $siswa = Siswa::findorfail($id);

        return view('admin.siswa.ubah-foto', compact('siswa'));
    }

    public function update_foto(Request $request, $id)
    {
        $this->validate($request, [
            'foto' => 'required',
        ]);

        $siswa = Siswa::findorfail($id);
        $foto = $request->foto;
        $new_foto = date('s'.'i'.'H'.'d'.'m'.'Y').'_'.$foto->getClientOriginalName();
        $siswa_data = [
            'foto' => 'uploads/siswa/'.$new_foto,
        ];
        $foto->move('uploads/siswa/', $new_foto);
        $siswa->update($siswa_data);

        return redirect()->route('siswa.index')->with('success', 'Berhasil merubah foto!');
    }

    public function view(Request $request)
    {
        $siswa = Siswa::OrderBy('nama_siswa', 'asc')->where('kelas_id', $request->id)->get();

        foreach ($siswa as $val) {
            $newForm[] = [
                'kelas' => $val->kelas->nama_kelas,
                'no_induk' => $val->no_induk,
                'nama_siswa' => $val->nama_siswa,
                'jk' => $val->jk,
                'foto' => $val->foto,
            ];
        }

        return response()->json($newForm);
    }

    public function cetak_pdf(Request $request)
    {
        $siswa = siswa::OrderBy('nama_siswa', 'asc')->where('kelas_id', $request->id)->get();
        $kelas = Kelas::findorfail($request->id);

        $pdf = PDF::loadView('siswa-pdf', ['siswa' => $siswa, 'kelas' => $kelas]);

        return $pdf->stream();
        // return $pdf->stream('jadwal-pdf.pdf');
    }

    public function kelas($id)
    {
        $id = Crypt::decrypt($id);
        $siswa = Siswa::where('kelas_id', $id)->OrderBy('nama_siswa', 'asc')->get();
        $kelas = Kelas::findorfail($id);

        return view('admin.siswa.show', compact('siswa', 'kelas'));
    }

    public function export_excel()
    {
        return Excel::download(new SiswaExport, 'siswa.xlsx');
    }

    public function import_excel(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx',
        ]);
        $file = $request->file('file');
        $nama_file = rand().$file->getClientOriginalName();
        $file->move('file_siswa', $nama_file);
        Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));

        return redirect()->back()->with('success', 'Data Siswa Berhasil Diimport!');
    }

    public function deleteAll()
    {
        $siswa = Siswa::all();
        if ($siswa->count() >= 1) {
            Siswa::whereNotNull('id')->delete();
            Siswa::withTrashed()->whereNotNull('id')->forceDelete();

            return redirect()->back()->with('success', 'Data table siswa berhasil dihapus!');
        } else {
            return redirect()->back()->with('warning', 'Data table siswa kosong!');
        }
    }
}
