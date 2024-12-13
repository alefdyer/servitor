<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $email
 * @property Client $client
 */
class Email extends Model
{
    protected $primaryKey = 'email';

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
