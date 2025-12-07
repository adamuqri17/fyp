<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grave extends Model
{
    use HasFactory;

    protected $primaryKey = 'grave_id';
    protected $fillable = ['admin_id', 'section_id', 'latitude', 'longitude', 'status'];

    public function admin()
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function deceased()
    {
        return $this->hasOne(Deceased::class, 'grave_id');
    }
}
