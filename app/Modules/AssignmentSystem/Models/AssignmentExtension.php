<?php

namespace App\Modules\AssignmentSystem\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'user_id', 'extended_due_date', 'reason', 'granted_by'
    ];

    protected $casts = [
        'extended_due_date' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
