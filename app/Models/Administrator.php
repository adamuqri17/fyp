<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Crucial import
use Illuminate\Notifications\Notifiable;

class Administrator extends Authenticatable // Extends Authenticatable instead of Model
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'admin_id'; // Specifying your custom primary key
    
    protected $fillable = ['username', 'password', 'name', 'phone'];

    protected $hidden = [
        'password',
    ];

    // Relationships
    public function graves()
    {
        return $this->hasMany(Grave::class, 'admin_id');
    }

    public function deceased()
    {
        return $this->hasMany(Deceased::class, 'admin_id');
    }
}