<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ClearSky
 * @link https://github.com/ClearSkyTeam/PocketMine-MP
 *
*/

// Modded by @CortexPE to add Multi-Effects

namespace CortexPE\entity;

use CortexPE\item\Potion;
use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\level\particle\Particle;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\utils\Color;

class AreaEffectCloud extends Entity {
	const NETWORK_ID = self::AREA_EFFECT_CLOUD;

	public $width = 5;
	public $length = 5;
	public $height = 1;

	private $PotionId = 0;
	private $Radius = 3;
	private $RadiusOnUse = -0.5;
	private $RadiusPerTick = -0.005;
	private $WaitTime = 10;
	private $TileX = 0;
	private $TileY = 0;
	private $TileZ = 0;
	private $Duration = 600;
	private $DurationOnUse = 0;

	public function initEntity(){
		parent::initEntity();

		if(!isset($this->namedtag->PotionId) or !($this->namedtag->PotionId instanceof ShortTag)){
			$this->namedtag->PotionId = new ShortTag("PotionId", $this->PotionId);
		}
		$this->PotionId = $this->namedtag->PotionId->getValue();

		if(!isset($this->namedtag->Radius) or !($this->namedtag->Radius instanceof FloatTag)){
			$this->namedtag->Radius = new FloatTag("Radius", $this->Radius);
		}
		$this->Radius = $this->namedtag->Radius->getValue();

		if(!isset($this->namedtag->RadiusOnUse) or !($this->namedtag->RadiusOnUse instanceof FloatTag)){
			$this->namedtag->RadiusOnUse = new FloatTag("RadiusOnUse", $this->RadiusOnUse);
		}
		$this->RadiusOnUse = $this->namedtag->RadiusOnUse->getValue();

		if(!isset($this->namedtag->RadiusPerTick) or !($this->namedtag->RadiusPerTick instanceof FloatTag)){
			$this->namedtag->RadiusPerTick = new FloatTag("RadiusPerTick", $this->RadiusPerTick);
		}
		$this->RadiusPerTick = $this->namedtag->RadiusPerTick->getValue();

		if(!isset($this->namedtag->WaitTime) or !($this->namedtag->WaitTime instanceof IntTag)){
			$this->namedtag->WaitTime = new IntTag("WaitTime", $this->WaitTime);
		}
		$this->WaitTime = $this->namedtag->WaitTime->getValue();

		if(!isset($this->namedtag->TileX) or !($this->namedtag->TileX instanceof IntTag)){
			$this->namedtag->TileX = new IntTag("TileX", (int)round($this->getX()));
		}
		$this->TileX = $this->namedtag->TileX->getValue();

		if(!isset($this->namedtag->TileY) or !($this->namedtag->TileY instanceof IntTag)){
			$this->namedtag->TileY = new IntTag("TileY", (int)round($this->getY()));
		}
		$this->TileY = $this->namedtag->TileY->getValue();

		if(!isset($this->namedtag->TileZ) or !($this->namedtag->TileZ instanceof IntTag)){
			$this->namedtag->TileZ = new IntTag("TileZ", (int)round($this->getZ()));
		}
		$this->TileZ = $this->namedtag->TileZ->getValue();

		if(!isset($this->namedtag->Duration) or !($this->namedtag->Duration instanceof IntTag)){
			$this->namedtag->Duration = new IntTag("Duration", $this->Duration);
		}
		$this->Duration = $this->namedtag->Duration->getValue();

		if(!isset($this->namedtag->DurationOnUse) or !($this->namedtag->DurationOnUse instanceof IntTag)){
			$this->namedtag->DurationOnUse = new IntTag("DurationOnUse", $this->DurationOnUse);
		}
		$this->DurationOnUse = $this->namedtag->DurationOnUse->getValue();

		$this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_PARTICLE_ID, self::DATA_TYPE_INT, Particle::TYPE_MOB_SPELL);//todo
		$this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_RADIUS, self::DATA_TYPE_FLOAT, $this->Radius);
		$this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_WAITING, self::DATA_TYPE_INT, $this->WaitTime);
		$this->setDataProperty(self::DATA_BOUNDING_BOX_HEIGHT, self::DATA_TYPE_FLOAT, 1);
		$this->setDataProperty(self::DATA_BOUNDING_BOX_WIDTH, self::DATA_TYPE_FLOAT, $this->Radius * 2);
		$this->setDataProperty(self::DATA_POTION_AMBIENT, self::DATA_TYPE_BYTE, 1);
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		if($this->closed){
			return false;
		}

		$this->timings->startTiming();

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > $this->Duration || $this->PotionId == 0 || $this->Radius <= 0){
			$this->close();
			$hasUpdate = true;
		}else{
			/** @var Effect[] $effects */
			$effects = Potion::getEffectsById($this->PotionId);
			if(count($effects) <= 0){
				$this->close();
				$this->timings->stopTiming();

				return true;
			}

			// Multi effect color... Based off of Color::mix()
			$count = $r = $g = $b = $a = 0;
			foreach($effects as $effect){
				$ecol = $effect->getColor();
				$r += $ecol->getR();
				$g += $ecol->getG();
				$b += $ecol->getB();
				$a += $ecol->getA();
				$count++;
			}
			$color = new Color((int) ($r / $count), (int) ($g / $count), (int) ($b / $count), (int) ($a / $count));

			$this->setDataProperty(self::DATA_POTION_COLOR, self::DATA_TYPE_INT, ((255 & 0xff) << 24) | (($color->getR() & 0xff) << 16) | (($color->getG() & 0xff) << 8) | ($color->getB() & 0xff));
			$this->Radius += $this->RadiusPerTick;
			$this->setDataProperty(self::DATA_BOUNDING_BOX_WIDTH, self::DATA_TYPE_FLOAT, $this->Radius * 2);
			if($this->WaitTime > 0){
				$this->WaitTime--;
				$this->timings->stopTiming();

				return true;
			}
			/*foreach($effects as $eff){
				$eff->setDuration($this->DurationOnUse + 20);//would do nothing at 0
			}*/ // Buggy as of now...
			$bb = new AxisAlignedBB($this->x - $this->Radius, $this->y, $this->z - $this->Radius, $this->x + $this->Radius, $this->y + $this->height, $this->z + $this->Radius);
			$used = false;
			foreach($this->getLevel()->getCollidingEntities($bb, $this) as $collidingEntity){
				if($collidingEntity instanceof Living && $collidingEntity->distanceSquared($this) <= $this->Radius ** 2){
					$used = true;
					foreach($effects as $eff){
						$collidingEntity->addEffect($eff);
					}
				}
			}
			if($used){
				$this->Duration -= $this->DurationOnUse;
				$this->Radius += $this->RadiusOnUse;
				$this->WaitTime = 10;
			}
		}

		$this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_RADIUS, self::DATA_TYPE_FLOAT, $this->Radius);
		$this->setDataProperty(self::DATA_AREA_EFFECT_CLOUD_WAITING, self::DATA_TYPE_INT, $this->WaitTime);

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	public function getName(){
		return "Area Effect Cloud";
	}

	protected function applyGravity(){
	}
}
