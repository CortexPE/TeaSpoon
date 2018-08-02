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

use CortexPE\block\Portal;
use pocketmine\block\{
	Block, BlockFactory, Solid
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class FireCharge extends Item {
	public function __construct($meta = 0){
		parent::__construct(self::FIRE_CHARGE, $meta, "Fire Charge");
	}

	public function canBeActivated(): bool{
		return true;
	}

	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $facePos): bool{
		$target = $blockClicked;
		$level = $player->getLevel();
		if($target->getId() === Block::OBSIDIAN){
			$tx = $target->getX();
			$ty = $target->getY();
			$tz = $target->getZ();
			$temporalVector = new Vector3(0, 0, 0);
			$x_max = $tx;
			$x_min = $tx;
			for($x = $tx + 1; $level->getBlock($temporalVector->setComponents($x, $ty, $tz))->getId() == Block::OBSIDIAN; $x++){
				$x_max++;
			}
			for($x = $tx - 1; $level->getBlock($temporalVector->setComponents($x, $ty, $tz))->getId() == Block::OBSIDIAN; $x--){
				$x_min--;
			}
			$count_x = $x_max - $x_min + 1;
			if($count_x >= 4 and $count_x <= 23){
				$x_max_y = $ty;
				$x_min_y = $ty;
				for($y = $ty; $level->getBlock($temporalVector->setComponents($x_max, $y, $tz))->getId() == Block::OBSIDIAN; $y++){
					$x_max_y++;
				}
				for($y = $ty; $level->getBlock($temporalVector->setComponents($x_min, $y, $tz))->getId() == Block::OBSIDIAN; $y++){
					$x_min_y++;
				}
				$y_max = min($x_max_y, $x_min_y) - 1;
				$count_y = $y_max - $ty + 2;
				if($count_y >= 5 and $count_y <= 23){
					$count_up = 0;
					for($ux = $x_min; ($level->getBlock($temporalVector->setComponents($ux, $y_max, $tz))->getId() == Block::OBSIDIAN and $ux <= $x_max); $ux++){
						$count_up++;
					}
					if($count_up == $count_x){
						for($px = $x_min + 1; $px < $x_max; $px++){
							for($py = $ty + 1; $py < $y_max; $py++){
								$level->setBlock($temporalVector->setComponents($px, $py, $tz), new Portal());
							}
						}
						if($player->isSurvival()){
							$player->getInventory()->setItemInHand($this);
						}

						return true;
					}
				}
			}

			$z_max = $tz;
			$z_min = $tz;
			for($z = $tz + 1; $level->getBlock($temporalVector->setComponents($tx, $ty, $z))->getId() == Block::OBSIDIAN; $z++){
				$z_max++;
			}
			for($z = $tz - 1; $level->getBlock($temporalVector->setComponents($tx, $ty, $z))->getId() == Block::OBSIDIAN; $z--){
				$z_min--;
			}
			$count_z = $z_max - $z_min + 1;
			if($count_z >= 4 and $count_z <= 23){
				$z_max_y = $ty;
				$z_min_y = $ty;
				for($y = $ty; $level->getBlock($temporalVector->setComponents($tx, $y, $z_max))->getId() == Block::OBSIDIAN; $y++){
					$z_max_y++;
				}
				for($y = $ty; $level->getBlock($temporalVector->setComponents($tx, $y, $z_min))->getId() == Block::OBSIDIAN; $y++){
					$z_min_y++;
				}
				$y_max = min($z_max_y, $z_min_y) - 1;
				$count_y = $y_max - $ty + 2;
				if($count_y >= 5 and $count_y <= 23){
					$count_up = 0;
					for($uz = $z_min; ($level->getBlock($temporalVector->setComponents($tx, $y_max, $uz))->getId() == Block::OBSIDIAN and $uz <= $z_max); $uz++){
						$count_up++;
					}
					if($count_up == $count_z){
						for($pz = $z_min + 1; $pz < $z_max; $pz++){
							for($py = $ty + 1; $py < $y_max; $py++){
								$level->setBlock($temporalVector->setComponents($tx, $py, $pz), new Portal());
							}
						}
						if($player->isSurvival()){
							$player->getInventory()->setItemInHand($this);
						}

						return true;
					}
				}
			}
		}

		if(($blockClicked instanceof Solid)){
			$level->setBlock($blockReplace, BlockFactory::get(Block::FIRE), true, true);
			$level->broadcastLevelSoundEvent($blockReplace->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_IGNITE);

			if($player->isSurvival()){
				--$this->count;
			}

			return true;
		}

		return false;
	}
}