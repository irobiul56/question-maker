<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicClass extends Model
{
    protected $guarded = [];

    public function education()
    {
        return $this->belongsTo(Education::class);
    }

    public function subject()
    {
        return $this->hasMany(Subject::class);
    }
}
