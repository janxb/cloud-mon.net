<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 19.03.18
 * Time: 08:50
 */

namespace App\Targets;

use App\Models\Log;
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
class HetznerTarget extends AbstractTarget
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
        $server_id = null;
        try {
            $server = new Servers();
            $serverTypes = new ServerTypes();
            $serverType = $serverTypes->get(1);
            $images = new Images();
            $image = $images->get(1);
            $locations = new Locations();
            $location = $locations->get(1);
            $ssh_keys = new SSHKeys();
            $start = microtime(true);
            $created_server = $server->create('mon-cloud-test-hetzner-'.env('APP_NAME').rand().'.mon-cloud.net', $serverType, $image, $location, null, [
                18802,
                33790,
            ]);
            $server_id = $created_server->id;
            $ping = new Ping($created_server->publicNet->ipv4->ip, 255, 5);
            $trys = 100;
            while ($ping->ping() == false && $trys != 0) {
                echo $trys;
                $trys--;
            }
            $end = microtime(true);

            $duration = $end - $start;

            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
            Log::setup($this->provider, $check, $created_server->id, 'create_success');
            $this->speedTest($created_server->publicNet->ipv4->ip, $created_server->id);
            $created_server->delete();
        } catch (\Exception $e) {
            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => 0]);
            Log::setup($this->provider, $check, $server_id, $e->getMessage());
        }

        return $check;
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkApiResponseTime()
    {
        try {
            $start = microtime(true);
            $servers = new Servers();
            $servers->all();
            $end = microtime(true);
            $duration = $end - $start;

            $check = $this->provider->checks()->create(['check' => 'api_response_time', 'result' => $duration]);
            Log::setup($this->provider, $check, null, 'success');
        } catch (\Exception $e) {
            $check = $this->provider->checks()->create(['check' => 'api_response_time', 'result' => 0]);
            Log::setup($this->provider, $check, null, $e->getMessage());
        }
    }

    /**
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function terminateAllServers()
    {
        $servers = new Servers();
        $servers = $servers->all();
        foreach ($servers as $server) {
            /**
             * @var $server \LKDev\HetznerCloud\Models\Servers\Server
             */
            if ($server->name != 'cloud-mon.net' && $server->name != 'test.cloud-mon.net') {
                $server->delete();
            }
        }
    }
}