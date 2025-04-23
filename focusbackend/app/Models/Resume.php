<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'birth_address',
        'height',
        'weight',
        'objectives',
        'civil_status',
        'religion',
        'nationality',
        'contact_no',
        'profession',
        
    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function educationalAttainments()
    {
        return $this->hasMany(EducationalAttainment::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    // Relationship to Skills
    public function skills()
    {
        return $this->hasMany(Skill::class); // Define one-to-many relationship with Skill
    }

    // Relationship to Certifications
    public function certifications()
    {
        return $this->hasMany(Certification::class); // Define one-to-many relationship with Certification
    }

    // Relationship to User Videos
    public function userVideos()
    {
        return $this->hasMany(UserVideo::class, 'user_id', 'user_id');
    }
}
