<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = ['resume_id', 'certificate_name', 'year_received'];

    // Define relationship with Resume model
    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }
}
