<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDescription extends Model
{
    protected $fillable = ['work_experience_id', 'description'];

    public function workExperience()
    {
        return $this->belongsTo(WorkExperience::class);
    }
}
