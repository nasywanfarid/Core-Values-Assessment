<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    protected $guarded = [];

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
