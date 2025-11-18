<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Status extends Model
{
    use HasFactory;

    protected $table = 'status';

    protected $fillable = [
        'title',
        'color',
        'color_text',
        'slug',
    ];

    public function status()
    {
        return $this->belongsToMany(Status::class, 'process_status_pivot', 'process_id', 'status_id')
            ->withPivot('is_active', 'user_id');
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

    public function user()
    {
        return $this->hasOneThrough(User::class, ProcessStatus::class, 'status_id', 'id', 'id', 'user_id');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
