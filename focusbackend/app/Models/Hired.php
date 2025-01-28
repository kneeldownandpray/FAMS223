<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hired extends Model
{
    protected $table = 'hired';
    protected $fillable = ['employer_id', 'applicant_id', 'approval_of_admin'];

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function accountInformation()
    {
        return $this->hasOne(AccountInformations::class, 'user_id', 'id');
    }
}
