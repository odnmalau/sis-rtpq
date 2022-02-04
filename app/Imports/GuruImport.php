<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\Mapel;
use Maatwebsite\Excel\Concerns\ToModel;

class GuruImport implements ToModel
{
    public function model(array $row)
    {
        $max = Guru::max('id_card');
        $kode = $max + 1;
        if (strlen($kode) == 1) {
            $id_card = '0000'.$kode;
        } elseif (strlen($kode) == 2) {
            $id_card = '000'.$kode;
        } elseif (strlen($kode) == 3) {
            $id_card = '00'.$kode;
        } elseif (strlen($kode) == 4) {
            $id_card = '0'.$kode;
        } else {
            $id_card = $kode;
        }
        $mapel = Mapel::where('nama_mapel', $row[3])->first();
        if ($row[2] == 'L') {
            $foto = 'uploads/guru/35251431012020_male.jpg';
        } else {
            $foto = 'uploads/guru/23171022042020_female.jpg';
        }

        return new Guru([
            'id_card' => $id_card,
            'nama_guru' => $row[0],
            'pendidikan' => $row[1],
            'jk' => $row[2],
            'foto' => $foto,
            'mapel_id' => $mapel->id,
        ]);
    }
}
