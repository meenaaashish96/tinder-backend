<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'location',
        'bio',
    ];

    public function images()
    {
        return $this->hasMany(ProfileImage::class);
    }

    public function swipes()
    {
        return $this->hasMany(Swipe::class);
    }
}