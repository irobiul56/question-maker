<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mark extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'institute_subject_id', // Use this instead of subject_id
        'class_id',
        'section_id',
        'teacher_id',
        'theory_marks',
        'practical_marks',
        'mcq_marks',
        'total_marks',
        'grade_point',
        'grade',
        'remarks',
        'is_absent',
        'is_reexamine'
    ];

    /**
     * Get the institute subject.
     */
    public function instituteSubject()
    {
        return $this->belongsTo(InstituteSubject::class, 'institute_subject_id');
    }

    /**
     * Get the exam.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class.
     */
    public function class()
    {
        return $this->belongsTo(InstituteClass::class);
    }

    /**
     * Get the teacher.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}