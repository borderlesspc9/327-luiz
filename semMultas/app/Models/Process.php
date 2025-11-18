<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'service_id',
        'seller_id',
        'plate',
        'chassis',
        'renavam',
        'state_plate',
        'infraction_code',
        'agency',
        'ait',
        'process_value',
        'payment_method',
        'observation',
        'process_number',
        'deadline_date',
        'slug',
    ];

    protected $casts = [
        'deadline_date' => 'date:d/m/Y',
    ];

    // Mutator genérico para datas
    // public function setAttribute($key, $value)
    // {
    //     if (in_array($key, ['birth_date', 'license_date']) && $value) {
    //         $this->attributes[$key] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    //     } else {
    //         parent::setAttribute($key, $value);
    //     }
    // }
    public function setAttribute($key, $value)
    {
        if (in_array($key, ['birth_date', 'license_date', 'deadline_date']) && $value) {
            // Tenta criar a data a partir de diferentes formatos
            try {
                $date = Carbon::createFromFormat('d/m/Y', $value);
            } catch (\Exception $e) {
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $value);
                } catch (\Exception $e) {
                    $date = null;
                }
            }

            if ($date) {
                $this->attributes[$key] = $date->format('Y-m-d');
            } else {
                $this->attributes[$key] = $value; // Mantém o valor original se não puder ser formatado
            }
        } else {
            parent::setAttribute($key, $value);
        }
    }

    public function status()
    {
        return $this->belongsToMany(Status::class, 'process_status_pivot', 'process_id', 'status_id')
        ->withPivot('is_active', 'user_id', 'created_at')
        ->orderBy('process_status_pivot.created_at', 'desc');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function uniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = self::where('slug', $slug)->count();
        if($count > 0) {
            $newSlug = $slug . '-' . ($count + 1);

            while(self::where('slug', $newSlug)->count() > 0) {
                $count++;
                $newSlug = $slug . '-' . ($count + 1);
            }

            return $newSlug;
        }

        return $slug;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
