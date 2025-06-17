<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'original_name', 'filename', 'path', 'mime_type', 'size'
    ];

    /**
     * Polymorphic relation ke Post atau Transaction
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}
