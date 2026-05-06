<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractionMatrix extends Model
{
    protected $guarded = [];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviewerDivision()
    {
        return $this->belongsTo(Division::class, 'reviewer_division_id');
    }

    public function targetDivision()
    {
        return $this->belongsTo(Division::class, 'target_division_id');
    }
}
