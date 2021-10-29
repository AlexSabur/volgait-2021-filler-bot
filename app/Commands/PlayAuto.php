<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use LaravelZero\Framework\Commands\Command;

class PlayAuto extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'play:auto {--gameId=}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $player = 1;
        do {
            $code = Artisan::call('play', [
                '--gameServer' => 'http://volga-it-2021.ml/api/',
                '--gameId' => $this->option('gameId'),
                '--playerId' => $player
            ]);

            sleep(1);

            $player = $player == 1 ? 2 : 1;
        } while ($code === 0);

        $this->info("winner: {$code}");
    }
}
