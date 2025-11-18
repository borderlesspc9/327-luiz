<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
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
