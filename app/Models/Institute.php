<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $guarded = [];

     public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }
    
    public function instituteclass()
    {
        return $this->hasMany(InstituteClass::class);
    }
    
    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }
    
    public function institutesubject()
    {
        return $this->hasMany(InstituteSubject::class);
    }
}
