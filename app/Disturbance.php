<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disturbance extends Model
{
    protected $fillable = [
        'title',
        'description',
        'latitude',
        'longitude',
        'area',
        'status',
        'type',
        'user_id'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute()
    {
        return $this->status === 'ongoing' ? 'Berlangsung' : 'Selesai';
    }

    public function getTypeTextAttribute()
    {
        $types = [
            'road_construction' => 'Konstruksi Jalan',
            'traffic_jam' => 'Macet',
            'accident' => 'Kecelakaan',
            'flood' => 'Banjir',
            'other' => 'Lainnya'
        ];

        return $types[$this->type] ?? 'Lainnya';
    }

    public function getMarkerColorAttribute()
    {
        return $this->status === 'ongoing' ? 'red' : 'green';
    }

    public function getMarkerIconAttribute()
    {
        $icons = [
            'road_construction' => 'fa-tools',
            'traffic_jam' => 'fa-car',
            'accident' => 'fa-exclamation-triangle',
            'flood' => 'fa-water',
            'other' => 'fa-exclamation-circle'
        ];

        return $icons[$this->type] ?? 'fa-exclamation-circle';
    }
}
