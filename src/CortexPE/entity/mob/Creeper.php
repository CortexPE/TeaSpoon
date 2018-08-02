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
use pocketmine\entity\Monster;
use pocketmine\item\Item;
use pocketmine\level\Explosion;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player;

class Creeper extends Monster {

	public const NETWORK_ID = self::CREEPER;
	public const TAG_POWERED = "powered";
	public const TAG_IGNITED = "ignited";
	public const TAG_FUSE = "Fuse";
	public const TAG_EXPLOSION_RADIUS = "ExplosionRadius";
	public $height = 1.7;
	public $width = 0.6;

	public function initEntity(): void{
		parent::initEntity();

		if(!$this->namedtag->hasTag(self::TAG_POWERED, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_POWERED, 0);
		}

		if($this->namedtag->hasTag(self::TAG_EXPLOSION_RADIUS, ShortTag::class)){ // oopsie whoopsie we made a fucky wucky [73f710b]
			$this->namedtag->removeTag(self::TAG_EXPLOSION_RADIUS);
		}
		if(!$this->namedtag->hasTag(self::TAG_EXPLOSION_RADIUS, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_EXPLOSION_RADIUS, 3);
		}

		if(!$this->namedtag->hasTag(self::TAG_FUSE, ShortTag::class)){
			$this->namedtag->setShort(self::TAG_FUSE, 30);
		}

		if(!$this->namedtag->hasTag(self::TAG_IGNITED, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_IGNITED, 0);
		}
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		$parent = parent::entityBaseTick($tickDiff);
		if($this->isIgnited()){
			$fuse = $this->getFuse() - $tickDiff;
			$this->setFuse($fuse);
			if($fuse <= 0){
				$this->explode();
			}
		}

		return $parent;
	}

	public function isIgnited(): bool{
		return ($this->getGenericFlag(self::DATA_FLAG_IGNITED) || boolval($this->namedtag->getByte(self::TAG_IGNITED, 0)));
	}

	public function getFuse(): int{
		return $this->namedtag->getShort(self::TAG_FUSE, 30);
	}

	public function setFuse(int $fuse): void{
		$this->namedtag->setShort(self::TAG_FUSE, $fuse);
	}

	public function explode(){
		$this->kill();
		if(Main::$creepersExplodes){
			$pow = $this->getExplosionRadius();
			if($this->isPowered()){
				$pow *= 2; // 6 ¯\_(ツ)_/¯
			}
			$explosion = new Explosion($this, $pow, $this);
			$explosion->explodeA();
			$explosion->explodeB();
		}
	}

	public function getExplosionRadius(): int{
		return $this->namedtag->getByte(self::TAG_EXPLOSION_RADIUS, 3);
	}

	public function isPowered(): bool{
		return ($this->getGenericFlag(self::DATA_FLAG_POWERED) || boolval($this->namedtag->getByte(self::TAG_POWERED, 0)));
	}

	public function getName(): string{
		return "Creeper";
	}

	public function getDrops(): array{
		if(mt_rand(1, 10) < 3){
			return [Item::get(Item::GUNPOWDER, 0, 1)];
		}

		return [];
	}

	public function setPowered(bool $powered): void{
		$this->namedtag->setByte(self::TAG_POWERED, intval($powered));
		$this->setGenericFlag(self::DATA_FLAG_POWERED, $powered);
	}

	public function setExplosionRadius(int $explosionRadius): void{
		$this->namedtag->setByte(self::TAG_EXPLOSION_RADIUS, $explosionRadius);
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if(Main::$ignitableCreepers && $item->getId() == Item::FLINT_AND_STEEL && !$this->isIgnited()){
			$this->setIgnited(true);
		}

		return true;
	}

	public function setIgnited(bool $ignited): void{
		$this->namedtag->setByte(self::TAG_IGNITED, intval($ignited));
		$this->setGenericFlag(self::DATA_FLAG_IGNITED, $ignited);
	}
}
