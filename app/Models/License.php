<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\AsLicenseInstanceCast;
use App\Casts\AsLicenseMetaCast;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $key
 * @property string|null $hash
 * @property string $status
 * @property \App\Data\LicenseInstance|null $instance
 * @property \App\Data\LicenseMeta|null $meta
 * @property \Carbon\CarbonImmutable|null $expires_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $activated_at
 * @property-read string $short_key
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereInstance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class License extends Model
{
    protected $fillable = [
        'key',
        'status',
        'instance',
        'meta',
        'expires_at',
        'hash',
    ];

    /**
     * @return Attribute<non-falsy-string, never>
     */
    public function shortKey(): Attribute
    {
        return Attribute::get(fn (): string => '****-'.Str::afterLast($this->key, '-'))->shouldCache();
    }

    /**
     * @return Attribute<\Carbon\CarbonImmutable|null, never>
     */
    public function activatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->instance?->createdAt)->shouldCache();
    }

    protected function casts(): array
    {
        return [
            'key' => 'encrypted',
            'instance' => AsLicenseInstanceCast::class,
            'meta' => AsLicenseMetaCast::class,
            'expires_at' => 'datetime',
        ];
    }
}
