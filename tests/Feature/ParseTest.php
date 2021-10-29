<?php

use App\Filler\Enums\Color;
use App\Filler\Enums\Player;
use App\Filler\Models\Cell;

it('cell import', function () {
    $cell = Cell::import([
        'playerId' => 1,
        'color' => '#ffffff',
    ]);

    $this->assertTrue($cell->isColor(Color::White()));
    $this->assertTrue($cell->isPlayer(Player::First()));
});
