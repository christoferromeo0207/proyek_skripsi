<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Marketing extends Authenticatable
{
    protected $table = 'marketing';
    protected $primaryKey = 'id_pegawai';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pegawai',
        'username_pegawai',
        'email',
        'posisi_pegawai',
        'no_telp',
        'tempat_lahir',
        'tanggal_lahir',
        'tanggal_masuk',
        'PIC',
    ];

    protected $hidden = ['password','remember_token'];
    

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'PIC'           => 'boolean',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(\App\Models\Post::class, 'PIC', 'id_pegawai');
    }
}
