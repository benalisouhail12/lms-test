<?php
namespace app\Modules\Analytics\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'metrics',
        'period',
        'data',
        'created_by'
    ];

    protected $casts = [
        'metrics' => 'array',
        'data' => 'array'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
