<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'credits',
        'status',
        'last_login',
        'last_login_ip',
        'hwid',
        'hwid_reset_at',
        'hwid_auto_reset_hours',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'hwid_reset_at' => 'datetime',
        'credits' => 'decimal:2',
    ];

    /**
     * Get the active subscription for the user.
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where('end_date', '>', Carbon::now())
            ->latest();
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get all login history for the user.
     */
    public function loginHistory()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * Get all transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all system logs for the user.
     */
    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class);
    }

    /**
     * Get all subscriptions sold by this reseller.
     */
    public function soldSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'reseller_id');
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has reseller role.
     */
    public function isReseller()
    {
        return $this->role === 'reseller';
    }

    /**
     * Check if user has an active subscription.
     */
/**
 * Check if user has an active subscription.
 */
public function hasActiveSubscription()
{
    try {
        return $this->activeSubscription()->exists();
    } catch (\Exception $e) {
        return false;
    }
}

    /**
     * Get time remaining until HWID can be reset automatically.
     */
    public function getTimeUntilHwidReset()
    {
        if (!$this->hwid_reset_at) {
            return null;
        }
        
        $now = Carbon::now();
        $resetTime = Carbon::parse($this->hwid_reset_at);
        
        if ($now->greaterThan($resetTime)) {
            return 0;
        }
        
        return $now->diffInSeconds($resetTime);
    }

    /**
     * Add credits to user account and log the transaction.
     */
    public function addCredits($amount, $description = null, $adminId = null, $subscriptionId = null)
    {
        $this->credits += $amount;
        $this->save();
        
        // Log transaction
        Transaction::create([
            'user_id' => $this->id,
            'admin_id' => $adminId,
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'related_subscription_id' => $subscriptionId
        ]);
        
        return true;
    }

    /**
     * Deduct credits from user account and log the transaction.
     */
    public function deductCredits($amount, $description = null, $adminId = null, $subscriptionId = null)
    {
        if ($this->credits < $amount) {
            return false;
        }
        
        $this->credits -= $amount;
        $this->save();
        
        // Log transaction
        Transaction::create([
            'user_id' => $this->id,
            'admin_id' => $adminId,
            'amount' => $amount,
            'type' => 'debit',
            'description' => $description,
            'related_subscription_id' => $subscriptionId
        ]);
        
        return true;
    }
}