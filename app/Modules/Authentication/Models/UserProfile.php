<?php
// app/Modules/Authentication/Models/UserProfile.php
namespace App\Modules\Authentication\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'profile_picture',
        'preferences'
    ];

    protected $casts = [
        'preferences' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
