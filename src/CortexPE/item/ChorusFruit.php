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

namespace CortexPE\item;

use CortexPE\Main;
use pocketmine\block\Lava;
use pocketmine\block\Solid;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\item\Food;
use pocketmine\item\Item;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\Player;

class ChorusFruit extends Food {

	const RAND_POS_X = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];
	const RAND_POS_Y = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];
	const RAND_POS_Z = [-8, -7.5, -7, -6.5, -6, -5.5, -5, -4.5, -4, -3.5, -3, -2, -1, 0, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 7.5, 8];

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::CHORUS_FRUIT, $meta, "Chorus Fruit");
	}

	public function getMaxStackSize(): int{
		return 64;
	}

	public function canBeConsumedBy(Entity $entity): bool{
		return $entity instanceof Human;
	}

	public function getFoodRestore(): int{
		return 4;
	}

	public function getSaturationRestore(): float{
		return 0; // todo: check
	}

	public function onConsume(Entity $human){
		parent::onConsume($human);

		if($human instanceof Player){
			$session = Main::getInstance()->getSessionById($human->getId());
			if(floor(microtime(true) - $session->lastChorusFruitEat) < Main::$chorusFruitCooldown){
				return;
			}else{
				$session->lastChorusFruitEat = time();
			}

			$tries = 0;
			$pos = $human->getPosition();
			while($tries < 100){
				$tries++;

				$randpos = $pos->add(
					self::RAND_POS_X[array_rand(self::RAND_POS_X)],
					self::RAND_POS_Y[array_rand(self::RAND_POS_Y)],
					self::RAND_POS_Z[array_rand(self::RAND_POS_Z)]
				);
				$b = $human->getLevel()->getBlock($randpos);
				$below = $human->getLevel()->getBlock($randpos->subtract(0, 1, 0));
				if(!($b instanceof Solid) && !($b instanceof Lava) && !($below instanceof Lava) && $below instanceof Solid){
					$human->teleport($randpos, $human->getYaw(), $human->getPitch());

					$human->getLevel()->addSound(new EndermanTeleportSound($randpos), $human->getLevel()->getPlayers());
					break;
				}
			}
		}
	}
}