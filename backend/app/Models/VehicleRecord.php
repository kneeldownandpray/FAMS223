<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pattern',
        'color',
        'vehicle_type',
        'image', // Allow image to be mass-assigned
    ];

    // Disable timestamps if you don't want Laravel to manage 'created_at' and 'updated_at'
    public $timestamps = true;

    // Optionally, define the path for the table
    protected $table = 'vehicle_records';
}
