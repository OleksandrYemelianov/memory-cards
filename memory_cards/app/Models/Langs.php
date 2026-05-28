<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\SerializeData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Langs extends Model
{
    use HasFactory;
    use SerializeData;

    protected $fillable = ['loc', 'name'];

    /**
     * Boot the model and apply global scopes.
     */
    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope());

        static::creating(function ($lang) {
            $lang->user_id = Auth::id();
        });
    }
}
