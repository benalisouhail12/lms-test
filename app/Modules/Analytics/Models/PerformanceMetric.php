<?php
namespace app\Modules\Analytics\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'previous_value',
        'unit',
        'period',
        'date_recorded'
    ];

    protected $casts = [
        'value' => 'float',
        'previous_value' => 'float',
        'date_recorded' => 'datetime'
    ];
}
