<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterJasa extends Model
{
    use HasFactory;

    protected $fillable = ['nama_jasa', 'harga', 'kategori_id'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function category() 
    {
        return $this->belongsTo(Category::class);
    }

}
