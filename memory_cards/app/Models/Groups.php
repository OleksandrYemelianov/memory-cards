<?php

namespace App\Models;

use App\Helpers\AppLangHelper;
use App\Scopes\UserScope;
use App\Traits\SerializeData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Groups extends Model
{
    use SerializeData;
    use HasFactory;

    protected $fillable = ['name', 'lang_id', 'user_id', 'qty'];

    /**
     * Boot the model and add global scopes and event listeners.
     */
    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope());

        static::creating(function ($group) {
            if (empty($group->user_id)) {
                $group->user_id = Auth::id();
            }
            if (empty($group->lang_id)) {
                $group->lang_id = AppLangHelper::getId();
            }
        });
    }
}
