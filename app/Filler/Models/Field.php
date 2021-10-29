<?php

namespace App\Filler\Models;

use App\Filler\Enums;
use App\Filler\Enums\Color;
use Illuminate\Support\Arr;

class Field
{
    protected $height;
    protected $width;

    /**
     * @var Cell[]
     */
    protected array $cells = [];
    protected array $cellsColored = [];

    public function __construct(int $height, int $width)
    {
        $this->height = $height;
        $this->width = $width;

        $this->cellsColored = array_map(
            fn () => [],
            Color::asArray()
        );
    }

    public static function import(array $import): Field
    {
        $field = new Field($import['height'], $import['width']);

        $field->setCells(
            array_map(
                fn ($cell) => Cell::import($cell),
                $import['cells']
            )
        );

        return $field;
    }

    public function setCells(array $cells = []): self
    {
        $index = 0;

        for ($posY = 0; $posY < $this->height; $posY++) {
            $maxPosX = $posY & 1 ? $this->width - 1 : $this->width;

            for ($posX = 0; $posX < $maxPosX; $posX++) {
                $cells[$index++]
                    ->setField($this)
                    ->setPosition($posX, $posY);
            }
        }

        $this->cells = $cells;

        return $this;
    }

    public function getCellByPos(int $x, int $y): ?Cell
    {
        foreach ($this->cells as $cell) {
            if ($cell->getPosition() === [$x, $y]) {
                return $cell;
            }
        }

        return null;
    }

    public function getCellsByPlayer(Enums\Player $player): array
    {
        $cells = [];

        foreach ($this->cells as $cell) {
            if ($cell->isPlayer($player)) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }

    public function isAvaibleColors(Enums\Player $player, Enums\Color $color)
    {
        foreach ($this->getAvaibleColors($player) as $pColor) {
            if ($pColor->is($color)) {
                return true;
            }
        }

        return false;
    }

    public function getAvaibleColors(Enums\Player $player): array
    {
        /** @var Cell[] */
        $playerCells = $this->getCellsByPlayer($player);
        $colors = [];

        foreach ($playerCells as $cell) {
            foreach ($this->getNearCoors($cell->getPosition())  as $newCoords) {
                [$x, $y] = $newCoords;

                $checkCell = $this->getCellByPos($x, $y);

                if (null === $checkCell) {
                    continue;
                }

                if ($checkCell->isNotPlayer(Enums\Player::None())) {
                    continue;
                }

                $color = $checkCell->getColor();

                $this->addColloredCell($checkCell, $this->getZoneByCell($checkCell));

                foreach ($colors as $finnedColor) {
                    if ($color->is($finnedColor)) {
                        continue 2;
                    }
                }

                $colors[] = $color;
            }
        }

        return $colors;
    }

    public function addColloredCell(Cell $checkCell, $cells)
    {
        $this->cellsColored[$checkCell->getColor()->key] = array_uunique(
            array_merge($this->cellsColored[$checkCell->getColor()->key], $cells),
            function ($a, $b) {
                // dump($a->getId() <=> $b->getId());
                return $a->getId() <=> $b->getId();
            }
        );
    }

    public function getBestColored($colors)
    {
        $items = collect($this->cellsColored)
            ->filter(function ($items, $key) use ($colors) {
                foreach ($colors as $color) {
                    if ($color->key === $key) {
                        return true;
                    }
                }

                return false;
            })
            ->sortByDesc(fn ($items) => count($items));

        [$cell] = $items->first();

        return $cell->getColor();
    }


    public function getZoneByCell(Cell $cell)
    {
        $zone = [$cell];

        for ($i = 0; $i < count($zone); $i++) {
            $cellZavr = $zone[$i];

            foreach ($this->getNearCoors($cellZavr->getPosition())  as $newCoords) {
                [$x, $y] = $newCoords;

                $checkCell = $this->getCellByPos($x, $y);

                if (null === $checkCell) {
                    continue;
                }

                if ($cellZavr->isNotColor($checkCell->getColor())) {
                    continue;
                }

                foreach ($zone as $zoneCell) {
                    if ($zoneCell->getId() === $checkCell->getId()) {
                        continue 2;
                    }
                }

                $zone[] = $checkCell;
            }
        }

        return $zone;
    }

    public function getNearCoors($coords)
    {
        [$x, $y] = $coords;

        if ($y === 0) {
            return [
                [$x - 1, $y + 1],
                [$x + 0, $y + 1],
            ];
        }

        if ($y % 2 === 0) {
            if ($x % 2 === 0) {
                return [
                    [$x - 1, $y + 1],
                    [$x + 0, $y + 1],
                    [$x - 1, $y - 1],
                    [$x + 0, $y - 1],
                ];
            }

            return [
                [$x,     $y - 1],
                [$x + 1, $y - 1], // 13 + 1
                [$x,     $y + 1],
                [$x + 1, $y + 1],
            ];
        }

        return [
            [$x,     $y - 1],
            [$x + 1, $y - 1],
            [$x,     $y + 1],
            [$x + 1, $y + 1],
        ];
    }
}
