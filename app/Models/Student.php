<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'username',
        'password',
        'full_name',
        'school',
        'class',
        'class_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
    ];



    public function gameSessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }

    public function gameFaces(): HasMany
    {
        return $this->hasMany(GameFace::class);
    }

}
