<?php

namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\CourseManagement\Models\Department;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'description', 'department_id'];

    /**
     * Relation avec Department (Un programme appartient à un département).
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
