<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionTraining extends Model
{
    protected $table = 'commission_training';
    protected $fillable = ['features', 'label'];

    protected $casts = [
        'features' => 'array',
    ];
}
