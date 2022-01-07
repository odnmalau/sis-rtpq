<?php

namespace App\Http\Controllers;

use App\Guru;
use App\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NilaiController extends Controller
{
    public function index()
    {
        $guru = Guru::where('id_card', Auth::user()->id_card)->first();
        $nilai = Nilai::where('guru_id', $guru->id)->first();
        return view('guru.nilai', compact('nilai', 'guru'));
    }

    public function create()
    {
        $guru = Guru::orderBy('kode')->get();
        return view('admin.nilai.index', compact('guru'));
    }

    public function store(Request $request)
    {
        $guru = Guru::where('kode', $request->guru_id)->first();

        Nilai::updateOrCreate(
            [
                'id' => $request->id
            ],
            [
                'guru_id' => $guru->id,
                'kkm' => $request->kkm,
                'deskripsi_a' => $request->predikat_a,
                'deskripsi_b' => $request->predikat_b,
                'deskripsi_c' => $request->predikat_c,
                'deskripsi_d' => $request->predikat_d,
            ]
        );

        return redirect()->back()->with('success', 'Data nilai kkm dan predikat berhasil diperbarui!');
    }
}
