<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $email
 * @property Client $client
 */
class Device extends Model
{
    use HasTimestamps;

    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'client',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
