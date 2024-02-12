<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $table = 'users_activity';

    protected $fillable = [
        'user_id',
        'activity',
        'ip_address',
        'device_info',
    ];
    
    // Set timestamps menjadi false untuk menghindari asumsi kolom 'updated_at'
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
