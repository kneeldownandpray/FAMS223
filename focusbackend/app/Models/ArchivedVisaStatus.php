<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivedVisaStatus extends Model
{
    use HasFactory;

    protected $table = 'archived_visa_statuses';

    protected $fillable = [
        'user_id',
        'visa_status_history_id',
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

    /**
     * Relationship to the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to the visa status history
     */
    public function visaStatusHistory()
    {
        return $this->belongsTo(VisaStatusHistory::class);
    }
}
