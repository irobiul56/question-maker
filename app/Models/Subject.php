<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $guarded = [];

    public function classs()
    {
        return $this->belongsTo(AcademicClass::class);
    }

    public function chapter()
    {
        return $this->hasMany(Chapter::class);
    }
}
