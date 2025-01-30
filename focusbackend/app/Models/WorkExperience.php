<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'company_name',
        'company_address',
        'position',
        'start_date',
        'end_date',
    ];

    // Define the inverse relationship with Resume
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    // Relationship to JobDescription (many-to-many)
    public function jobDescriptions()
    {
        return $this->hasMany(JobDescription::class); // Define one-to-many relationship with JobDescription
    }
}
