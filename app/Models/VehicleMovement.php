<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class VehicleMovement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id'];

    public function ambulanceAllocation()
    {
        return $this->belongsTo(AmbulanceAllocation::class, 'allocation_id');
    }

    public function transportAllocation()
    {
        return $this->belongsTo(TransportAllocation::class, 'allocation_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }
}