<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    // Menambahkan $fillable untuk mengizinkan pengisian massal kolom
    protected $fillable = [
        'name',
        'slug',
        'color',      
        'description', 
    ];

    // Definisi relasi hasMany ke model Post
    public function posts(): HasMany {
        return $this->hasMany(Post::class);
    }
}
