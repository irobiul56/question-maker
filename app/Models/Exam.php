<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    
    public function class()
    {
        return $this->belongsTo(InstituteClass::class);
    }
    
    public function subjects()
    {
        return $this->belongsToMany(InstituteSubject::class, 'exam_subjects')
            ->withPivot('exam_date', 'start_time', 'end_time', 'full_marks', 'pass_marks');
    }
    
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    
    public function results()
    {
        return $this->hasMany(Result::class);
    }
}
