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
use CortexPE\Session;
use pocketmine\item\{
	Item, ProjectileItem
};
use pocketmine\math\Vector3;
use pocketmine\Player;

class EnderPearl extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::ENDER_PEARL, $meta, "Ender Pearl");
	}

	public function getProjectileEntityType(): string{
		return "EnderPearl";
	}

	public function getThrowForce(): float{
		return 1.1;
	}

	public function getMaxStackSize(): int{
		return 16;
	}

	public function onClickAir(Player $player, Vector3 $directionVector): bool{
		$session = Main::getInstance()->getSessionById($player->getId());
		if($session instanceof Session){
			if(floor(microtime(true) - $session->lastEnderPearlUse) < Main::$enderPearlCooldown){
				return false;
			}else{
				$session->lastEnderPearlUse = time();

				return parent::onClickAir($player, $directionVector);
			}
		}
	}

}