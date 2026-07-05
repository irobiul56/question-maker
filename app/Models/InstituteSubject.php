<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstituteSubject extends Model
{
    use HasFactory;

    protected $table = 'institute_subjects';

    protected $fillable = [
        'institute_id',
        'subject_id',
        'class_id',
        'teacher_id',
        'full_marks',
        'pass_marks',
        'is_active'
    ];

    /**
     * Get the marks for this institute subject.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'institute_subject_id');
    }

    /**
     * Get the subject.
     */
    public function subject()
    {
        return $this->belongsTo(InstituteSubject::class);
    }

    /**
     * Get the institute.
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the class.
     */
    public function class()
    {
        return $this->belongsTo(InstituteClass::class, 'class_id');
    }

    /**
     * Get the teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the exam subjects.
     */
    public function examSubjects()
    {
        return $this->hasMany(ExamInstituteSubjec::class);
    }
}