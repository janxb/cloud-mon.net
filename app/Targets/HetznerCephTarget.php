<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 19.03.18
 * Time: 08:50
 */

namespace App\Targets;

use App\Models\Provider;
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
class HetznerCephTarget extends HetznerTarget
{

    /**
     * @var \LKDev\HetznerCloud\HetznerAPIClient
     */
    protected $hetzner;

    /**
     * HetznerTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        parent::__construct($provider);
        $this->hetzner = new HetznerAPIClient($provider->getCredentials()->api_key);
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkServerCreationTime()
    {
        try {
            $server = new Servers();
            $serverTypes = new ServerTypes();
            $serverType = $serverTypes->get(2);
            $images = new Images();
            $image = $images->get(1);
            $locations = new Locations();
            $location = $locations->get(1);
            $ssh_keys = new SSHKeys();

            $created_server = $server->create('mon-cloud-test-hetzner-ceph-' . env('APP_NAME').rand() . '.mon-cloud.net', $serverType, $image, $location, null, [18802,33790]);
            $start = microtime(true);
            $ping = new Ping($created_server->publicNet->ipv4->ip, 255, 5);
            $trys = 100;
            while ($ping->ping() == false && $trys != 0) {
                echo $trys;
                $trys--;
            }
            $end = microtime(true);

            $duration = $end - $start;

            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
            $this->speedTest($created_server->publicNet->ipv4->ip);
        } catch (\Exception $e) {
            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => 0]);
        }

        return $check;
    }
}