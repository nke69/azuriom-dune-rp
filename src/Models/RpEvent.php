<?php

namespace Azuriom\Plugin\DuneRp\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\HasMarkdown;
use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class RpEvent extends Model
{
    use HasTablePrefix, HasMarkdown, Loggable;

    protected $prefix = 'dune_rp_';

    protected $fillable = [
        'title', 'description', 'organizer_id', 'organizer_house_id', 'event_date',
        'location', 'max_participants', 'spice_cost', 'reward_spice', 'event_type',
        'status', 'is_public',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'max_participants' => 'integer',
        'spice_cost' => 'decimal:2',
        'reward_spice' => 'decimal:2',
        'is_public' => 'boolean',
    ];

    public const EVENT_TYPES = [
        'harvest' => 'Récolte d\'épice',
        'combat' => 'Combat',
        'negotiation' => 'Négociation',
        'ceremony' => 'Cérémonie',
        'exploration' => 'Exploration',
        'trade' => 'Commerce',
        'council' => 'Conseil',
    ];

    public const STATUSES = [
        'planned' => 'Planifié',
        'ongoing' => 'En cours',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
    ];

    public const STATUS_COLORS = [
        'planned' => 'primary',
        'ongoing' => 'warning',
        'completed' => 'success',
        'cancelled' => 'secondary',
    ];

    public const TYPE_COLORS = [
        'harvest' => 'warning',
        'combat' => 'danger',
        'negotiation' => 'info',
        'ceremony' => 'light',
        'exploration' => 'success',
        'trade' => 'primary',
        'council' => 'secondary',
    ];

    /**
     * Get the event organizer.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Get the organizing house.
     */
    public function organizerHouse(): BelongsTo
    {
        return $this->belongsTo(House::class, 'organizer_house_id');
    }

    /**
     * Get related spice transactions.
     */
    public function spiceTransactions(): HasMany
    {
        return $this->hasMany(SpiceTransaction::class, 'related_event_id');
    }

    /**
     * Parse the description as markdown.
     */
    public function parseDescription(): string
    {
        return $this->parseMarkdown('description');
    }

    /**
     * Get the event type name.
     */
    public function getEventTypeName(): string
    {
        return self::EVENT_TYPES[$this->event_type] ?? 'Inconnu';
    }

    /**
     * Get the status name.
     */
    public function getStatusName(): string
    {
        return self::STATUSES[$this->status] ?? 'Inconnu';
    }

    /**
     * Get status color for display.
     */
    public function getStatusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /**
     * Get type color for display.
     */
    public function getTypeColor(): string
    {
        return self::TYPE_COLORS[$this->event_type] ?? 'secondary';
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->event_date->isFuture() && $this->status === 'planned';
    }

    /**
     * Check if the event is ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->status === 'ongoing';
    }

    /**
     * Check if the event is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if user can participate.
     */
    public function canUserParticipate(?User $user = null): bool
    {
        if (!$user) {
            return false;
        }

        if (!$this->is_public || !$this->isUpcoming()) {
            return false;
        }

        // Check if user has spice if cost required
        if ($this->spice_cost > 0) {
            $character = $user->characters()->where('is_approved', true)->first();
            if (!$character || !$character->house) {
                return false;
            }
            
            return $character->house->hasSpiceReserves($this->spice_cost);
        }

        return true;
    }

    /**
     * Get formatted event date.
     */
    public function getFormattedDate(): string
    {
        return $this->event_date->format('d/m/Y à H:i');
    }

    /**
     * Get relative time to event.
     */
    public function getRelativeTime(): string
    {
        if ($this->event_date->isPast()) {
            return 'Il y a ' . $this->event_date->diffForHumans();
        }

        return 'Dans ' . $this->event_date->diffForHumans();
    }
}
