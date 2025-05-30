<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVideo extends Model
{
    protected $fillable = [
        'youtube_link',
        'title',
        'description',
        'user_id',
    ];
    public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}
