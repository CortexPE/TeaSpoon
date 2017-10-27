<?php

namespace CortexPE\entity;

use pocketmine\entity\Monster;

class Silverfish extends Monster {
    const NETWORK_ID = self::SILVERFISH;

    public function getName(): string {
        return "Silverfish";
    }
}