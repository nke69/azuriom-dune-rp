<?php

namespace Azuriom\Plugin\DuneRp\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpiceTransaction extends Model
{
    use HasTablePrefix, Loggable;

    protected $prefix = 'dune_rp_';

    protected $fillable = [
        'house_id', 'type', 'amount', 'reason', 'related_event_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public const TYPES = [
        'income' => 'Revenus',
        'expense' => 'Dépenses',
        'transfer' => 'Transfert',
        'tribute' => 'Tribut',
        'trade' => 'Commerce',
    ];

    public const TYPE_COLORS = [
        'income' => 'success',
        'expense' => 'danger',
        'transfer' => 'info',
        'tribute' => 'warning',
        'trade' => 'primary',
    ];

    public const TYPE_ICONS = [
        'income' => 'bi-plus-circle',
        'expense' => 'bi-dash-circle',
        'transfer' => 'bi-arrow-left-right',
        'tribute' => 'bi-gift',
        'trade' => 'bi-bag-check',
    ];

    /**
     * Get the house that made this transaction.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Get the related event if any.
     */
    public function relatedEvent(): BelongsTo
    {
        return $this->belongsTo(RpEvent::class, 'related_event_id');
    }

    /**
     * Get the type name.
     */
    public function getTypeName(): string
    {
        return self::TYPES[$this->type] ?? 'Inconnu';
    }

    /**
     * Get the type color for display.
     */
    public function getTypeColor(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'secondary';
    }

    /**
     * Get the type icon for display.
     */
    public function getTypeIcon(): string
    {
        return self::TYPE_ICONS[$this->type] ?? 'bi-circle';
    }

    /**
     * Format amount for display.
     */
    public function getFormattedAmount(): string
    {
        $prefix = $this->type === 'expense' ? '-' : '+';
        return $prefix . number_format($this->amount, 2) . ' tonnes';
    }

    /**
     * Get transaction description.
     */
    public function getDescription(): string
    {
        $description = $this->getTypeName();
        
        if ($this->reason) {
            $description .= ': ' . $this->reason;
        }
        
        if ($this->relatedEvent) {
            $description .= ' (Événement: ' . $this->relatedEvent->title . ')';
        }
        
        return $description;
    }

    /**
     * Check if transaction is positive for house.
     */
    public function isPositive(): bool
    {
        return in_array($this->type, ['income', 'tribute']);
    }
}
