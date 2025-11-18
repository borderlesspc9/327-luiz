<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nuvem',
        'phone',
        'birth_date',
        'marital_status',
        'license_date',
        'profession',
        'cpf',
        'rg',
        'cep',
        'state',
        'city',
        'neighborhood',
        'address',
        'number',
        'complement',
        'slug',
    ];

    protected $casts = [
        'birth_date' => 'date:d/m/Y',
        'license_date' => 'date:d/m/Y',
        'created_at' => 'date:d/m/Y',
        'updated_at' => 'date:d/m/Y',
    ];

    // Mutator genérico para datas
    public function setAttribute($key, $value)
    {
        if (in_array($key, ['birth_date', 'license_date']) && $value) {
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

    public function processes()
    {
        return $this->hasMany(Process::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class, 'client_file')->withTimestamps();
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
