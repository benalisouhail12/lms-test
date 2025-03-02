<?php

namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\CourseManagement\Models\Program;
class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'description'];

    /**
     * Relation avec Program (Un département a plusieurs programmes).
     */
     public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
