<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $guarded = [];

    public function assignment()
    {
        return $this->belongsTo(ReviewerAssignment::class, 'assignment_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
}
