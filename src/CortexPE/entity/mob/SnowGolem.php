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

use CortexPE\Main;
use CortexPE\Utils;
use CortexPE\utils\BiomeUtils;
use pocketmine\block\Block;
use pocketmine\block\SnowLayer;
use pocketmine\entity\Monster;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\Shears;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\Player;

class SnowGolem extends Monster {

	public const NETWORK_ID = self::SNOW_GOLEM;
	public const TAG_PUMPKIN = "Pumpkin";
	public $width = 0.7;
	public $height = 1.9;

	public function getName(): string{
		return "Snow Golem";
	}

	public function initEntity(): void{
		if(!$this->namedtag->hasTag(self::TAG_PUMPKIN, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_PUMPKIN, 1);
		}

		$this->setMaxHealth(4);
		$this->setHealth(4);

		parent::initEntity();
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if(Main::$shearableSnowGolem && $item->getId() == Item::SHEARS && $this->isWearingPumpkin()){
			/** @var $item Shears */
			$this->setWearingPumpkin(false);
			if($player->isSurvival()){
				$item->applyDamage(1);
			}
		}

		return true;
	}

	public function isWearingPumpkin(): bool{
		return boolval($this->namedtag->getByte(self::TAG_PUMPKIN, 1));
	}

	public function setWearingPumpkin(bool $wearing): void{
		$this->namedtag->setByte(self::TAG_PUMPKIN, intval($wearing));
	}

	public function onUpdate(int $currentTick): bool{
		if($this->isFlaggedForDespawn() || !$this->isAlive()){
			return false;
		}
		$parent = parent::onUpdate($currentTick);
		if(Main::$snowGolemSnowTrails){
			if(Utils::canSeeSky($this->getLevel(), $this)){
				$lvl = $this->getLevel();
				for($x = -0.5; $x <= 0.5; $x += 0.5){
					for($z = -0.5; $z <= 0.5; $z += 0.5){
						$v3 = new Vector3(intval($this->getFloorX() + $x), intval($this->y), intval($this->getFloorZ() + $z));
						if($lvl->getBlock($v3)->getId() == Block::AIR){
							$lvl->setBlock($v3, new SnowLayer());
						}
					}
				}
			}
		}
		if(Main::$snowGolemMelts){
			$rainDamage = false;
			if(Main::$weatherEnabled){
				$weather = Main::$weatherData[$this->getLevel()->getId()];
				if($weather->isRainy() || $weather->isRainyThunder()){
					if(Utils::canSeeSky($this->getLevel(), $this)){
						$rainDamage = true;
					}
				}
			}
			if(BiomeUtils::getTemperature(intval($this->x), intval($this->y), intval($this->z), $this->getLevel()) > 1.0 || $rainDamage){
				$this->attack(new EntityDamageEvent($this, EntityDamageEvent::CAUSE_FIRE, 0.5));
			}
		}

		return $parent;
	}

	public function getDrops(): array{
		return [Item::get(Item::SNOWBALL, 0, mt_rand(0, 15))];
	}
}
