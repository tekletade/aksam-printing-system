<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaperInventory extends Model
{
    use HasFactory;

    /**
     * Specify the table name since it doesn't follow Laravel's plural convention
     */
    protected $table = 'paper_inventory';

    protected $fillable = [
        'printer_id',
        'tray_name',
        'paper_size',
        'paper_type',
        'current_sheets',
        'max_capacity',
        'threshold_reorder',
        'threshold_critical',
        'is_low',
        'is_empty',
        'last_refilled_at',
    ];

    protected $casts = [
        'last_refilled_at' => 'datetime',
        'is_low' => 'boolean',
        'is_empty' => 'boolean',
    ];

    public function printer(): BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }

    /**
     * Get the usage percentage
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->max_capacity <= 0) {
            return 0;
        }
        return round(($this->current_sheets / $this->max_capacity) * 100, 1);
    }

    /**
     * Check if paper is low
     */
    public function getIsLowAttribute(): bool
    {
        return $this->current_sheets <= $this->threshold_reorder;
    }

    /**
     * Check if paper is critical
     */
    public function getIsCriticalAttribute(): bool
    {
        return $this->current_sheets <= $this->threshold_critical;
    }
}
