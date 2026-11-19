<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_category_id',
        'kode',
        'nama',
        'kapasitas',
        'lokasi',
        'fasilitas',
        'deskripsi',
        'status',
        'gedung',
        'lantai',
        'luas_m2',
        'foto_url',
    ];

    protected function casts(): array
    {
        return [
            'kapasitas' => 'integer',
            'lantai' => 'integer',
            'luas_m2' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'room_category_id');
    }

    public function roomCategory(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'room_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
