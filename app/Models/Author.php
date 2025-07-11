<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;

    // Tabel yang digunakan
    protected $table = 'authors';

    // Kolom yang dapat diisi
    protected $fillable = ['name', 'email', 'phone'];

    // Define any relationships if necessary
    public function posts()
    {
        return $this->hasMany(Post::class);
    }


}
