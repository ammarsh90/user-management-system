<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'chat_id',
        'event_type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all possible event types.
     */
    public static function getEventTypes()
    {
        return [
            'login' => 'User Login',
            'registration' => 'User Registration',
            'subscription' => 'Subscription Changes',
            'hwid_reset' => 'HWID Reset',
            'credit' => 'Credit Transactions',
            'system' => 'System Events',
        ];
    }
}