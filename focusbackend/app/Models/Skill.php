<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['resume_id', 'skill_name'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
