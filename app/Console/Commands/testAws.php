<?php

namespace App\Console\Commands;

use App\Models\Provider;
use App\Targets\AwsTarget;
use Illuminate\Console\Command;

class testAws extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:aws';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $provider = Provider::where('name','=','AWS')->first();
        if($provider != null){
            $awsTarget = new AwsTarget($provider);
            $awsTarget->checkServerCreationTime();

        } else {
            $this->info('Provider not available!');
        }
    }
}
