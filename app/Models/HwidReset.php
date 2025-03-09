<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HwidReset extends Model
{
    use HasFactory;

    // تحديد الجدول الذي يتعامل معه هذا الموديل (إذا كان اسم الجدول مختلفاً عن اسم الموديل)
    protected $table = 'hwid_resets';

    // تحديد الحقول التي يمكن تعبئتها (fillable)
    protected $fillable = [
        'user_id', 
        'reset_by', 
        'reset_type', 
        'old_hwid'
    ];

    // تحديد العلاقة بين HwidReset و User (إذا كانت هناك علاقة)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function resetBy()
    {
        return $this->belongsTo(User::class, 'reset_by');
    }
}
