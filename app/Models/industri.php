<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class industri extends Model
{
    protected $fillable =['nama', 'bidang_usaha', 'alamat', 'kontak', 'email', 'guru_pembimbing'];

    public function pkl()
    {
        return $this->hasMany(pkl::class);
    }

    public function guru ()
    {
        return $this->belongsTo(guru::class, 'guru_pembimbing');
    }
}