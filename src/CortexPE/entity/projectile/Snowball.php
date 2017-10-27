<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace CortexPE\entity\projectile;

use pocketmine\block\Block;
use pocketmine\entity\projectile\Throwable;
use pocketmine\level\particle\DestroyBlockParticle;

class Snowball extends Throwable {
	const NETWORK_ID = self::SNOWBALL;

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			$this->getLevel()->addParticle(new DestroyBlockParticle($this, Block::get(Block::SNOW))); // Realistic aye?
			$this->kill();
		}
		return parent::onUpdate($currentTick);
	}
}
