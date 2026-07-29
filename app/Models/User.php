<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'status', 'login_attempts', 'locked_until', 'last_login_ip', 'last_login_at', 'hosting_storage_limit_mb', 'referral_code', 'referred_by'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use \App\Traits\HasHashid;

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi untuk melihat riwayat pesanan klien
    public function clientOrders()
    {
        return $this->hasMany(JokiOrder::class, 'client_id');
    }

    // Relasi untuk melihat proyek apa saja yang sedang dikerjakan admin/dev
    public function workerOrders()
    {
        return $this->hasMany(JokiOrder::class, 'worker_id');
    }

    public function hostingProjects()
    {
        return $this->hasMany(HostingProject::class, 'user_id');
    }

    public function sharedHostingProjects()
    {
        return $this->belongsToMany(HostingProject::class, 'hosting_project_users', 'user_id', 'project_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function hostingBillings()
    {
        return $this->hasMany(HostingBilling::class, 'user_id');
    }

    public function hostingDatabases()
    {
        return $this->hasMany(HostingDatabase::class, 'user_id');
    }

    public function hostingNosqlDatabases()
    {
        return $this->hasMany(HostingNosqlDatabase::class, 'user_id');
    }

    public function hostingPgsqlDatabases()
    {
        return $this->hasMany(HostingPgsqlDatabase::class, 'user_id');
    }

    public function hasActiveHostingSubscription()
    {
        if (in_array($this->role, ['superadmin', 'admin_hosting'])) {
            return true;
        }

        return $this->hostingBillings()
            ->where('status', 'active')
            ->where('next_due_date', '>', now())
            ->exists();
    }

    public function hasActiveJokiSubscription()
    {
        // ... (bisa disesuaikan logicnya nanti jika ada langganan Joki)
        return false;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function affiliateCommissions()
    {
        return $this->hasMany(AffiliateCommission::class, 'user_id');
    }

    /**
     * Hosting Plan Definitions.
     * Keys: plan slug. Values: ['storage_mb', 'max_projects', 'price_key', 'default_price', 'label']
     */
    public static function hostingPlans(): array
    {
        return [
            'free'  => [
                'label'         => 'Free',
                'storage_mb'    => 256,
                'max_projects'  => 1,
                'price_key'     => 'plan_free_price',
                'promo_key'     => 'plan_free_promo',
                'default_price' => 0,
                'color'         => 'slate',
                'features'      => ['256 MB Storage', 'Maks. 1 Project', '1 MySQL Database', 'Subdomain Bawaan', 'Prioritas Support'],
            ],
            'starter'  => [
                'label'         => 'Starter',
                'storage_mb'    => 1024,
                'max_projects'  => 3,
                'price_key'     => 'plan_starter_price',
                'promo_key'     => 'plan_starter_promo',
                'default_price' => 15000,
                'color'         => 'indigo',
                'features'      => ['1 GB Storage', 'Maks. 3 Project', 'MySQL & PostgreSQL', 'Custom Domain + SSL', 'Prioritas Support'],
            ],
            'pro' => [
                'label'         => 'Pro',
                'storage_mb'    => 3072,
                'max_projects'  => 10,
                'price_key'     => 'plan_pro_price',
                'promo_key'     => 'plan_pro_promo',
                'default_price' => 30000,
                'color'         => 'violet',
                'features'      => ['3 GB Storage', 'Maks. 10 Project', 'MySQL, PostgreSQL & Redis', 'Custom Domain + SSL', 'Prioritas Support'],
            ],
            'business' => [
                'label'         => 'Business',
                'storage_mb'    => 10240,
                'max_projects'  => -1, // -1 = unlimited
                'price_key'     => 'plan_business_price',
                'promo_key'     => 'plan_business_promo',
                'default_price' => 75000,
                'color'         => 'amber',
                'features'      => ['10 GB Storage', 'Project Unlimited', 'Semua Database', 'Custom Domain + SSL', 'Prioritas Support'],
            ],
        ];
    }

    /**
     * Get the limits for a specific plan.
     */
    public static function getPlanLimits(string $plan): array
    {
        return static::hostingPlans()[$plan] ?? static::hostingPlans()['starter'];
    }

    public static function getPlanPricing(string $plan): array
    {
        $plans = static::hostingPlans();
        if (!isset($plans[$plan])) return ['normal' => 0, 'promo' => null, 'active' => 0];
        
        $p = $plans[$plan];
        $normal_price = isset($p['price_key']) 
            ? (int) \App\Models\Setting::val($p['price_key'], $p['default_price'] ?? 0)
            : ($p['default_price'] ?? 0);

        $promo_price = null;
        if (isset($p['promo_key'])) {
            $promo = \App\Models\Setting::val($p['promo_key'], null);
            if ($promo !== null && $promo !== '') {
                $promo_price = (int) $promo;
            }
        }

        return [
            'normal' => $normal_price,
            'promo' => $promo_price,
            'active' => $promo_price !== null ? $promo_price : $normal_price,
        ];
    }

    /**
     * Get the price for a specific plan (reads from Settings, falls back to default).
     */
    public static function getPlanPrice(string $plan): int
    {
        return static::getPlanPricing($plan)['active'];
    }

    /**
     * Get the max project count for the current user's plan.
     * Reads plan from the active billing. Returns -1 for unlimited.
     */
    public function getMaxProjects(): int
    {
        if (in_array($this->role, ['superadmin', 'admin_hosting'])) return -1;
        $activeBilling = $this->hostingBillings()
            ->where('status', 'active')
            ->where('next_due_date', '>', now())
            ->latest()
            ->first();
        $plan = $activeBilling->plan ?? 'starter';
        return static::getPlanLimits($plan)['max_projects'];
    }

    /**
     * Check if user can create more projects.
     */
    public function canCreateMoreProjects(): bool
    {
        $max = $this->getMaxProjects();
        if ($max === -1) return true;
        return $this->hostingProjects()->count() < $max;
    }

    public function canCreateDatabase(string $type = 'mysql'): array
    {
        $hasActive = $this->hasActiveHostingSubscription();
        $activeBilling = $hasActive ? $this->hostingBillings()->where('status', 'active')->where('next_due_date', '>', now())->latest()->first() : null;
        $currentPlan = $activeBilling->plan ?? 'free';

        $mysqlCount = \App\Models\HostingDatabase::where('user_id', $this->id)->count();
        $pgsqlCount = \App\Models\HostingPgsqlDatabase::where('user_id', $this->id)->count();
        $nosqlCount = \App\Models\HostingNosqlDatabase::where('user_id', $this->id)->count();

        if ($currentPlan === 'free') {
            if ($type !== 'mysql') {
                return ['allowed' => false, 'message' => 'Paket Free hanya mendukung MySQL Database. Silakan upgrade ke Starter/Pro/Business untuk menggunakan ' . strtoupper($type) . '.'];
            }
            if ($mysqlCount >= 1) {
                return ['allowed' => false, 'message' => 'Paket Free hanya mengizinkan maksimal 1 MySQL Database. Silakan upgrade paket untuk membuat database lebih banyak.'];
            }
        }

        if ($currentPlan === 'starter') {
            if ($type === 'redis' || $type === 'nosql') {
                return ['allowed' => false, 'message' => 'Paket Starter tidak mendukung Redis/NoSQL Database. Silakan upgrade ke Pro atau Business.'];
            }
        }

        return ['allowed' => true, 'message' => ''];
    }
}
