<?php

namespace App\Filler\Models;

use App\Filler\Enums;
use App\Filler\Exceptions\ColorNotAvailableExceprion;
use App\Filler\Exceptions\PlayerCannotWalkExceprion;
use Illuminate\Support\Arr;

class Game
{
    protected Field $field;
    protected array $players = [];

    protected Enums\Player $currentPlayer;
    protected Enums\Player $winnerPlayer;

    public function __construct()
    {
        [$one, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $class = Arr::get($caller, 'class', null);

        throw_unless(
            is_a($this, $class),
        );
    }

    public static function import(array $import): Self
    {
        $game = new Game();

        $game->setField(Field::import($import['field']));
        $game->setCurrentPlayer(Enums\Player::fromValue($import['currentPlayerId']));
        $game->setWinnerPlayer(Enums\Player::fromValue($import['winnerPlayerId']));
        $game->setPlayers(
            array_map(
                fn ($player) => Player::import($player),
                $import['players'],
            )
        );

        return $game;
    }

    public function setField(Field $field = null): self
    {
        $this->field = $field;

        return $this;
    }

    public function setPlayers(array $players): self
    {
        $this->players = $players;

        return $this;
    }

    public function setCurrentPlayer(Enums\Player $player): self
    {
        $this->currentPlayer = $player;

        return $this;
    }

    public function getCurrentPlayer(): Enums\Player
    {
        return $this->currentPlayer;
    }

    public function setWinnerPlayer(Enums\Player $player): self
    {
        $this->winnerPlayer = $player;

        return $this;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function getAvaibleColors(Enums\Player $player): array
    {
        $colors = $this->field->getAvaibleColors($player);

        return array_filter(
            $colors,
            fn ($color) => $this->isNotBlockedColor($color),
        );
    }

    public function isNotBlockedColor(Enums\Color $color): bool
    {
        foreach ($this->players as $player) {
            if ($player->isColor($color)) {
                return false;
            }
        }

        return true;
    }

    public function isWinner(Enums\Player $player)
    {
        return $this->winnerPlayer->is($player);
    }
}
