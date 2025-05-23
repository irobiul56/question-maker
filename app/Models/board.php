<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class board extends Model
{
    protected $guarded = [];

    public function question()
    {
        return $this->hasMany(Question::class);
    }
}
