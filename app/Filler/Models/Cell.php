<?php

namespace App\Filler\Models;

use App\Filler\Enums;

class Cell
{
    protected ?Field $field = null;

    protected $x = 0;
    protected $y = 0;

    protected $id;
    protected static $instanceCounter = 0;

    public function __construct(
        protected Enums\Player $player,
        protected Enums\Color $color,
    ) {
        $this->id = static::$instanceCounter++;
    }

    public static function import(array $import): self
    {
        return new Cell(
            Enums\Player::fromValue($import['playerId']),
            Enums\Color::fromValue($import['color']),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setField(?Field $field = null): self
    {
        $this->field = $field;

        return $this;
    }

    public function setPosition(int $x = 0, int $y = 0): self
    {
        $this->x = $x;
        $this->y = $y;

        return $this;
    }

    public function getPosition(): array
    {
        return [
            $this->x,
            $this->y
        ];
    }

    public function getField(): ?Field
    {
        return $this->field;
    }

    public function getColor(): Enums\Color
    {
        return $this->color;
    }

    public function setPlayer(Enums\Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function setColor(Enums\Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function isPlayer(Enums\Player $player): bool
    {
        return $this->player->is($player);
    }

    public function isNotPlayer(Enums\Player $player): bool
    {
        return $this->player->isNot($player);
    }

    public function isColor(Enums\Color $color): bool
    {
        return $this->color->is($color);
    }

    public function isNotColor(Enums\Color $color): bool
    {
        return $this->color->isNot($color);
    }

    public function dump()
    {
        dump((clone $this)->setField(null));

        return $this;
    }
}
