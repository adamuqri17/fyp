<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $primaryKey = 'section_id';
    protected $fillable = ['section_name', 'description'];

    public function graves()
    {
        return $this->hasMany(Grave::class, 'section_id');
    }
}
