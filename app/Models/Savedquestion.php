<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Savedquestion extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_savedquestion');
    }
}
