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

/**
 *
 */
class LinodeTarget extends AbstractTarget
{
    /**
     * @var \DigitalOceanV2\DigitalOceanV2
     */
    protected $linode;

    /**
     * HetznerTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        parent::__construct($provider);
        $this->linode = new LinodeApi($provider->getCredentials()->api_key);
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkServerCreationTime()
    {
        // return the key api
        $created_server = $this->linode->create(10, 1);

        $created_server_id = $created_server['LinodeID'];
        $start = microtime(true);
        $created_server = $this->linode->getList($created_server_id);
        while ($created_server[0]['STATUS'] != 0) {
            echo "Next try if it is 'brand new'".PHP_EOL;
            $created_server = $this->linode->getList($created_server_id);
            sleep(0.4);
        }
        $disk = new DiskApi($this->provider->getCredentials()->api_key);
        $created_disk = $disk->createFromDistribution($created_server_id, 146, 'cloud-mon', 1024, str_random());
        $created_disk_id = $created_disk['DiskID'];
        $config = new ConfigApi($this->provider->getCredentials()->api_key);
        $created_config = $config->create($created_server_id, 'cloud-mon', 138, $created_disk_id);
        $created_config_id = $created_config['ConfigID'];
        $this->linode->boot($created_server_id, $created_config_id);
        $ip_api = new IpApi($this->provider->getCredentials()->api_key);
        $ip = $ip_api->getList($created_server_id);
        $created_server = $this->linode->getList($created_server_id);
        while ($created_server[0]['STATUS'] != 1) {
            echo "Next Try if it comes online".PHP_EOL;
            $created_server = $this->linode->getList($created_server_id);
            sleep(1);
        }
        $ping = new Ping($ip[0]['IPADDRESS'], 255, 5);
        $trys = 100;
        while ($ping->ping() == false && $trys != 0) {
            echo $trys;
            $trys--;
        }
        $end = microtime(true);

        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
        $this->linode->delete($created_server_id, true);

        return $check;
    }

    /**
     * @return \App\Models\Check|\Illuminate\Database\Eloquent\Model
     * @throws \LKDev\HetznerCloud\APIException
     */
    public function checkApiResponseTime()
    {
        $start = microtime(true);
        $this->linode->getList();
        $end = microtime(true);
        $duration = $end - $start;

        $check = $this->provider->checks()->create(['check' => 'api_response_time', 'result' => $duration]);
    }
}