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

use CortexPE\level\particle\MobSpellParticle;
use CortexPE\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Arrow as PMArrow;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Color;

class Arrow extends PMArrow {
	/** @var int */
	protected $potionId;
	/** @var Color */
	protected $color;

	public function initEntity(): void{
		$this->potionId = $this->namedtag->getShort("Potion", 0);
		if($this->potionId >= 1 && $this->potionId <= 36){
			$this->color = Utils::getPotionColor($this->potionId);
		}

		parent::initEntity();
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult): void{
		parent::onHitEntity($entityHit, $hitResult);

		if($this->potionId >= 1 && $this->potionId <= 36 && $entityHit instanceof Living){
			foreach(Potion::getPotionEffectsById($this->potionId) as $effect){
				$entityHit->addEffect($effect);
			}
		}
	}

	public function onUpdate(int $currentTick): bool{
		$hasUpdate = parent::onUpdate($currentTick);

		if($this->potionId >= 1 && $this->potionId <= 36){
			if(!$this->isOnGround() or ($this->isOnGround() and ($currentTick % 4) == 0)){
				if($this->getLevel() instanceof Level && $this->color instanceof Color){
					$this->getLevel()->addParticle(new MobSpellParticle($this->asVector3(), $this->color->getR(), $this->color->getG(), $this->color->getB(), $this->color->getA()));
				}
			}
			$hasUpdate = true;
		}

		return $hasUpdate;
	}
}
