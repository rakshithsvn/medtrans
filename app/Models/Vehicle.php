<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Fields that are mass assignable
    protected $fillable = [
        'id',
        'model',
        'type',
        'reg_no',
        'employee_id',
        'fuel_type',
        'km_in',
        'user_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
