<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequirement extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'requirement_type_id', 'file_path', 'original_name','status','note'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requirementType()
    {
        return $this->belongsTo(RequirementType::class);
    }
}
