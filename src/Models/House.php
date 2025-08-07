<?php

namespace Azuriom\Plugin\DuneRp\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\HasImage;
use Azuriom\Models\Traits\HasMarkdown;
use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class House extends Model
{
    use HasTablePrefix, HasImage, HasMarkdown, Loggable;

    protected $prefix = 'dune_rp_';

    protected $fillable = [
        'name', 'sigil_url', 'motto', 'description', 'leader_id',
        'homeworld', 'color', 'spice_reserves', 'influence_points', 'is_active',
    ];

    protected $casts = [
        'spice_reserves' => 'decimal:2',
        'influence_points' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $imageKey = 'sigil_url';

    /**
     * Get the house leader.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get all characters belonging to this house.
     */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    /**
     * Get spice transactions for this house.
     */
    public function spiceTransactions(): HasMany
    {
        return $this->hasMany(SpiceTransaction::class);
    }

    /**
     * Get events organized by this house.
     */
    public function events(): HasMany
    {
        return $this->hasMany(RpEvent::class, 'organizer_house_id');
    }

    /**
     * Parse the description as markdown.
     */
    public function parseDescription(): string
    {
        return $this->parseMarkdown('description');
    }

    /**
     * Get the house color as hex.
     */
    public function getColorHex(): string
    {
        return $this->color ?? '#8B4513';
    }

    /**
     * Check if house has enough spice reserves.
     */
    public function hasSpiceReserves(float $amount): bool
    {
        return $this->spice_reserves >= $amount;
    }

    /**
     * Add spice to house reserves.
     */
    public function addSpice(float $amount, string $reason = null): void
    {
        $this->increment('spice_reserves', $amount);
        
        SpiceTransaction::create([
            'house_id' => $this->id,
            'type' => 'income',
            'amount' => $amount,
            'reason' => $reason ?? 'Spice harvesting',
        ]);
    }

    /**
     * Remove spice from house reserves.
     */
    public function removeSpice(float $amount, string $reason = null): bool
    {
        if (!$this->hasSpiceReserves($amount)) {
            return false;
        }

        $this->decrement('spice_reserves', $amount);
        
        SpiceTransaction::create([
            'house_id' => $this->id,
            'type' => 'expense',
            'amount' => $amount,
            'reason' => $reason ?? 'Spice expenditure',
        ]);

        return true;
    }

    /**
     * Get active members count.
     */
    public function getActiveMembersCount(): int
    {
        return $this->characters()
                   ->where('status', 'alive')
                   ->where('is_approved', true)
                   ->count();
    }

    /**
     * Get house influence level.
     */
    public function getInfluenceLevel(): string
    {
        return match (true) {
            $this->influence_points >= 10000 => 'Empereur',
            $this->influence_points >= 5000 => 'Grande Maison Majeure',
            $this->influence_points >= 2000 => 'Grande Maison',
            $this->influence_points >= 500 => 'Maison Mineure',
            default => 'Maison Émergente',
        };
    }
}
