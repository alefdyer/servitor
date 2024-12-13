<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    private const DEFAULT_NAME = 'Anonymous';

    protected $fillable = [
        'name',
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getActiveSubscription(): ?Subscription
    {
        return $this->subscriptions()->active()->first();
    }

    public function scopePremium(Builder $query): void
    {
        $query->whereHas('subscriptions', fn(Builder $ss) => $ss->active());
    }

    public static function createByDevice(Device $device): static
    {
        $client = self::create(['name' => self::DEFAULT_NAME]);
        $client->devices()->save($device);
        return $client;
    }
}
