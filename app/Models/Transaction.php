<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'bukti_pembayaran' => 'array',
        'approval_rs'      => 'boolean',
        'approval_mitra'   => 'boolean',
    ];

    protected $fillable = [
        'nama_produk','post_id', 'jumlah', 'merk', 'harga_satuan', 'total_harga',
        'tipe_pembayaran', 'bukti_pembayaran', 'pic_rs', 'approval_rs', 'pic_mitra', 'approval_mitra', 'status'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function rsUser()
    {
        return $this->belongsTo(User::class, 'pic_rs');
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'pic_mitra');
    }
    
}
