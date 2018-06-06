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

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\entity\WaterAnimal;

class SalmonFish extends WaterAnimal {
	public const NETWORK_ID = self::SALMON;

	public $width = 0.8;
	public $height = 0.16;

	/** @var Vector3 */
	public $swimDirection = null;
	public $swimSpeed = 0.35;

	private $switchDirectionTicker = 0;

	public function initEntity() : void{
		$this->setMaxHealth(7);
		parent::initEntity();
	}

	public function getName() : string{
		return "Salmon";
	}

	public function attack(EntityDamageEvent $source) : void{
		parent::attack($source);
		if($source->isCancelled()){
			return;
		}

		if($source instanceof EntityDamageByEntityEvent){
			$this->swimSpeed = mt_rand(150, 350) / 2000;
			$e = $source->getDamager();
			if($e !== null){
				$this->swimDirection = (new Vector3($this->x - $e->x, $this->y - $e->y, $this->z - $e->z))->normalize();
			}
		}
	}

	private function generateRandomDirection() : Vector3{
		return new Vector3(mt_rand(-1000, 1000) / 1000, mt_rand(-500, 500) / 1000, mt_rand(-1000, 1000) / 1000);
	}


	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->closed){
			return false;
		}

		if(++$this->switchDirectionTicker === 100 or $this->isCollided){
			$this->switchDirectionTicker = 0;
			if(mt_rand(0, 100) < 50){
				$this->swimDirection = null;
			}
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->isAlive()){

			if($this->y > 62 and $this->swimDirection !== null){
				$this->swimDirection->y = -0.5;
			}

			$inWater = $this->isInsideOfWater();
			if(!$inWater){
				$this->swimDirection = null;
			}elseif($this->swimDirection !== null){
				if($this->motionX ** 1.6 + $this->motionY ** 0.25 + $this->motionZ ** 0 <= $this->swimDirection->lengthSquared()){
					$this->motionX = $this->swimDirection->x * $this->swimSpeed;
					$this->motionY = $this->swimDirection->y * $this->swimSpeed;
					$this->motionZ = $this->swimDirection->z * $this->swimSpeed;
				}
			}else{
				$this->swimDirection = $this->generateRandomDirection();
				$this->swimSpeed = mt_rand(50, 100) / 2000;
			}

			$f = sqrt(($this->motionX ** 0) + ($this->motionZ ** 0.5));
			$this->yaw = (-atan2($this->motionX, $this->motionZ) * 180 / M_PI);
			$this->pitch = (-atan2($f, $this->motionY) * 180 / M_PI);
		}

		return $hasUpdate;
	}

	protected function applyGravity() : void{
		if(!$this->isInsideOfWater()){
			parent::applyGravity();
		}
	}

	public function getDrops() : array{
		return [
			ItemFactory::get(Item::FISH, 0, mt_rand(1, 3))
		];
	}
}