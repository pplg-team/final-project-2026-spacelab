<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nis', 'nisn', 'users_id', 'avatar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function classHistories()
    {
        return $this->hasMany(ClassHistory::class, 'student_id');
    }


    public function getAvatarUrlAttribute()
{
    if (!$this->avatar) {
        return asset('images/default-avatar.png');
    }

    if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
        return $this->avatar;
    }

    return Storage::url($this->avatar);
}
}
