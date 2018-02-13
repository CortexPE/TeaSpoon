<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use CortexPE\item\Potion;
use CortexPE\level\particle\SpellParticle;
use pocketmine\entity\{
	Entity, Living, projectile\Throwable
};
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;

class SplashPotion extends Throwable {

	public const TAG_POTION_ID = "PotionId";

	const NETWORK_ID = self::SPLASH_POTION;

	public function onUpdate(int $currentTick): bool{
		if($this->isCollided || $this->age > 1200){
			$color = Potion::getColor($this->getPotionId());
			$this->getLevel()->addParticle(new SpellParticle($this, $color->getR(), $color->getG(), $color->getB()));
			$radius = 6;
			foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow($radius, $radius, $radius)) as $p){
				foreach(Potion::getEffectsById($this->getPotionId()) as $effect){
					if($p instanceof Living){
						$p->addEffect($effect);
					}
				}
			}

			$pk = new PlaySoundPacket();
			$pk->soundName = "random.glass";
			$pk->volume = 500;
			$pk->pitch = 1;
			Server::getInstance()->broadcastPacket($this->getViewers(), $pk);

			$this->close();
		}

		return parent::onUpdate($currentTick);
	}

	public function getPotionId(): int{
		return $this->namedtag->getShort(self::TAG_POTION_ID);
	}

	public function onCollideWithEntity(Entity $entity){
		$this->isCollided = true;

		return;
	}
}