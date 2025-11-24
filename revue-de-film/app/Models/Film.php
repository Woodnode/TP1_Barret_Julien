<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Film extends Model
{
    use HasFactory;

    // https://stackoverflow.com/questions/29886497/how-to-only-use-created-at-in-laravel
    const UPDATED_AT = null;

    protected $fillable = [
        'title',
        'release_year',
        'length',
        'description',
        'rating',
        'language_id',
        'special_features',
        'image',
    ];

    public function language() : BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function critics() : HasMany
    {
        return $this->hasMany(Critic::class);
    }

    // https://laravel.com/docs/master/eloquent-relationships#many-to-many
    public function actors() : BelongsToMany
    {
        return $this->belongsToMany(Actor::class, 'actor_film')->withTimestamps();
    }
}
