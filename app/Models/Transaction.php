<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'address_id',
        'courier_id',
        'invoice',
        'subtotal',
        'shipping_cost',
        'admin_fee',
        'total_price',
        'snap_token',
        'payment_type',
        'pdf_url',
        'proof_of_delivery',
        'payment_status',
        'transaction_status',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    /**
     * Relasi ke User (Pembeli)
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Relasi ke User (Kurir Lokal)
     */
    public function courier()
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    /**
     * Relasi ke Alamat Pengiriman
     */
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
