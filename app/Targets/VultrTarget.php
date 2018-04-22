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
use Vultr\Exception\ApiException;
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
        $regions = $this->vultr->region()->getList();
        $data = [];
        $plans = [200, 201];
        foreach ($plans as $plan) {
            if (empty($data)) {
                foreach ($regions as $region) {
                    try {
                        // check if smartest server is available
                        if ($this->vultr->server()->isAvailable($region['DCID'], $plan)) {
                            $data = [
                                'DCID' => $region['DCID'],
                                'VPSPLANID' => $plan,
                                'OSID' => 215,
                            ];
                            echo "Plan $plan exist on ".$region['name'].PHP_EOL;
                            break;
                        }
                    } catch (ApiException $apiException) {

                    }
                    sleep(1);
                    echo "Plan $plan doesn't exist on ".$region['name'].PHP_EOL;
                }
            } else {
                break;
            }
        }
        if (! empty($data)) {
            // return the key api
            $created_server_id = $this->vultr->server()->create($data);

            $start = microtime(true);
            $created_server = $this->vultr->server()->getList($created_server_id);
            echo "Created Server: ".$created_server_id.PHP_EOL;
            $t = 1;
            while ($created_server['server_state'] != 'ok') {
                echo $t." | Server State isn't okay".PHP_EOL;
                $created_server = $this->vultr->server()->getList($created_server_id);
                sleep(2);
                $t++;
            }
            echo "Got IP: ".$created_server['main_ip'].PHP_EOL;
            $ping = new Ping($created_server['main_ip'], 255, 5);
            $trys = 100;
            while ($ping->ping() == false && $trys != 0) {
                echo $trys;
                $trys--;
            }
            $end = microtime(true);

            $duration = $end - $start;

            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
            $this->speedTest($created_server['main_ip']);

            return $check;
        }
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