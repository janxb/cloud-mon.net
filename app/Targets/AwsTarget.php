<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.18
 * Time: 14:59
 */

namespace App\Targets;

use App\Models\Provider;
use Aws\Ec2\Ec2Client;

class AwsTarget extends AbstractTarget
{
    protected $ec2Client;

    public function __construct(Provider $provider)
    {
        parent::__construct($provider);
        $ec2Client = new Ec2Client([
            'key' => '[aws access key]',
            'secret' => '[aws secret key]',
            'region' => '[aws region]' // (e.g., us-east-1)
        ]);
    }

    public function checkServerCreationTime()
    {
        // TODO: Implement checkServerCreationTime() method.
    }

    public function checkApiResponseTime()
    {

    }

    public function terminateAllServers()
    {
        // TODO: Implement terminateAllServers() method.
    }
}