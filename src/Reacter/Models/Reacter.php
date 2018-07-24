<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reacter\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reacter extends Model
{
    protected $table = 'love_reacters';

    public function reacterable(): MorphTo
    {
        return $this->morphTo('reacterable', 'type', 'id', 'love_reacter_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reacter_id');
    }

    // TODO: Add ReactionType
    public function reactTo(Reactant $reactant): void
    {
        $attributes = [
            'reactant_id' => $reactant->getKey(),
        ];

        $reaction = $this->reactions()->where($attributes)->exists();
        if ($reaction) {
            // TODO: Throw custom typed exception
            throw new \RuntimeException('Reaction of type `ReactionType` already exists.');
        }

        $this->reactions()->create($attributes);
    }

    // TODO: Add ReactionType
    public function unreactTo(Reactant $reactant): void
    {
        $reaction = $this->reactions()
            ->where('reactant_id', $reactant->getKey())
            ->first();

        if (!$reaction) {
            // TODO: Throw custom typed exception
            throw new \RuntimeException('Reaction of type `ReactionType` not exists.');
        }

        if ($reaction) {
            $reaction->delete();
        }
    }

    // TODO: Add ReactionType
    public function isReactedTo(Reactant $reactant): bool
    {
        return $this->reactions()->where([
            'reactant_id' => $reactant->getKey(),
        ])->exists();
    }

    // TODO: Add ReactionType
    public function isNotReactedTo(Reactant $reactant): bool
    {
        return !$this->isReactedTo($reactant);
    }
}