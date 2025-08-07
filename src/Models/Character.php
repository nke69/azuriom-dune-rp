<?php

namespace Azuriom\Plugin\DuneRp\Models;

use Azuriom\Models\Traits\HasTablePrefix;
use Azuriom\Models\Traits\HasUser;
use Azuriom\Models\Traits\HasImage;
use Azuriom\Models\Traits\HasMarkdown;
use Azuriom\Models\Traits\Loggable;
use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    use HasTablePrefix, HasUser, HasImage, HasMarkdown, Loggable;

    protected $prefix = 'dune_rp_';

    protected $fillable = [
        'name', 'title', 'house_id', 'biography', 'birthworld', 'age', 'status',
        'spice_addiction_level', 'special_abilities', 'avatar_url', 'is_public', 'is_approved',
    ];

    protected $casts = [
        'age' => 'integer',
        'spice_addiction_level' => 'integer',
        'special_abilities' => 'array',
        'is_public' => 'boolean',
        'is_approved' => 'boolean',
    ];

    protected $imageKey = 'avatar_url';

    public const STATUSES = [
        'alive' => 'Vivant',
        'missing' => 'Disparu',
        'deceased' => 'Décédé',
        'exiled' => 'Exilé',
    ];

    public const ADDICTION_LEVELS = [
        0 => 'Aucune',
        1 => 'Légère',
        2 => 'Modérée',
        3 => 'Forte',
        4 => 'Critique',
    ];

    public const SPECIAL_ABILITIES = [
        'prescience' => 'Prescience',
        'voice' => 'La Voix',
        'mentat' => 'Mentat',
        'bene_gesserit' => 'Bene Gesserit',
        'fremen_skills' => 'Compétences Fremen',
        'spice_trance' => 'Transe d\'Épice',
        'stillsuit_mastery' => 'Maîtrise du Distille',
        'sandwalk' => 'Marche des Sables',
    ];

    /**
     * Get the user who owns this character.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the house this character belongs to.
     */
    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Parse the biography as markdown.
     */
    public function parseBiography(): string
    {
        return $this->parseMarkdown('biography');
    }

    /**
     * Get the character's full title.
     */
    public function getFullTitle(): string
    {
        $title = $this->name;
        
        if ($this->title) {
            $title = $this->title . ' ' . $title;
        }
        
        if ($this->house) {
            $title .= ' of House ' . $this->house->name;
        }
        
        return $title;
    }

    /**
     * Get the addiction level name.
     */
    public function getAddictionLevelName(): string
    {
        return self::ADDICTION_LEVELS[$this->spice_addiction_level] ?? 'Inconnue';
    }

    /**
     * Get the status name.
     */
    public function getStatusName(): string
    {
        return self::STATUSES[$this->status] ?? 'Inconnu';
    }

    /**
     * Check if character has a specific ability.
     */
    public function hasAbility(string $ability): bool
    {
        return in_array($ability, $this->special_abilities ?? []);
    }

    /**
     * Get character abilities names.
     */
    public function getAbilitiesNames(): array
    {
        if (!$this->special_abilities) {
            return [];
        }

        return array_map(function ($ability) {
            return self::SPECIAL_ABILITIES[$ability] ?? $ability;
        }, $this->special_abilities);
    }

    /**
     * Check if character is a house leader.
     */
    public function isHouseLeader(): bool
    {
        return $this->house && $this->house->leader_id === $this->user_id;
    }

    /**
     * Get character age display.
     */
    public function getAgeDisplay(): string
    {
        if (!$this->age) {
            return 'Âge inconnu';
        }

        return $this->age . ' ans';
    }
}
