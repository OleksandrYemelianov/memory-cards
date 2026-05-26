<?php

namespace App\Models;

use App\Scopes\UserScope;
use App\Traits\SerializeData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MemoryCard extends Model
{
    use HasFactory;
    use SerializeData;

    protected $fillable = [
        'foreign_word',
        'translation',
        'group_id',
    ];

    /**
     * Boot the model and apply global scope.
     */
    public static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new UserScope());

        static::creating(function ($card) {
            $card->color = self::generateRandomColor();
            $card->user_id = Auth::id();
        });
    }

    /**
     * Generate a random color in HEX format.
     */
    public static function generateRandomColor(): string
    {
        $minBrightness = 0x33;
        $maxBrightness = 0xCC;

        $red = mt_rand($minBrightness, $maxBrightness);
        $green = mt_rand($minBrightness, $maxBrightness);
        $blue = mt_rand($minBrightness, $maxBrightness);

        return sprintf('#%02X%02X%02X', $red, $green, $blue);
    }
}
