<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = ['notulen_id', 'file_path', 'file_type', 'created_at', 'updated_at'];

    public function notulen()
    {
        return $this->belongsTo(Notulen::class);
    }
}
