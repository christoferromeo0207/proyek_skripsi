<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['post_id','user_id','subject','body','attachments','is_read'];
    protected $casts = [
      'attachments' => 'array',
      'is_read'     => 'boolean',
    ];

    public function post()   { return $this->belongsTo(Post::class); }
    public function sender() { return $this->belongsTo(User::class,'user_id'); }
}
