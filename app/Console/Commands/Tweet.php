<?php

namespace App\Console\Commands;

use App\Models\Provider;
use Faker\Provider\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
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
        $path = storage_path(str_random() . '.png');
        /*Browsershot::url(env('APP_URL') . '/?hide=speed_test_download')->waitUntilNetworkIdle()->setDelay(1000 * 10)->fullPage()->save($path);
        Mail::raw('Next is there. ', function ($mail) use ($path) {
            $mail->to('kontakt@lukas-kaemmerling.de');
            $mail->attach($path);
            $mail->subject('Image');
        });*/
        $providers = Provider::all()->map(function ($p) {
            return '#'.str_slug($p->name);
        });
        echo $providers->implode(' ');
        /*$uploaded_media = Twitter::uploadMedia(['media' => File::get($path)]);

         Twitter::postTweet(['status' => "Hi there! I've got a new daily result of my monitoring from ".env('APP_LOCATION')." for you! ", 'media_ids' => $uploaded_media->media_id_string]);

        unlink($path);*/
    }
}
