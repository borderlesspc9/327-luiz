<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProcessStatus extends Pivot
{
    use HasFactory;

    protected $table = 'process_status_pivot';

    protected $fillable = [
        'process_id',
        'user_id',
        'status_id',
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'created_at' => 'datetime:d/m/Y H:i:s',
        'updated_at' => 'datetime:d/m/Y H:i:s',
    ];
}
