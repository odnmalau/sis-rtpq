<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';
    protected $fillable = ['id_card', 'nip', 'nama_guru', 'mapel_id', 'kode', 'jk', 'telp', 'tmp_lahir', 'tgl_lahir', 'foto'];

    public function mapel()
    {
        return $this->belongsTo('App\Models\Mapel')->withDefault();
    }

    public function dsk($id)
    {
        $dsk = Nilai::where('guru_id', $id)->first();

        return $dsk;
    }
}