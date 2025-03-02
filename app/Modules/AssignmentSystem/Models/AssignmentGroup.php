<?php

namespace App\Modules\AssignmentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'assignment_id'
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function members()
    {
        return $this->hasMany(AssignmentGroupMember::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'group_id');
    }
}
