<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_received',
        'interview_employer_confirmation',
        'requirements',
        'skill_assessment',
        'visa_preparation',
        'visa_lodged',
        'medical_biometrics',
        'awaiting_decision',
        'visa_outcome',
        'ready_to_fly',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
