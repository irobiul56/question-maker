<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'institute_id',
        'name',
        'year',
        'session',
        'start_date',
        'end_date',
        'is_current'
    ];

    protected $casts = [
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    /**
     * Get the institute that owns the academic year.
     */
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }

    /**
     * Get the classes for this academic year.
     */
    public function instituteClasses()
    {
        return $this->hasMany(InstituteClass::class);
    }

    /**
     * Get the exams for this academic year.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the student academic info for this academic year.
     */
    public function studentAcademicInfos()
    {
        return $this->hasMany(StudentAcademicInfo::class);
    }

    /**
     * Scope a query to only include current academic year.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to filter by institute.
     */
    public function scopeForInstitute($query, $instituteId)
    {
        return $query->where('institute_id', $instituteId);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_current ? 'Current' : 'Inactive';
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_current 
            ? 'bg-green-100 text-green-800' 
            : 'bg-gray-100 text-gray-800';
    }

    /**
     * Get the formatted session.
     */
    public function getFormattedSessionAttribute()
    {
        return $this->session;
    }

    /**
     * Get the duration in days.
     */
    public function getDurationInDaysAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Check if academic year is active.
     */
    public function isActive()
    {
        return $this->is_current;
    }
}