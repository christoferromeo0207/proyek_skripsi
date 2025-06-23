<?php

namespace App\Models;


use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterBarang extends Model
{
    use HasFactory;

    protected $fillable = ['nama_barang'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
        
    public function categories() 
    {
        return $this->belongsTo(Category::class);
    }

}
