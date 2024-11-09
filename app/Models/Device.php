<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Uuid $id
 * @property string $model
 * @property Client $client
 */
class Device extends Model
{
    use HasFactory;
    use HasTimestamps;
    use HasUuids;

    protected $fillable = [
        'id',
        'model',
        'client',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
