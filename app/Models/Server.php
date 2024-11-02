<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * VPN-service information.
 *
 * @property string $url Client connection URL
 * @property string $country Country where server is located (alpha2 code)
 * @property string $location More precise location of server (city, etc)
 * @property int $capacity The number of users server can serve
 * @property bool $active Do server accept connections?
 */
class Server extends Model
{
    protected $fillable = [
        'url',
        'country',
        'location',
        'capacity',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }
}
