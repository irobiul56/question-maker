<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function institute()
    {
        return $this->belongsTo(Institute::class);
    }
    
    public function academicInfos()
    {
        return $this->hasMany(StudentAcademicInfo::class);
    }
    
    public function currentAcademicInfo()
    {
        return $this->hasOne(StudentAcademicInfo::class)->whereHas('academicYear', function($q) {
            $q->where('is_current', true);
        });
    }
    
    public function marks()
    {
        return $this->hasMany(Mark::class);
    }
    
    public function results()
    {
        return $this->hasMany(Result::class);
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
