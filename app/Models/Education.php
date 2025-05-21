<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $guarded = [];

    public function classes()
    {
        return $this->hasMany(AcademicClass::class, 'education_id');
    }
}

