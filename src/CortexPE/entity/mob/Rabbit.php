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

use CortexPE\item\enchantment\Enchantment;
use pocketmine\entity\Animal;
use pocketmine\event\entity\{
	EntityDamageByEntityEvent, EntityDamageEvent
};
use pocketmine\item\Item;
use pocketmine\nbt\tag\{
	IntTag
};
use pocketmine\Player;

class Rabbit extends Animal {

	public const NETWORK_ID = self::RABBIT;

	/** @var int */
	public const
		DATA_RABBIT_TYPE = 18,
		DATA_JUMP_TYPE = 19;

	/** @var int */
	public const
		TYPE_BROWN = 0,
		TYPE_WHITE = 1,
		TYPE_BLACK = 2,
		TYPE_BLACK_WHITE = 3,
		TYPE_GOLD = 4,
		TYPE_SALT_PEPPER = 5,
		TYPE_KILLER_BUNNY = 99;
	public const TAG_RABBIT_TYPE = "RabbitType";
	public $width = 0.4;
	public $height = 0.5;

	public function initEntity(): void{
		$type = $this->getRandomRabbitType();
		if(!$this->namedtag->hasTag(self::TAG_RABBIT_TYPE, IntTag::class)){
			$this->namedtag->setInt(self::TAG_RABBIT_TYPE, $type);
		}

		$this->setMaxHealth(3);
		$this->getDataPropertyManager()->setByte(self::DATA_RABBIT_TYPE, $type);
		parent::initEntity();
	}

	public function getRandomRabbitType(): int{
		$arr = [0, 1, 2, 3, 4, 5, 99];

		return $arr[array_rand($arr)];
	}

	public function getRabbitType(): int{
		return $this->namedtag->getInt(self::TAG_RABBIT_TYPE);
	}

	public function getName(): string{
		return "Rabbit";
	}

	public function setRabbitType(int $type){
		$this->namedtag->setInt(self::TAG_RABBIT_TYPE, $type);
	}

	public function getDrops(): array{
		$lootingL = 0;
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$looting = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING);
				if($looting !== null){
					$lootingL = $looting->getLevel();
				}else{
					$lootingL = 0;
				}
			}
		}
		$drops = [Item::get(Item::RABBIT_HIDE, 0, mt_rand(0, 1))];
		if($this->getLastDamageCause() === EntityDamageEvent::CAUSE_FIRE){
			$drops[] = Item::get(Item::COOKED_RABBIT, 0, mt_rand(0, 1));
		}else{
			$drops[] = Item::get(Item::RAW_RABBIT, 0, mt_rand(0, 1));
		}
		if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
			$drops[] = Item::get(Item::RABBIT_FOOT, 0, 1);
		}

		return $drops;
	}
}
