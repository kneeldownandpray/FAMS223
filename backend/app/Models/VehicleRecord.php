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
        'vehicle_status' // Add vehicle_status to mass assignable fields
    ];

    public $timestamps = true;

    protected $table = 'vehicle_records';
}
