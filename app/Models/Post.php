<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Post extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = ['title', 'pic_mitra', 'category_id', 'slug', 'body',
    'phone', 'email', 'alamat', 'keterangan_bpjs', 'pembayaran', 
    'tanggal_awal', 'tanggal_akhir', 'file_path', 'PIC', 'parent_id', 
    'commission_percentage', 'commission_amount', 'transaction_value',];
   
    protected $with = ['category', 'picUser'];

    protected static $recordEvents = [
        'created',
        'updated',
        'deleted',
    ];

    protected $casts = [
        'tanggal_awal'  => 'date',
        'tanggal_akhir' => 'date',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];



    public function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    

    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? false, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('pic_mitra', 'like', "%{$search}%")
                ->orWhereHas('picUser', fn($q2) =>
                    $q2->where('name', 'like', "%{$search}%")
                );
            });
        });

        $query->when($filters['category'] ?? false, 
            fn($query, $category) => $query->whereHas('category',
                fn($q) => $q->where('slug', $category)
            )
        );
    }


    public function picUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'PIC');
    }

    public function mitraUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_mitra', 'name');
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'post_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('post')
            ->logFillable()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Telah {$eventName} data mitra: “{$this->title}”");
    }

    // anak perusahaan
    public function children()
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    // induk perusahaan 
    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

     public function calculateCommissionPercentage(): float
    {
        if (! $this->parent_id) {
            return 0.00;
        }
        $parent = $this->parent()->first();
        if ($parent && $parent->parent_id) {
            return 5.00;
        }
        return 7.00;
    }


    public function calculateCommissionAmount(): float
    {
        $pct = $this->calculateCommissionPercentage();
        $tv  = (float) $this->transaction_value;
        return $tv * ($pct / 100);
    }



}
