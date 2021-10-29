<?php

namespace App\Filler\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static None()
 * @method static static First()
 * @method static static Second()
 */
class Player extends Enum
{
    const None   = 0;
    const First  = 1;
    const Second = 2;
}
