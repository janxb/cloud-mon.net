<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.18
 * Time: 14:59
 */

namespace App\Targets;

use App\Models\Log;
use App\Models\Provider;
use Aws\Credentials\Credentials;
use Aws\Ec2\Ec2Client;
use Aws\Exception\CredentialsException;
use function GuzzleHttp\Promise\promise_for;
use GuzzleHttp\Promise\RejectedPromise;
use JJG\Ping;

/**
 *
 */
class AwsTarget extends AbstractTarget
{

    /**
     * @var \Aws\Ec2\Ec2Client
     */
    protected $ec2Client;

    /**
     * AwsTarget constructor.
     *
     * @param \App\Models\Provider $provider
     */
    public function __construct(Provider $provider)
    {
        parent::__construct($provider);

        $this->ec2Client = new Ec2Client([
            'region' => 'eu-central-1', // (e.g., us-east-1)
            'version' => 'latest',
            'credentials' => $this->getCredentials(),
        ]);
    }

    /**
     * @return \App\Models\Check|void
     */
    public function checkServerCreationTime()
    {
        $server_id = null;
        try {
            $start = microtime(true);
            $result = $this->ec2Client->runInstances([
                'ImageId' => 'ami-a034194b',
                'MinCount' => 1,
                'MaxCount' => 1,
                'InstanceType' => 't2.micro',
                'KeyName' => 'server@cloud-mon.net',
                'groupId' => ['sg-69042204'],
                'AdditionalInfo' => 'mon-cloud-test-aws-' . env('APP_NAME') . rand() . '.mon-cloud.net',
            ]);

            $server_id = $result->getPath('Instances/*/InstanceId');
            
            $dns = current($result->getPath('Reservations/*/Instances/*/PublicDnsName'));
            $ip = gethostbyname($dns);
            $ping = new Ping($ip, 255, 5);
            $trys = 100;
            while ($ping->ping() == false && $trys != 0) {
                echo $trys;
                $trys--;
            }
            $end = microtime(true);
            $duration = $end - $start;

            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => $duration]);
            Log::setup($this->provider, $check, $server_id, 'create_success');
            $this->speedTest($ip, $server_id);
            $this->terminateAllServers();
        } catch (\Exception $e) {
            $check = $this->provider->checks()->create(['check' => 'server_creation_time', 'result' => 0]);
            Log::setup($this->provider, $check, $server_id, $e->getMessage());
        }
    }

    /**
     * @return \App\Models\Check|void
     */
    public function checkApiResponseTime()
    {
        try {
            $start = microtime(true);
            $this->ec2Client->describeInstances();
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
     *
     */
    public function terminateAllServers()
    {
        foreach ($this->ec2Client->describeInstances() as $result) {
            array_walk_recursive($result, function ($value, $key) use (&$instances) {
                if ($key === 'InstanceId') {
                    $this->ec2Client->terminateInstances(['InstanceIds' => [$value]]);
                }
            });
        }


    }

    // This function CREATES a credential provider

    /**
     * @return \Closure
     */
    private function getCredentials()
    {
        // This function IS the credential provider
        return function () {
            $credentials = (json_decode($this->provider->getCredentials()->api_key));
            $key = $credentials->key;
            $secret = $credentials->secret;
            if ($key && $secret) {

                return promise_for(
                    new Credentials($credentials->key, $credentials->secret)
                );
            }

            $msg = 'Could not find environment variable ';

            return new RejectedPromise(new CredentialsException($msg));
        };
    }
}