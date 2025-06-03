<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_post_id',
        'child_post_id',
        'transaction_id',
        'commission_pct',
        'commission_amount',
    ];

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_post_id');
    }

    public function child()
    {
        return $this->belongsTo(Post::class, 'child_post_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
