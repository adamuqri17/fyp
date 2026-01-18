<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerOrder extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'grave_id', 'ledger_id', 'buyer_name', 'buyer_phone', 
        'transaction_date', 'amount', 'status'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function grave()
    {
        return $this->belongsTo(Grave::class, 'grave_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id');
    }
}