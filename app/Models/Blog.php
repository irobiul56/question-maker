<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{

    protected $guarded = [];

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
}
