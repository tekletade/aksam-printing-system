<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TonerLevel extends Model
{
    use HasFactory;

    protected $table = 'toner_levels';

    protected $fillable = [
        'printer_id',
        'toner_color',
        'current_level',
        'threshold_warning',
        'threshold_critical',
        'is_low',
        'is_critical',
        'last_replaced_at',
        'toner_model',
        'toner_serial',
    ];

    protected $casts = [
        'last_replaced_at' => 'datetime',
        'is_low' => 'boolean',
        'is_critical' => 'boolean',
    ];

    public function printer(): BelongsTo
    {
        return $this->belongsTo(Printer::class);
    }

    /**
     * Check if toner is low
     */
    public function getIsLowAttribute(): bool
    {
        return $this->current_level <= $this->threshold_warning;
    }

    /**
     * Check if toner is critical
     */
    public function getIsCriticalAttribute(): bool
    {
        return $this->current_level <= $this->threshold_critical;
    }

    /**
     * Get color class for badge
     */
    public function getColorClassAttribute(): string
    {
        return match($this->toner_color) {
            'black' => 'gray',
            'cyan' => 'info',
            'magenta' => 'danger',
            'yellow' => 'warning',
            default => 'gray',
        };
    }
}
