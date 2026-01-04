<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Deceased extends Model
{
    use HasFactory;

    protected $table = 'deceased'; 

    protected $primaryKey = 'deceased_id';
    
    protected $fillable = [
        'grave_id', 'admin_id', 'full_name', 'ic_number',
        'gender', 'date_of_birth', 'date_of_death',
        'time_of_death', 'burial_date', 'notes'
    ];

    public function grave()
    {
        return $this->belongsTo(Grave::class, 'grave_id');
    }

    public function admin()
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}