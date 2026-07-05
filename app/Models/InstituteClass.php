<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstituteClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'institute_classes';

    protected $fillable = [
        'institute_id',
        'name',
        'bn_name',
        'numeric_value',
        'group',
        'display_order',
        'has_elective'
    ];

    protected $casts = [
        'numeric_value' => 'integer',
        'display_order' => 'integer',
        'has_elective' => 'boolean'
    ];

    /**
     * Get the institute that owns this class.
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the students for this class through student_academic_infos.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentAcademicInfo::class,
            'institute_class_id', // Foreign key on student_academic_infos
            'id', // Foreign key on students
            'id', // Local key on institute_classes
            'student_id' // Local key on student_academic_infos
        );
    }

    /**
     * Get active students for this class.
     */
    public function activeStudents()
    {
        return $this->students()->where('students.status', 'active');
    }

    /**
     * Get the student academic info for this class.
     */
    public function studentAcademicInfos()
    {
        return $this->hasMany(StudentAcademicInfo::class, 'institute_class_id');
    }

    /**
     * Get the sections for this class.
     */
    public function sections()
    {
        return $this->hasMany(Section::class, 'institute_class_id');
    }

    /**
     * Get the subjects for this class.
     */
    public function subjects()
    {
        return $this->belongsToMany(InstituteSubject::class, 'institute_class_institute_subjects', 'institute_class_id', 'institute_subject_id')
                    ->withPivot('type', 'full_marks', 'pass_marks', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get the exams for this class.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class, 'institute_class_id');
    }

    /**
     * Get the teachers for this class.
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher', 'institute_class_id', 'teacher_id')
                    ->withPivot('section_id', 'academic_year_id', 'is_active')
                    ->withTimestamps();
    }

    /**
     * Get the academic years for this class.
     */
    public function academicYears()
    {
        return $this->belongsToMany(AcademicYear::class, 'class_academic_year', 'institute_class_id', 'academic_year_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    /**
     * Get the current academic year for this class.
     */
    public function currentAcademicYear()
    {
        return $this->academicYears()->wherePivot('is_active', true)->first();
    }

    /**
     * Get the class teacher for current academic year.
     */
    public function classTeacher()
    {
        return $this->teachers()
                    ->wherePivot('academic_year_id', function($query) {
                        $query->select('id')->from('academic_years')->where('is_current', true);
                    })
                    ->first();
    }

    /**
     * Get the results for this class.
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'institute_class_id');
    }

    /**
     * Get the marks for this class.
     */
    public function marks()
    {
        return $this->hasMany(Mark::class, 'institute_class_id');
    }

    /**
     * Get the attendance for this class.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'institute_class_id');
    }

    /**
     * Scope a query to filter by institute.
     */
    public function scopeForInstitute($query, $instituteId)
    {
        return $query->where('institute_id', $instituteId);
    }

    /**
     * Scope a query to order by numeric value.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('numeric_value')->orderBy('display_order');
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeForGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only include classes with elective.
     */
    public function scopeWithElective($query)
    {
        return $query->where('has_elective', true);
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->whereHas('academicYears', function($q) use ($academicYearId) {
            $q->where('academic_year_id', $academicYearId);
        });
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
     * Get the group badge class for UI.
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
     * Get the group icon.
     */
    public function getGroupIconAttribute()
    {
        $icons = [
            'science' => 'fa-solid fa-flask',
            'commerce' => 'fa-solid fa-chart-line',
            'arts' => 'fa-solid fa-palette',
            'general' => 'fa-solid fa-users'
        ];
        return $icons[$this->group] ?? 'fa-solid fa-school';
    }

    /**
     * Get the full class name with group.
     */
    public function getFullNameAttribute()
    {
        $name = $this->name;
        if ($this->group) {
            $name .= ' (' . $this->group_label . ')';
        }
        return $name;
    }

    /**
     * Get the student count for this class.
     */
    public function getStudentCountAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Get the active student count for this class.
     */
    public function getActiveStudentCountAttribute()
    {
        return $this->activeStudents()->count();
    }

    /**
     * Get the section count.
     */
    public function getSectionCountAttribute()
    {
        return $this->sections()->count();
    }

    /**
     * Get the class type (Regular/With Elective).
     */
    public function getClassTypeAttribute()
    {
        return $this->has_elective ? 'With Elective' : 'Regular';
    }

    /**
     * Get the class type badge class.
     */
    public function getClassTypeBadgeClassAttribute()
    {
        return $this->has_elective 
            ? 'bg-yellow-100 text-yellow-800' 
            : 'bg-gray-100 text-gray-800';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active 
            ? 'bg-green-100 text-green-800' 
            : 'bg-gray-100 text-gray-800';
    }

    /**
     * Check if class has any student.
     */
    public function hasStudents()
    {
        return $this->students()->exists();
    }

    /**
     * Check if class has any active student.
     */
    public function hasActiveStudents()
    {
        return $this->activeStudents()->exists();
    }

    /**
     * Check if class has any section.
     */
    public function hasSections()
    {
        return $this->sections()->exists();
    }

    /**
     * Check if class has any subject.
     */
    public function hasSubjects()
    {
        return $this->subjects()->exists();
    }

    /**
     * Check if class has any exam.
     */
    public function hasExams()
    {
        return $this->exams()->exists();
    }

    /**
     * Check if class has any teacher.
     */
    public function hasTeachers()
    {
        return $this->teachers()->exists();
    }

    /**
     * Get the class name with numeric value.
     */
    public function getDisplayNameAttribute()
    {
        return $this->name . ' (Class ' . $this->numeric_value . ')';
    }

    /**
     * Get the class name in Bangla with numeric value.
     */
    public function getDisplayNameBnAttribute()
    {
        $bnName = $this->bn_name ?? $this->name;
        return $bnName . ' (শ্রেণি ' . $this->numeric_value . ')';
    }

    /**
     * Get the students count for a specific academic year.
     */
    public function getStudentCountForYear($academicYearId)
    {
        return $this->studentAcademicInfos()
                    ->where('academic_year_id', $academicYearId)
                    ->count();
    }
}