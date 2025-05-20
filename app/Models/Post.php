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

    protected $fillable = ['title', 'pic_mitra', 'slug', 'body','phone', 'email', 'alamat', 'keterangan_bpjs', 'pembayaran', 
    'tanggal_awal', 'tanggal_akhir', 'file_path', 'PIC'];
   
    protected $with = ['category', 'picUser'];




    public function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
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
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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


}
