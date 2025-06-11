<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['post_id','user_id', 'receiver_id','subject','body','attachments','is_read'];
    protected $casts = [
      'attachments' => 'array',
      'is_read'     => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /** Pesan ini milik Post mana */
    public function post()
    {
        return $this->belongsTo(\App\Models\Post::class, 'post_id', 'id');
    }
}
