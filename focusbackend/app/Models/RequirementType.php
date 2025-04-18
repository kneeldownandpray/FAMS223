<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description',];

    public function userRequirements()
    {
        return $this->hasMany(UserRequirement::class);
    }
}
