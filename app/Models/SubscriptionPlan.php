<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'duration_months',
        'price',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    /**
     * Get all subscriptions for this plan.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration_months == 1) {
            return '1 Month';
        } elseif ($this->duration_months == 12) {
            return '1 Year';
        } else {
            return $this->duration_months . ' Months';
        }
    }
}