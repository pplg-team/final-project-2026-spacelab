<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Room extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code', 'name', 'building_id', 'floor', 'capacity', 'type', 'is_active', 'notes',
    ];

    public function timetableEntries(): HasManyThrough
    {
        return $this->hasManyThrough(
            TimetableEntry::class,
            RoomHistory::class,
            'room_id',
            'room_history_id',
            'id',
            'id'
        );
    }

    public function directTimetableEntries(): HasManyThrough
    {
        // Alias for timetableEntries for consistency with Teacher model
        return $this->timetableEntries();
    }

    public function scheduleEntries(): HasManyThrough
    {
        return $this->timetableEntries();
    }

    public function building()
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    // ← TAMBAHAN: relasi langsung ke RoomHistory
    public function roomHistories(): HasMany
    {
        return $this->hasMany(RoomHistory::class, 'room_id');
    }
}