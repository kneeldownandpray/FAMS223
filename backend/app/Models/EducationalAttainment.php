<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalAttainment extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'level',
        'institution',
        'period',
        'course',
        'major'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
