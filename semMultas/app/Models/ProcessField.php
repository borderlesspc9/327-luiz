<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'type',
        'mask',
        'required',
        'description',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_process_field');
    }

}
