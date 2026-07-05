<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
     public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function class()
    {
        return $this->belongsTo(InstituteClass::class);
    }
    
    public function marks()
    {
        return $this->hasMany(Mark::class, 'student_id', 'student_id')
            ->where('exam_id', $this->exam_id);
    }
}
