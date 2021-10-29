<?php

namespace App\Filler\Models;

use App\Filler\Enums;

class Player
{
    public function __construct(
        protected Enums\Player $player,
        protected Enums\Color $color,
    ) {
        //
    }


    public function isColor(Enums\Color $color): bool
    {
        return $this->color->is($color);
    }

    public function isNotColor(Enums\Color $color): bool
    {
        return $this->color->isNot($color);
    }

    public function getColor(): Enums\Color
    {
        return $this->color;
    }

    public function setColor(Enums\Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    public static function import(array $import): self
    {
        return new Player(
            Enums\Player::fromValue($import['id']),
            Enums\Color::fromValue($import['color']),
        );
    }

    public function export(): array
    {
        return [
            'id' => $this->player->value,
            'color' => $this->color->value,
        ];
    }
}
