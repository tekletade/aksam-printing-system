<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_code',
        'name',
        'company_name',
        'email',
        'phone',
        'alternate_phone',
        'tin_number',
        'type',
        'address',
        'city',
        'sub_city',
        'woreda',
        'house_number',
        'credit_limit',
        'outstanding_balance',
        'total_purchases',
        'total_orders',
        'telegram_chat_id',
        'whatsapp_number',
        'preferred_channel',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'total_purchases' => 'decimal:2',
        'metadata' => 'array',
    ];

    protected $attributes = [
        'status' => 'active',
        'type' => 'individual',
        'city' => 'Addis Ababa',
        'preferred_channel' => 'phone',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->sub_city ? 'Sub-city: ' . $this->sub_city : null,
            $this->woreda ? 'Woreda: ' . $this->woreda : null,
            $this->house_number ? 'House No: ' . $this->house_number : null,
            $this->city,
        ]);

        return implode(', ', $parts);
    }

    public function getIsVipAttribute(): bool
    {
        return $this->type === 'vip';
    }

    public function getHasCreditAttribute(): bool
    {
        return $this->credit_limit > 0;
    }

    public function getAvailableCreditAttribute(): float
    {
        return max(0, $this->credit_limit - $this->outstanding_balance);
    }

    public function getCreditStatusAttribute(): string
    {
        if ($this->credit_limit <= 0) return 'no_credit';
        if ($this->outstanding_balance >= $this->credit_limit) return 'maxed';
        if ($this->outstanding_balance > ($this->credit_limit * 0.8)) return 'warning';
        return 'good';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVip($query)
    {
        return $query->where('type', 'vip');
    }

    public function scopeWithCredit($query)
    {
        return $query->where('credit_limit', '>', 0);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('customer_code', 'like', "%{$search}%")
              ->orWhere('tin_number', 'like', "%{$search}%");
        });
    }

    // Boot method to generate customer code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = static::generateCustomerCode();
            }
        });
    }

    private static function generateCustomerCode(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastCustomer = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastCustomer) {
            $lastNumber = intval(substr($lastCustomer->customer_code, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "CUST-{$year}{$month}-{$newNumber}";
    }
}
