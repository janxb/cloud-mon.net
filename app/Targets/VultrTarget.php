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
use Linode\Linode\ConfigApi;
use Linode\Linode\DiskApi;
use Linode\Linode\IpApi;
use Linode\LinodeApi;
use LKDev\HetznerCloud\HetznerAPIClient;
use LKDev\HetznerCloud\Models\Images\Images;
use LKDev\HetznerCloud\Models\Locations\Locations;
use LKDev\HetznerCloud\Models\Servers\Servers;
use LKDev\HetznerCloud\Models\Servers\Types\ServerTypes;
use LKDev\HetznerCloud\Models\SSHKeys\SSHKeys;
use Vultr\VultrClient;

/**
 *
 */
class VultrTarget extends AbstractTarget
{
    /**
     * @var \Vultr\VultrClient
     */
    protected $vultr;

    /**
     * HetznerTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        parent::__construct($provider);
        $this->vultr = new VultrClient(new \Vultr\Adapter\GuzzleHttpAdapter($provider->getCredentials()->api_key));
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkServerCreationTime()
    {
        // return the key api
        $created_server_id = $this->vultr->server()->create([
            'DCID' => 12,
            'VPSPLANID' => 201,
            'OSID' => 215,
        ]);

        $start = microtime(true);
        $created_server = $this->vultr->server()->getList($created_server_id);
        while ($created_server['server_state'] != 'ok') {
            echo "Next try if it is 'brand new'".PHP_EOL;
            $created_server = $this->vultr->server()->getList($created_server_id);
            sleep(2);
        }
        $ping = new Ping($created_server['main_ip'], 255, 5);
        $trys = 100;
        while ($ping->ping() == false && $trys != 0) {
            echo $trys;
            $trys--;
        }
        $end = microtime(true);

        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
        $this->vultr->server()->destroy($created_server_id);

        return $check;
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkApiResponseTime()
    {
        $start = microtime(true);
        $this->vultr->server()->getList();
        $end = microtime(true);
        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'api_response_time', 'result' => $duration]);
    }

    public function terminateAllServers()
    {
        $servers = $this->vultr->server()->getList();
        foreach ($servers as $server) {
            var_dump($this->vultr->server()->destroy($server['SUBID']));
        }
    }
}