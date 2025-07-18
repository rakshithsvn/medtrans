<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Fields that are mass assignable
    protected $fillable = [
        'id',
        'name',
        'employee_id',
        'address',
        'email',
        'mobile',
        'designation',
        'area_assigned',
        'type',
        'user_id',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
