<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'institute_id',
        'name',
        'point',
        'marks_from',
        'marks_to',
        'remarks',
        'is_active',
        'display_order'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'point' => 'decimal:2',
        'marks_from' => 'decimal:2',
        'marks_to' => 'decimal:2',
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Get the school that owns the grade.
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the results for this grade.
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'final_grade', 'name');
    }

    /**
     * Scope a query to only include active grades.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Scope a query to filter by school.
     */
    public function scopeForSchool($query, $instituteId)
    {
        return $query->where('institute_id', $instituteId);
    }

    /**
     * Get grade by marks.
     */
    public static function getGradeByMarks($marks, $instituteId = null)
    {
        $query = self::active();
        
        if ($instituteId) {
            $query->forInstitute($instituteId);
        }

        return $query->where('marks_from', '<=', $marks)
                    ->where('marks_to', '>=', $marks)
                    ->first();
    }

    /**
     * Get GPA by marks.
     */
    public static function getGpaByMarks($marks, $instituteId = null)
    {
        $grade = self::getGradeByMarks($marks, $instituteId);
        return $grade ? $grade->point : 0.00;
    }

    /**
     * Get grade name by marks.
     */
    public static function getGradeNameByMarks($marks, $instituteId = null)
    {
        $grade = self::getGradeByMarks($marks, $instituteId);
        return $grade ? $grade->name : 'F';
    }

    /**
     * Get default grades.
     */
    public static function getDefaultGrades()
    {
        return [
            ['name' => 'A+', 'point' => 5.00, 'marks_from' => 80, 'marks_to' => 100, 'remarks' => 'Outstanding'],
            ['name' => 'A', 'point' => 4.00, 'marks_from' => 70, 'marks_to' => 79, 'remarks' => 'Excellent'],
            ['name' => 'A-', 'point' => 3.50, 'marks_from' => 60, 'marks_to' => 69, 'remarks' => 'Very Good'],
            ['name' => 'B', 'point' => 3.00, 'marks_from' => 50, 'marks_to' => 59, 'remarks' => 'Good'],
            ['name' => 'C', 'point' => 2.00, 'marks_from' => 40, 'marks_to' => 49, 'remarks' => 'Average'],
            ['name' => 'D', 'point' => 1.00, 'marks_from' => 33, 'marks_to' => 39, 'remarks' => 'Below Average'],
            ['name' => 'F', 'point' => 0.00, 'marks_from' => 0, 'marks_to' => 32, 'remarks' => 'Fail']
        ];
    }

    /**
     * Seed default grades for a school.
     */
    public static function seedDefaultGrades($instituteId)
    {
        $defaults = self::getDefaultGrades();

        foreach ($defaults as $index => $grade) {
            self::create([
                'institute_id' => $instituteId,
                'name' => $grade['name'],
                'point' => $grade['point'],
                'marks_from' => $grade['marks_from'],
                'marks_to' => $grade['marks_to'],
                'remarks' => $grade['remarks'],
                'display_order' => $index + 1,
                'is_active' => true
            ]);
        }

        return true;
    }

    /**
     * Calculate GPA from marks list.
     */
    public static function calculateGpa($marksList, $instituteId = null)
    {
        $totalPoints = 0;
        $totalSubjects = count($marksList);

        foreach ($marksList as $marks) {
            $grade = self::getGradeByMarks($marks, $instituteId);
            $totalPoints += $grade ? $grade->point : 0;
        }

        return $totalSubjects > 0 ? round($totalPoints / $totalSubjects, 2) : 0;
    }

    /**
     * Get grade color for UI.
     */
    public function getColorAttribute()
    {
        $colors = [
            'A+' => 'green',
            'A' => 'blue',
            'A-' => 'indigo',
            'B' => 'yellow',
            'C' => 'orange',
            'D' => 'red',
            'F' => 'dark-red'
        ];

        return $colors[$this->name] ?? 'gray';
    }

    /**
     * Get badge class for UI.
     */
    public function getBadgeClassAttribute()
    {
        $classes = [
            'green' => 'bg-green-100 text-green-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'red' => 'bg-red-100 text-red-800',
            'dark-red' => 'bg-red-200 text-red-900',
            'gray' => 'bg-gray-100 text-gray-800'
        ];

        return $classes[$this->color] ?? $classes['gray'];
    }

    /**
     * Get range display.
     */
    public function getRangeDisplayAttribute()
    {
        return $this->marks_from . ' - ' . $this->marks_to;
    }
}