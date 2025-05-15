<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulen extends Model
{
    use HasFactory;

    protected $table = 'notulen';
    protected $guarded = [];
    protected $fillable = ['pertemuan', 'tanggal', 'unit', 'jabatan', 'status', 'jenis', 'nama','hasil', 'no_hp'];
    
}
