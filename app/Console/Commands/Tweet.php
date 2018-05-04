<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Browsershot\Browsershot;
use Spatie\Image\Manipulations;

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
        $path = storage_path(str_random().'.png');
        Browsershot::url(env('APP_URL').'/?hide=speed_test_download')->waitUntilNetworkIdle()->fullPage()->save($path);
        Mail::raw('Next is there. ', function ($mail) use ($path) {
            $mail->to('kontakt@lukas-kaemmerling.de');
            $mail->attach($path);
            $mail->subject('Image');
        });
        unlink($path);
    }
}
