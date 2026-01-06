<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'variant_id',
        'qty',
        'price',
        'notes',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function variant()
    {
        // Jika nama model Anda ShoeVariant dan tabelnya shoes_variants
        return $this->belongsTo(ShoesVariant::class, 'variant_id');
    }
}
