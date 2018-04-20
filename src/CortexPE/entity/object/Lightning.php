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

namespace CortexPE\entity\object;

use CortexPE\entity\mob\Creeper;
use CortexPE\Main;
use pocketmine\block\Liquid;
use pocketmine\entity\{
	Animal, Living
};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Lightning extends Animal {
	const NETWORK_ID = self::LIGHTNING_BOLT;

	public $doneDamage = false;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

	public function getName(): string{
		return "Lightning";
	}

	public function onUpdate(int $currentTick): bool{
		if(!$this->doneDamage){
			$this->doneDamage = true;
			// Tnx Genisys
			if(Main::$lightningFire){
				$fire = Item::get(Item::FIRE)->getBlock();
				$oldBlock = $this->getLevel()->getBlock($this);
				if($oldBlock instanceof Liquid){

				}elseif($oldBlock->isSolid()){
					$v3 = new Vector3($this->x, $this->y + 1, $this->z);
				}else{
					$v3 = new Vector3($this->x, $this->y, $this->z);
				}

				$fire->setDamage(11); // Only one random tick away till a chance of despawn ;)

				if(isset($v3)) $this->getLevel()->setBlock($v3, $fire);

				foreach($this->level->getNearbyEntities($this->boundingBox->grow(6, 6, 6), $this) as $entity){
					if($entity instanceof Living){
						$distance = $this->distance($entity);

						$distance = ($distance > 0 ? $distance : 1);

						$k = 5;
						$damage = $k / $distance;

						$ev = new EntityDamageByEntityEvent($this, $entity, 16, $damage); // LIGHTNING
						$entity->attack($ev);
						$entity->setOnFire(mt_rand(3, 8));
					}

					if($entity instanceof Creeper){
						$entity->setPowered(true);
					}
				}
			}
			$spk = new PlaySoundPacket();
			$spk->soundName = "ambient.weather.lightning.impact";
			$spk->x = $this->getX();
			$spk->y = $this->getY();
			$spk->z = $this->getZ();
			$spk->volume = 500;
			$spk->pitch = 1;

			foreach($this->level->getPlayers() as $p){
				$p->dataPacket($spk);
			}
		}
		if($this->age > 6 * 20){
			$this->flagForDespawn();
		}

		return parent::onUpdate($currentTick);
	}
}
