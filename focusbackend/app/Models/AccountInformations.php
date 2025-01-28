<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountInformations extends Model
{
    use HasFactory;

    protected $table = 'account_informations';

    protected $fillable = [
        'user_id',
        'requirements',
        'account_status',
        'account_acceptor_id',
        'deleted',
        'company',
        'skills_assessment',
    ];

    /**
     * Define the inverse relationship to User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
