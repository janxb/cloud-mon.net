<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 *
 */
class Provider extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'target',
        'credentials',
        'color'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'target',
        'credentials',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    /**
     * @return mixed
     */
    public function getCredentials()
    {
        return json_decode(Crypt::decrypt($this->credentials));
    }

    /**
     * @return \App\Targets\AbstractTarget
     */
    public function getTarget()
    {
        $class = 'App\\Targets\\'.$this->target;

        return $target = new $class($this);
    }

    /**
     * @throws \ReflectionException
     */
    public function fireChecks()
    {
        $target = $this->getTarget();
        $reflection = new \ReflectionClass(get_class($target));
        $methods = $reflection->getMethods();

        foreach ($methods as $method) {
            if (starts_with($method->name, 'check')) {
                call_user_func([$target, $method->name]);
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(){
        return $this->hasMany(Log::class);
    }
}
