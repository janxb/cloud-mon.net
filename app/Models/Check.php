<?php

namespace App\Models;

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider(){
        return $this->belongsTo(Provider::class);
    }
}
