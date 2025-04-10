<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'birthday',
        'gender',
        'age',
        'address',
        'email',
        'password',
        'account_type',
        'profile_picture',
        'skill_assessment',
        'visa_status',
    ];
    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Define a one-to-one relationship with AccountInformations.
     */
    public function accountInformation()
    {
        return $this->hasOne(AccountInformations::class, 'user_id', 'id');
    }
    public function videos()
    {
        return $this->hasMany(UserVideo::class, 'user_id', 'id');
    }
    public function resumes()
    {
        return $this->hasMany(Resume::class);
    }
    public function resume() // Change method name to singular
{
    return $this->hasOne(Resume::class);
}
public function applicantHired()
{
    return $this->hasMany(Hired::class, 'applicant_id', 'id');
}

    
}
