<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 19.03.18
 * Time: 08:50
 */

namespace App\Targets;

use App\Models\Provider;
use DigitalOceanV2\Adapter\GuzzleHttpAdapter;
use DigitalOceanV2\DigitalOceanV2;
use JJG\Ping;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Images\Images;
use LKDev\HetznerCloud\Models\Locations\Locations;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerTypes;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;

/**
 *
 */
class DigitalOceanTarget extends AbstractTarget
{
    /**
     * @var \DigitalOceanV2\DigitalOceanV2
     */
    protected $digitalOcean;

    /**
     * HetznerTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        parent::__construct($provider);
        $this->digitalOcean = new DigitalOceanV2(new GuzzleHttpAdapter($provider->getCredentials()->api_key));
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkServerCreationTime()
    {
        // return the key api
        $key = collect($this->digitalOcean->key()->getAll())->map(function ($key) {
            return $key->id;
        })->toArray();

        $created_server = $this->digitalOcean->droplet()->create('mon-cloud-test-digitalocean.mon-cloud.net', 'fra1', 's-1vcpu-1gb', 'ubuntu-16-04-x64', false, false, false, $key);
        $start = microtime(true);

        while (empty($created_server->networks)) {
            $created_server = $this->digitalOcean->droplet()->getById($created_server->id);
        }

        $ping = new Ping($created_server->networks[0]->ipAddress, 255, 5);
        $trys = 100;
        while ($ping->ping() == false && $trys != 0) {
            echo $trys;
            $trys--;
        }
        $end = microtime(true);

        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
        $created_server = $this->digitalOcean->droplet()->delete($created_server->id);

        return $check;
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkApiResponseTime()
    {
        $start = microtime(true);
        $this->digitalOcean->droplet()->getAll();
        $end = microtime(true);
        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'api_response_time', 'result' => $duration]);
    }
}