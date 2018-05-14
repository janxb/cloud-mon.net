<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 19.03.18
 * Time: 08:47
 */

namespace App\Targets;

use App\Models\Log;
use App\Models\Provider;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

/**
 *
 */
abstract class AbstractTarget
{
    /**
     * @var \App\Models\Provider
     */
    protected $provider;

    const SPEED_TEST_MAX_TRYS = 20;

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

    /**
     * @return \App\Models\Check
     */
    public abstract function checkApiResponseTime();

    /**
     * @param $ip
     */
    public function speedTest($ip, $serverId = null)
    {
        echo "Perform Speedtest".PHP_EOL;
        $key = new RSA();
        $key->loadKey(file_get_contents(storage_path('app/cloud_mon.key')));
        if (! defined('NET_SSH2_LOGGING')) {
            define('NET_SSH2_LOGGING', 2);
        }
// Domain can be an IP too
        echo "Wait some seconds to help the server get up and running".PHP_EOL;
        sleep(15); // Wait 15 seconds, since the server could need some time to be sshable.
        $main_trys = 0;
        while ($main_trys < self::SPEED_TEST_MAX_TRYS) {
            sleep(5); // Wait 5 Seconds
            try {
                $ssh = new SSH2($ip);
            } catch (\Exception $e) {
                echo "Error on something other".PHP_EOL;
                echo $e->getMessage();
            }
            try {
                if ( $ssh->login('root', $key) == false && $ssh->login('ubuntu', $key) == false) {
                    echo "Error on Login".PHP_EOL;
                    throw new \Exception("Can't login");
                } else {
                    echo "Download of speedtest-cli".PHP_EOL;
                    $ssh->exec('wget -O speedtest-cli https://raw.githubusercontent.com/sivel/speedtest-cli/master/speedtest.py && chmod +x speedtest-cli');
                    echo "Done".PHP_EOL;
                    $lines = [];
                    $data = [];
                    $trys = self::SPEED_TEST_MAX_TRYS * 2;
                    $parsed = false;
                    while ($parsed == false) {
                        echo "Try: ".$trys.' from max: 20'.PHP_EOL;
                        echo "Wait 5 seconds".PHP_EOL;
                        sleep(5); // Wait 5 Seconds
                        echo "Install pyhton2".PHP_EOL;
                        $ssh->exec('apt-get update && apt-get install -yq python');
                        echo "Wait 5 seconds".PHP_EOL;
                        sleep(5); // Wait 5 Seconds
                        echo "Run Speedtest".PHP_EOL;
                        $response = (string) $ssh->exec('./speedtest-cli --json');
                        echo "Response:".$response.PHP_EOL;
                        $response = json_decode($response);
                        if (is_object($response)) {
                            echo "Parseable!".PHP_EOL;
                            $parsed = true;
                            $data['upload'] = $response->upload;
                            $data['download'] = $response->download;
                        } else {
                            echo "Error! Try Again".PHP_EOL;
                        }
                        $trys--;
                        if ($trys == 0) {
                            throw new \Exception("Timeout Limit");
                        }
                    }
                    $check = $this->provider->checks()->create([
                        'check' => 'speed_test_upload',
                        'result' => $data['upload'],
                    ]);

                    Log::setup($this->provider, $check, $serverId, 'speed_test_success');
                    $check = $this->provider->checks()->create([
                        'check' => 'speed_test_download',
                        'result' => $data['download'],
                    ]);
                    Log::setup($this->provider, $check, $serverId, 'speed_test_success');
                    $main_trys = self::SPEED_TEST_MAX_TRYS * 100;
                }
            } catch (\Exception $e) {
                // echo $ssh->getLog();
                echo $e->getMessage().' '.$e->getFile().' Line: '.$e->getLine();
                $main_trys++;
            }
        }
        if ($main_trys < self::SPEED_TEST_MAX_TRYS) {
            $check = $this->provider->checks()->create(['check' => 'speed_test_upload', 'result' => 0]);
            Log::setup($this->provider, $check, $serverId, 'Running out of tries. Max Trys: '.self::SPEED_TEST_MAX_TRYS);
            $check = $this->provider->checks()->create(['check' => 'speed_test_download', 'result' => 0]);
            Log::setup($this->provider, $check, $serverId, 'Running out of tries. Max Trys: '.self::SPEED_TEST_MAX_TRYS);
        }
    }

    /**
     * @return void
     */
    public abstract function terminateAllServers();
}