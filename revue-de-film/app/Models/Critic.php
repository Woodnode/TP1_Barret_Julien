<?php

//https://laravel.com/docs/12.x/eloquent-mutators#attribute-casting


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Critic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'film_id',
        'score',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:1',
        ];
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function film() : BelongsTo
    {
        return $this->belongsTo(Film::class);
    }
}
