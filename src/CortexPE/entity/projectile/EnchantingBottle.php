<?php

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use pocketmine\entity\projectile\Throwable;

class EnchantingBottle extends Throwable {
	const NETWORK_ID = self::XP_BOTTLE;

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			// TODO: spawn XPOrbs on Collission.... #BlamePMMP
			$this->kill();
		}

		return parent::onUpdate($currentTick);
	}
}