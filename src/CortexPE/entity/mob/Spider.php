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

namespace CortexPE\entity\mob;

use pocketmine\entity\{
	Human, Monster
};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\Player;

class Spider extends Monster {

	public const NETWORK_ID = self::SPIDER;

	public $width = 1.4;
	public $height = 0.9;

	public function getName(): string{
		return "Spider";
	}

	public function getDrops(): array{
		$drops = [Item::get(Item::STRING, 0, 1)];
		if($this->lastDamageCause instanceof EntityDamageByEntityEvent and ($this->lastDamageCause->getEntity() instanceof Player || $this->lastDamageCause->getEntity() instanceof Human)){
			if(mt_rand(0, 199) < 5){
				switch(mt_rand(0, 2)){
					case 0:
						$drops[] = Item::get(Item::IRON_INGOT, 0, 1);
						break;
					case 1:
						$drops[] = Item::get(Item::CARROT, 0, 1);
						break;
					case 2:
						$drops[] = Item::get(Item::POTATO, 0, 1);
						break;
				}
			}
		}

		return $drops;
	}
}
