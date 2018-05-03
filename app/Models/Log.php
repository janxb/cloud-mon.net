<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class Log extends Model
{

    /**
     * @var array
     */
    protected $fillable = [
        'provider_id',
        'check_id',
        'server_id',
        'error',
    ];

    /**
     * @var array
     */
    protected $with = ['check'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    /**
     * @param \App\Models\Provider $provider
     * @param \App\Models\Check    $check
     * @param null                 $server_id
     * @param null                 $error_message
     *
     * @return mixed
     */
    public static function setup(Provider $provider, Check $check, $server_id = null, $error_message = null)
    {
        return Log::create([
            'provider_id' => $provider->id,
            'check_id' => $check->id,
            'server_id' => $server_id,
            'error' => $error_message,
        ]);
    }
}
