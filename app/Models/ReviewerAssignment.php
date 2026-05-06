<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewerAssignment extends Model
{
    protected $guarded = [];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'assignment_id');
    }
}
