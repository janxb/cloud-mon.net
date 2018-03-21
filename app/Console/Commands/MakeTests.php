<?php

namespace App\Console\Commands;

use App\Models\Provider;
use Illuminate\Console\Command;

class MakeTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:tests {provider?}';

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
        if ($this->argument('provider') == null) {
            $providers = Provider::all();
        } else {
            $providers = Provider::where('id', '=', $this->argument('provider'))->get();
        }

        $this->info(count($providers).' Provider');
        foreach ($providers as $provider) {
            $this->info($provider->name);
            $provider->fireChecks();
        }
    }
}
