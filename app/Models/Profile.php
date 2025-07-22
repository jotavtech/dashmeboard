<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'nickname',
        'bio',
        'profession',
        'mood',
        'public_agenda',
        'private_agenda',
        'daily_music',
        'fortune_cookie_message',
        'profile_image',
        'background_image',
        'background_image_url',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return "https://res.cloudinary.com/dzwfuzxxw/image/upload/v1752606826/{$this->profile_image}";
        }
        return null;
    }

    public function getBackgroundImageUrlAttribute()
    {
        if ($this->background_image) {
            return "https://res.cloudinary.com/dzwfuzxxw/image/upload/v1752606826/{$this->background_image}";
        }
        return null;
    }
}
