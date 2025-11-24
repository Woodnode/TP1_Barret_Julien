<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Actor extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'last_name',
        'first_name',
        'birthdate',  
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    // https://laravel.com/docs/master/eloquent-relationships#many-to-many
    public function films() : BelongsToMany
    {
        return $this->belongsToMany(Film::class, 'actor_film')->withTimestamps();
    }
}
