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


use CortexPE\entity\object\ArmorStand as ArmorStandEntity;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ArmorStand extends Item {
	public function __construct(int $meta = 0){
		parent::__construct(self::ARMOR_STAND, $meta, "Armor Stand");
	}

	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
		$entity = Entity::createEntity(Entity::ARMOR_STAND, $player->getLevel(), Entity::createBaseNBT($blockReplace->add(0.5, 0, 0.5), null, $this->getDirection($player->getYaw())));

		if($entity instanceof ArmorStandEntity){
			if($player->isSurvival()){
				$this->pop();
			}
			$entity->spawnToAll();
		}

		return true;
	}

	public function getDirection($yaw): float{
		$rotation = $yaw % 360;
		if($rotation < 0){
			$rotation += 360;
		}
		if((0 <= $rotation && $rotation < 22.5) || (337.5 <= $rotation && $rotation < 360)){
			return 180;
		}elseif(22.5 <= $rotation && $rotation < 67.5){
			return 225;
		}elseif(67.5 <= $rotation && $rotation < 112.5){
			return 270;
		}elseif(112.5 <= $rotation && $rotation < 157.5){
			return 315;
		}elseif(157.5 <= $rotation && $rotation < 202.5){
			return 0;
		}elseif(202.5 <= $rotation && $rotation < 247.5){
			return 45;
		}elseif(247.5 <= $rotation && $rotation < 292.5){
			return 90;
		}elseif(292.5 <= $rotation && $rotation < 337.5){
			return 135;
		}else{
			return 0;
		}
	}
}