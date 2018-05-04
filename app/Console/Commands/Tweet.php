<?php

namespace App\Console\Commands;

use App\Models\Provider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use File;
use Spatie\Browsershot\Browsershot;
use Spatie\Image\Manipulations;
use Thujohn\Twitter\Facades\Twitter;

class Tweet extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tweet';

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
        if (env('APP_LOCATION', false) != false) {
            $checks = ['server_creation_time' => "server creation time", 'speed_test_upload' => "upload speedtest", 'speed_test_download' => "download speedtest"];
            $media = [];
            foreach ($checks as $check => $text) {
                $path = storage_path(str_random() . '.png');
                Browsershot::url(env('APP_URL') . '/?hide=' . $check)->waitUntilNetworkIdle()->setDelay(1000 * 10)->fullPage()->save($path);

                $uploaded_media = Twitter::uploadMedia(['media' => File::get($path)]);

                $media[] = $uploaded_media->media_id_string;
                unlink($path);
            }
            $providers = Provider::all()->map(function ($p) {
                return '#' . str_replace('-', '', $p->name);
            });
            Twitter::postTweet(['status' => "Hi there! I've got a new daily result of my #monitoring from #" . env('APP_LOCATION') . " for you! " . $providers->implode(' ') . ' #cloud', 'media_ids' => $media]);

        }
    }
}
