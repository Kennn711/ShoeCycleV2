<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone_number',
        'label',
        'full_address',
        'district',
        'village',
        'courier_note',
        'is_primary',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper untuk format alamat lengkap satu baris
    public function getFormattedAddressAttribute()
    {
        return "{$this->full_address}, Kec. {$this->district}, {$this->village}";
    }
}
