<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AmbulanceAllocation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function ambulanceRequest()
    {
        return $this->belongsTo(AmbulanceRequest::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }

    public function vehicleMovement()
    {
        return $this->hasOne(VehicleMovement::class, 'allocation_id')->whereIn('type', ['ward', 'help-desk']);
    }
}
