<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'ledger_id';
    
    protected $fillable = [
        'name', 'description', 'material', 'price', 'picture'
    ];

    public function orders()
    {
        return $this->hasMany(LedgerOrder::class, 'ledger_id');
    }
}