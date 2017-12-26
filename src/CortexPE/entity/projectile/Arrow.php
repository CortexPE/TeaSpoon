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

namespace CortexPE\entity\projectile;

use CortexPE\item\Potion;
use CortexPE\level\particle\MobSpellParticle;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow as PMArrow;
use pocketmine\event\entity\EntityCombustByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class Arrow extends PMArrow {
	/** @var int */
	protected $potionId;
	/** @var array */
	protected $color = []; // arrays are faster than getting values from a function every update?

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, bool $critical = false){
		parent::__construct($level, $nbt, $shootingEntity, $critical);
		$this->potionId = $this->namedtag->getShort("Potion", 0);
		$col = Potion::getColor($this->potionId);
		if($this->potionId != 0){
			$this->color = [
				"r" => $col->getR(),
				"g" => $col->getG(),
				"b" => $col->getB(),
				"a" => $col->getA()
			];
		}
	}

	public function onCollideWithEntity(Entity $entity){
		parent::onCollideWithEntity($entity);

		if($this->potionId != 0 && $entity instanceof Living){
			foreach(Potion::getEffectsById($this->potionId) as $effect){
				$entity->addEffect($effect);
			}
		}
	}

	public function onUpdate(int $currentTick): bool{
		$hasUpdate = parent::onUpdate($currentTick);

		if($this->potionId != 0){
			if(!$this->isOnGround() or ($this->isOnGround() and ($currentTick % 4) == 0)){
				if($this->getLevel() instanceof Level){ // why tf would it even be null?! #BlamePocketMine (this has been an entity bug for ages now -_-)
					$this->getLevel()->addParticle(new MobSpellParticle($this->asVector3(), $this->color["r"], $this->color["g"], $this->color["b"], $this->color["a"]));
				}
			}
			$hasUpdate = true;
		}

		return $hasUpdate;
	}
}