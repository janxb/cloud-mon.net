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
        'specs',
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

    public function fireChecks()
    {
        $class = 'App\\Targets\\'.$this->target;
        $reflection = new \ReflectionClass($class);
        $methods = $reflection->getMethods();
        $target = new $class($this);
        foreach ($methods as $method) {
            var_dump($method->name);
            if (starts_with($method->name, 'check')) {
                call_user_func([$target, $method->name]);
            }
        }
    }
}
