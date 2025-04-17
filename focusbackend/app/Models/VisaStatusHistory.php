<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaStatusHistory extends Model
{
    use HasFactory;

    // Define the table if it differs from the plural form of the model name
    protected $table = 'visa_status_histories';

    // Define the fillable columns (mass assignable fields)
    protected $fillable = [
        'user_id',         // applicant (worker)
        'employer_id',     // employer
        'visa_status_id',  // visa status record
        'approved_by',     // user who approved
        'profession',      // profession snapshot
        'step',            // visa step
        'status',          // 1=completed, 0=not done, 3=skipped
        'completed_at',    // timestamp of completion
    ];

    // Define the relationships if necessary
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function visaStatus()
    {
        return $this->belongsTo(VisaStatus::class, 'visa_status_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
