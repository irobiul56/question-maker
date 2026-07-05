<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAcademicInfo extends Model
{
    use HasFactory;

    protected $table = 'student_academic_infos';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'institute_class_id',
        'section_id',
        'roll_number',
        'monthly_fee',
        'group',
        'attendance_days',
        'is_promoted',
        'remarks'
    ];

    protected $casts = [
        'monthly_fee' => 'float',
        'attendance_days' => 'integer',
        'is_promoted' => 'boolean'
    ];

    /**
     * Get the student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the institute class.
     */
    public function instituteClass()
    {
        return $this->belongsTo(InstituteClass::class, 'institute_class_id');
    }

    /**
     * Get the section.
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the marks for this academic info.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    /**
     * Get the results for this academic info.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to filter by class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('institute_class_id', $classId);
    }

    /**
     * Scope a query to filter by section.
     */
    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Get the group label in Bangla.
     */
    public function getGroupLabelAttribute()
    {
        $groups = [
            'science' => 'বিজ্ঞান',
            'commerce' => 'বাণিজ্য',
            'arts' => 'মানবিক',
            'general' => 'সাধারণ'
        ];
        return $groups[$this->group] ?? $this->group;
    }

    /**
     * Get the group badge class.
     */
    public function getGroupBadgeClassAttribute()
    {
        $classes = [
            'science' => 'bg-blue-100 text-blue-800',
            'commerce' => 'bg-green-100 text-green-800',
            'arts' => 'bg-purple-100 text-purple-800',
            'general' => 'bg-gray-100 text-gray-800'
        ];
        return $classes[$this->group] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if student is promoted.
     */
    public function isPromoted()
    {
        return $this->is_promoted;
    }
}