<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\MasterBarang;
use App\Models\MasterJasa;

class Transaction extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $casts = [
        'bukti_pembayaran' => 'array',
        'approval_rs'      => 'boolean',
        'approval_mitra'   => 'boolean',
    ];

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    protected $fillable = [
        'nama_produk','post_id', 'jumlah', 'merk', 'harga_satuan', 'total_harga',
        'tipe_pembayaran', 'bukti_pembayaran', 'pic_rs', 'approval_rs', 
        'pic_mitra', 'approval_mitra', 'status', 'jenis_transaksi', 'tanggal_mulai',
        'tanggal_selesai', 'master_barang_id', 'master_jasa_id'
    ];



    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('transaction')    
            ->logAll()                      
            ->logOnlyDirty()                
            ->dontSubmitEmptyLogs();       
    }


    public function getDescriptionForEvent(string $eventName): string
    {
        return "Transaction was {$eventName}";
    }

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

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'master_barang_id');
    }

    public function masterJasa()
    {
        return $this->belongsTo(MasterJasa::class, 'master_jasa_id');
    }
        
}
