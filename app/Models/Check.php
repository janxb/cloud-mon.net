<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class Check extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'check',
        'result',
    ];

    protected $casts = [
        'result' => 'float',
    ];

    protected $hidden = [
        'provider_id',
        'updated_at',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('ordering', function (Builder $builder) {
            $builder->orderBy('id', 'DESC');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
