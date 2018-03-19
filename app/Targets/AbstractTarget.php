<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 19.03.18
 * Time: 08:47
 */

namespace App\Targets;

use App\Models\Provider;

/**
 *
 */
abstract class AbstractTarget
{
    /**
     * @var \App\Models\Provider
     */
    protected $provider;

    /**
     * AbstractTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return \App\Models\Check
     */
    public abstract function checkServerCreationTime();

}