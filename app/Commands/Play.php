<?php

namespace App\Commands;

use App\Api;
use App\Filler\Enums\Player;
use App\Filler\Models\Game;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;

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
    protected $signature = 'play {--gameServer=} {--gameId=} {--playerId=}';

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
        if (blank($gameServer = $this->option('gameServer'))) {
            return static::ERROR;
        }

        if (blank($gameId = $this->option('gameId'))) {
            return static::ERROR;
        }

        if (blank($playerId = $this->option('playerId'))) {
            return static::ERROR;
        }

        $api = app(Api::class, ['baseUrl' => Str::finish($gameServer, '/')]);

        $data = $api->getState($gameId);

        if (blank($data)) {
            return static::ERROR;
        }

        $game = Game::import($data);

        switch (true) {
            case $game->isWinner(Player::First()):
                return static::EXIT_PLAYER_1_WINNER;
            case $game->isWinner(Player::Second()):
                return static::EXIT_PLAYER_2_WINNER;
            default:
                $player = Player::fromValue((int) $playerId);

                $color = $game->getBestColor($player);

                $this->info("step: {$player->value} {$color->key} ");

                $api->makeStep($gameId, $player->value, $color);

                return static::EXIT_GAME_ON;
        }
    }
}
