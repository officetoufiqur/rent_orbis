<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class RentLog extends Model
{
    use GlobalStatus;

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pickUpLocation()
    {
        return $this->belongsTo(Location::class, 'pick_location','id');
    }

    public function dropUpLocation()
    {
        return $this->belongsTo(Location::class, 'drop_location');
    }


    public function scopeUpcoming($query)
    {
        return $query->where('pick_time', '>', now());
    }

    public function scopeRunning($query)
    {
        return $query->where('pick_time', '<', now())->where('drop_time', '>', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('drop_time', '<', now());
    }
}
