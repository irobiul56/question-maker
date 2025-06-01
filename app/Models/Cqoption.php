<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cqoption extends Model
{

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
