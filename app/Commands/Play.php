<?php

namespace App\Commands;

use App\Api;
use App\Filler\Enums\Player;
use App\Filler\Models\Game;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputOption;

class Play extends Command
{
    const EXIT_GAME_ON = 0;
    const EXIT_PLAYER_1_WINNER = 1;
    const EXIT_PLAYER_2_WINNER = 2;

    const ERROR = 255;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $name = 'play';

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
        $api = app(Api::class);

        $gameId = '617b9b22e9df051bb57d2ee7';
        $gameId = '617bb538e9df051bb57d2eed';

        $data = $api->getState($gameId);

        $game = Game::import($data);

        switch (true) {
            case $game->isWinner(Player::First()):
                return static::EXIT_PLAYER_1_WINNER;
            case $game->isWinner(Player::Second()):
                return static::EXIT_PLAYER_2_WINNER;
            default:
                // dump($game->getCurrentPlayer());

                // dump($game->getField()->getAvaibleColors(Player::First()));
                // dump($game->getAvaibleColors(Player::Second()));
                // dump();
                $player = $game->getCurrentPlayer();

                $colors = $game->getAvaibleColors($player);

                // $color = collect($colors)->dump()->sortByDesc(fn ($color) => $color->getCount())->dump()->first();

                // dd($color);
                $color = collect($colors)->sortByDesc(fn ($color) => $color->getCount())->first();

                dump($colors);
                dump($color);
                dump($player);

                $api->makeStep($gameId, $player->value, $color);

                return static::EXIT_GAME_ON;
        }
    }
}
