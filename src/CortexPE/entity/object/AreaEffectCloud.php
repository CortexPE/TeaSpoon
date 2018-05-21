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

namespace CortexPE\entity\object;

use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Potion;
use pocketmine\level\particle\Particle;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;

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

	public const TAG_POTION_ID = "PotionId";
	public const TAG_AGE = "Age";
	public const TAG_RADIUS = "Radius";
	public const TAG_RADIUS_ON_USE = "RadiusOnUse";
	public const TAG_RADIUS_PER_TICK = "RadiusPerTick";
	public const TAG_WAIT_TIME = "WaitTime";
	public const TAG_TILE_X = "TileX";
	public const TAG_TILE_Y = "TileY";
	public const TAG_TILE_Z = "TileZ";
	public const TAG_DURATION = "Duration";
	public const TAG_DURATION_ON_USE = "DurationOnUse";

	public function initEntity(): void{
		parent::initEntity();

		if(!$this->namedtag->hasTag(self::TAG_POTION_ID, ShortTag::class)){
			$this->namedtag->setShort(self::TAG_POTION_ID, $this->PotionId);
		}
		$this->PotionId = $this->namedtag->getShort(self::TAG_POTION_ID);

		if(!$this->namedtag->hasTag(self::TAG_RADIUS, FloatTag::class)){
			$this->namedtag->setFloat(self::TAG_RADIUS, $this->Radius);
		}
		$this->Radius = $this->namedtag->getFloat(self::TAG_RADIUS);

		if(!$this->namedtag->hasTag(self::TAG_RADIUS_ON_USE, FloatTag::class)){
			$this->namedtag->setFloat(self::TAG_RADIUS_ON_USE, $this->RadiusOnUse);
		}
		$this->RadiusOnUse = $this->namedtag->getFloat(self::TAG_RADIUS_ON_USE);

		if(!$this->namedtag->hasTag(self::TAG_RADIUS_PER_TICK, FloatTag::class)){
			$this->namedtag->setFloat(self::TAG_RADIUS_PER_TICK, $this->RadiusPerTick);
		}
		$this->RadiusPerTick = $this->namedtag->getFloat(self::TAG_RADIUS_PER_TICK);

		if(!$this->namedtag->hasTag(self::TAG_WAIT_TIME, IntTag::class)){
			$this->namedtag->setInt(self::TAG_WAIT_TIME, $this->WaitTime);
		}
		$this->WaitTime = $this->namedtag->getInt(self::TAG_WAIT_TIME);

		if(!$this->namedtag->hasTag(self::TAG_TILE_X, IntTag::class)){
			$this->namedtag->setInt(self::TAG_TILE_X, intval(round($this->getX())));
		}
		$this->TileX = $this->namedtag->getInt(self::TAG_TILE_X);

		if(!$this->namedtag->hasTag(self::TAG_TILE_Y, IntTag::class)){
			$this->namedtag->setInt(self::TAG_TILE_Y, intval(round($this->getY())));
		}
		$this->TileY = $this->namedtag->getInt(self::TAG_TILE_Y);

		if(!$this->namedtag->hasTag(self::TAG_TILE_Z, IntTag::class)){
			$this->namedtag->setInt(self::TAG_TILE_Z, intval(round($this->getZ())));
		}
		$this->TileZ = $this->namedtag->getInt(self::TAG_TILE_Z);

		if(!$this->namedtag->hasTag(self::TAG_DURATION, IntTag::class)){
			$this->namedtag->setInt(self::TAG_DURATION, $this->Duration);
		}
		$this->Duration = $this->namedtag->getInt(self::TAG_DURATION);

		if(!$this->namedtag->hasTag(self::TAG_DURATION_ON_USE, IntTag::class)){
			$this->namedtag->setInt(self::TAG_DURATION_ON_USE, $this->DurationOnUse);
		}
		$this->DurationOnUse = $this->namedtag->getInt(self::TAG_DURATION_ON_USE);

		$this->getDataPropertyManager()->setInt(self::DATA_AREA_EFFECT_CLOUD_PARTICLE_ID, Particle::TYPE_MOB_SPELL);//todo
		$this->getDataPropertyManager()->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS, $this->Radius);
		$this->getDataPropertyManager()->setInt(self::DATA_AREA_EFFECT_CLOUD_WAITING, $this->WaitTime);
		$this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_HEIGHT, 1);
		$this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_WIDTH, $this->Radius * 2);
		$this->getDataPropertyManager()->setByte(self::DATA_POTION_AMBIENT, 1);
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
			/** @var EffectInstance[] $effects */
			$effects = Potion::getPotionEffectsById($this->PotionId);
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

			$r /= $count;
			$g /= $count;
			$b /= $count;
			$a /= $count;

			$this->getDataPropertyManager()->setInt(self::DATA_POTION_COLOR, (($a & 0xff) << 24) | (($r & 0xff) << 16) | (($g & 0xff) << 8) | ($b & 0xff));

			$this->Radius += $this->RadiusPerTick;
			$this->getDataPropertyManager()->setFloat(self::DATA_BOUNDING_BOX_WIDTH, $this->Radius * 2);
			if($this->WaitTime > 0){
				$this->WaitTime--;
				$this->timings->stopTiming();

				return true;
			}
			/*foreach($effects as $eff){
				$eff->setDuration($this->DurationOnUse + 20);//would do nothing at 0
			}*/ // Buggy as of now...
			$bb = new AxisAlignedBB($this->x - $this->Radius, $this->y - 1, $this->z - $this->Radius, $this->x + $this->Radius, $this->y + 1, $this->z + $this->Radius);
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

		$this->getDataPropertyManager()->setFloat(self::DATA_AREA_EFFECT_CLOUD_RADIUS, $this->Radius);
		$this->getDataPropertyManager()->setInt(self::DATA_AREA_EFFECT_CLOUD_WAITING, $this->WaitTime);

		$this->timings->stopTiming();

		return $hasUpdate;
	}

	public function getName(){
		return "Area Effect Cloud";
	}

	protected function applyGravity(): void{
	}
}
