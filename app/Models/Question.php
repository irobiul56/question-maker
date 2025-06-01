<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function academicClass()
    {
        return $this->belongsTo(AcademicClass::class);
    }

     public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

     public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function lavel()
    {
        return $this->belongsTo(Level::class);
    }

    public function type()
    {
        return $this->belongsToMany(Type::class);
    }

    public function board()
    {
        return $this->belongsTo(board::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function cqoptions()
    {
        return $this->hasMany(Cqoption::class);
    }
}
