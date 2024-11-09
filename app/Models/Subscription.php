<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $period
 * @property \DateTime $start_at
 * @property \DateTime $end_at
 * @property Client $client
 */
class Subscription extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'client',
        'period',
    ];

    protected $cast = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeActive(Builder $query): void
    {
        $now = now();
        $query
            ->where('start_at', '<', $now)
            ->where('end_at', '>', $now);
    }
}
