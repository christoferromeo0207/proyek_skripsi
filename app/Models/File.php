<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'post_id', 'path', 'original_name', 'mime_type', 'size'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
