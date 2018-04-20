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
use CortexPE\task\DelayedCreeperExplosionTask;
use pocketmine\entity\Monster;
use pocketmine\item\Item;
use pocketmine\level\Explosion;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\Player;

class Creeper extends Monster {
	const NETWORK_ID = self::CREEPER;

	public $height = 1.7;
	public $width = 0.6;

	/** @var bool */
	protected $ignited = false; // used to ignore flint and steel interaction when about to explode

	public const TAG_POWERED = "powered";
	public const TAG_IGNITED = "ignited";
	public const TAG_EXPLOSION_RADIUS = "ExplosionRadius";

	public function initEntity(){
		parent::initEntity();

		if(!$this->namedtag->hasTag(self::TAG_POWERED, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_POWERED, 0);
		}

		if(!$this->namedtag->hasTag(self::TAG_EXPLOSION_RADIUS, ShortTag::class)){
			$this->namedtag->setShort(self::TAG_EXPLOSION_RADIUS, 3);
		}

		// TODO: Fuse NBT (probably used for MobAI)

		if(!$this->namedtag->hasTag(self::TAG_IGNITED, ByteTag::class)){
			$this->namedtag->setByte(self::TAG_IGNITED, 0);
		}
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

	public function isIgnited(): bool{
		return ($this->getGenericFlag(self::DATA_FLAG_IGNITED) || boolval($this->namedtag->getByte(self::TAG_IGNITED, 0)));
	}

	public function setIgnited(bool $ignited): void{
		if(!$this->ignited){
			$this->ignited = true;
			$this->namedtag->setByte(self::TAG_IGNITED, intval($ignited));
			$this->setGenericFlag(self::DATA_FLAG_IGNITED, $ignited);
			Main::getInstance()->getServer()->getScheduler()->scheduleDelayedTask(new DelayedCreeperExplosionTask(Main::getInstance(), $this), 30); // 1.5 seconds
		}
	}

	public function isPowered(): bool{
		return ($this->getGenericFlag(self::DATA_FLAG_POWERED) || boolval($this->namedtag->getByte(self::TAG_POWERED, 0)));
	}

	public function setPowered(bool $powered): void{
		$this->namedtag->setByte(self::TAG_POWERED, intval($powered));
		$this->setGenericFlag(self::DATA_FLAG_POWERED, $powered);
	}

	public function setExplosionRadius(int $explosionRadius): void{
		$this->namedtag->setShort(self::TAG_EXPLOSION_RADIUS, $explosionRadius);
	}

	public function getExplosionRadius(): int{
		return $this->namedtag->getShort(self::TAG_EXPLOSION_RADIUS, 3);
	}

	public function explode(){
		if(Main::$creepersExplodes){
			$pow = $this->getExplosionRadius();
			if($this->isPowered()){
				$pow *= 2; // 6 ¯\_(ツ)_/¯
			}
			$explosion = new Explosion($this, $pow, $this);
			$explosion->explodeA();
			$explosion->explodeB();
		}
		$this->broadcastEntityEvent(EntityEventPacket::DEATH_ANIMATION); // idek why this isn't called somewhere on explosion and on Entity::kill()
		$this->kill();
	}

	public function onInteract(Player $player, Item $item, int $slot, Vector3 $clickPos): bool{
		if(Main::$ignitableCreepers && $item->getId() == Item::FLINT_AND_STEEL && !$this->isIgnited()){
			$this->setIgnited(true);
		}

		return true;
	}
}
