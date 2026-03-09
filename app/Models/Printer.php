<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'printer_model_id',
        'name',
        'ip_address',
        'mac_address',
        'serial_number',
        'status',
        'location',
        'department',
        'total_pages_count',
        'black_white_pages',
        'color_pages',
        'simplex_pages',
        'duplex_pages',
        'total_print_length_meters',
        'configuration',
        'capabilities',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_interval_days',
        'snmp_community',
        'snmp_port',
        'snmp_version',
        'is_monitoring_enabled',
        'is_active',
        'last_polled_at',
    ];

    protected $casts = [
        'configuration' => 'array',
        'capabilities' => 'array',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'last_polled_at' => 'datetime',
        'is_monitoring_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function printerModel(): BelongsTo
    {
        return $this->belongsTo(PrinterModel::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(PrinterStatusLog::class);
    }

    public function printJobs(): HasMany
    {
        return $this->hasMany(PrintJob::class);
    }

    public function tonerLevels(): HasMany
    {
        return $this->hasMany(TonerLevel::class);
    }

    public function paperInventory(): HasMany
    {
        return $this->hasMany(PaperInventory::class);
    }

    public function latestTonerLevel()
    {
        return $this->hasOne(TonerLevel::class)->latestOfMany();
    }

    public function latestPaperInventory()
    {
        return $this->hasOne(PaperInventory::class)->latestOfMany();
    }

    public function getCurrentTonerLevelAttribute()
    {
        return $this->tonerLevels()->latest()->first();
    }

    public function getCurrentPaperLevelAttribute()
    {
        return $this->paperInventory()->latest()->first();
    }

    public function getIsLowOnTonerAttribute(): bool
    {
        $toner = $this->currentTonerLevel;
        return $toner && $toner->current_level < $toner->threshold_warning;
    }

    public function getIsCriticalOnTonerAttribute(): bool
    {
        $toner = $this->currentTonerLevel;
        return $toner && $toner->current_level < $toner->threshold_critical;
    }

    public function getMaintenanceStatusAttribute(): string
    {
        if (!$this->next_maintenance_date) {
            return 'not_scheduled';
        }

        if ($this->next_maintenance_date->isPast()) {
            return 'overdue';
        }

        if ($this->next_maintenance_date->diffInDays(now()) <= 7) {
            return 'due_soon';
        }

        return 'ok';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOnline($query)
    {
        return $query->whereIn('status', ['Ready', 'Printing']);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }
}
