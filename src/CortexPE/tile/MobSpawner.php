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

namespace CortexPE\tile;

use CortexPE\Main;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class MobSpawner extends Spawnable {

	/** @var string */
	public const
		TAG_ENTITY_ID = "EntityId",
		TAG_SPAWN_COUNT = "SpawnCount",
		TAG_SPAWN_RANGE = "SpawnRange",
		TAG_MIN_SPAWN_DELAY = "MinSpawnDelay",
		TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay",
		TAG_DELAY = "Delay";
	/** @var int */
	protected $entityId = 0;
	/** @var int */
	protected $spawnCount = 4;
	/** @var int */
	protected $spawnRange = 4;
	/** @var int */
	protected $minSpawnDelay = 200;
	/** @var int */
	protected $maxSpawnDelay = 800;
	/** @var int */
	protected $delay;

	public function getName(): string{
		return "Monster Spawner";
	}

	public function onUpdate(): bool{
		if($this->isClosed()){
			return false;
		}

		$this->timings->startTiming();

		if($this->canUpdate() && Main::$mobSpawnerEnable){
			if($this->delay <= 0){
				$success = false;
				for($i = 0; $i < $this->getSpawnCount(); $i++){
					$pos = $this->add(mt_rand() / mt_getrandmax() * $this->getSpawnRange(), mt_rand(-1, 1), mt_rand() / mt_getrandmax() * $this->getSpawnRange());
					$target = $this->getLevel()->getBlock($pos);
					if($target->getId() == Item::AIR){
						$success = true;
						$entity = Entity::createEntity($this->getEntityId(), $this->getLevel(), Entity::createBaseNBT($target->add(0.5, 0, 0.5), null, lcg_value() * 360, 0));
						if($entity instanceof Entity){
							$entity->spawnToAll();
						}
					}
				}
				if($success){
					$this->generateRandomDelay();
				}
			}else{
				$this->delay--;
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	public function canUpdate(): bool{
		if($this->getEntityId() !== 0 && $this->getLevel()->isChunkLoaded($this->getX() >> 4, $this->getZ() >> 4)){
			$hasPlayer = false;
			$count = 0;
			foreach($this->getLevel()->getEntities() as $e){
				if($e instanceof Player && $e->distance($this) <= 15){
					$hasPlayer = true;
				}
				if($e::NETWORK_ID == $this->getEntityId()){
					$count++;
				}
			}

			return ($hasPlayer && $count < 15);
		}

		return false;
	}

	protected function generateRandomDelay(): int{
		return ($this->delay = mt_rand($this->getMinSpawnDelay(), $this->getMaxSpawnDelay()));
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$this->applyBaseNBT($nbt);
	}

	private function applyBaseNBT(CompoundTag &$nbt): void{
		$nbt->setInt(self::TAG_ENTITY_ID, $this->getEntityId());
		$nbt->setInt(self::TAG_SPAWN_COUNT, $this->getSpawnCount());
		$nbt->setInt(self::TAG_SPAWN_RANGE, $this->getSpawnRange());
		$nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->getMinSpawnDelay());
		$nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->getMaxSpawnDelay());
		$nbt->setInt(self::TAG_DELAY, $this->getDelay());
	}

	/**
	 * @return int
	 */
	public function getEntityId(): int{
		return $this->entityId;
	}

	/**
	 * @param int $entityId
	 */
	public function setEntityId(int $entityId): void{
		$this->entityId = $entityId;
		$this->onChanged(); // this needs to be sent to the client so the entity animation updates too
		$this->scheduleUpdate();
	}

	/**
	 * @return int
	 */
	public function getSpawnCount(): int{
		return $this->spawnCount;
	}

	/**
	 * @param int $spawnCount
	 */
	public function setSpawnCount(int $spawnCount): void{
		$this->spawnCount = $spawnCount;
	}

	/**
	 * @return int
	 */
	public function getSpawnRange(): int{
		return $this->spawnRange;
	}

	/**
	 * @param int $spawnRange
	 */
	public function setSpawnRange(int $spawnRange): void{
		$this->spawnRange = $spawnRange;
	}

	/**
	 * @return int
	 */
	public function getMinSpawnDelay(): int{
		return $this->minSpawnDelay;
	}

	/**
	 * @param int $minSpawnDelay
	 */
	public function setMinSpawnDelay(int $minSpawnDelay): void{
		$this->minSpawnDelay = $minSpawnDelay;
	}

	/**
	 * @return int
	 */
	public function getMaxSpawnDelay(): int{
		return $this->maxSpawnDelay;
	}

	/**
	 * @param int $maxSpawnDelay
	 */
	public function setMaxSpawnDelay(int $maxSpawnDelay): void{
		$this->maxSpawnDelay = $maxSpawnDelay;
	}

	/**
	 * @return int
	 */
	public function getDelay(): int{
		return $this->delay;
	}

	/**
	 * @param int $delay
	 */
	public function setDelay(int $delay): void{
		$this->delay = $delay;
	}

	protected function readSaveData(CompoundTag $nbt): void{
		if($this->delay === null){
			$this->generateRandomDelay();
		}
		if($nbt->hasTag(self::TAG_SPAWN_COUNT, ShortTag::class) || $nbt->hasTag(self::TAG_ENTITY_ID, StringTag::class)){ // duct-tape fix for #206
			// NUKE THE OUTDATED TILE NBT. REEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE
			$nbt->removeTag(self::TAG_ENTITY_ID);
			$nbt->removeTag(self::TAG_SPAWN_COUNT);
			$nbt->removeTag(self::TAG_SPAWN_RANGE);
			$nbt->removeTag(self::TAG_MIN_SPAWN_DELAY);
			$nbt->removeTag(self::TAG_MAX_SPAWN_DELAY);
			$nbt->removeTag(self::TAG_DELAY);
		}

		if(!$nbt->hasTag(self::TAG_ENTITY_ID, IntTag::class)){
			$nbt->setInt(self::TAG_ENTITY_ID, $this->entityId);
		}
		$this->entityId = $nbt->getInt(self::TAG_ENTITY_ID, $this->entityId);

		if(!$nbt->hasTag(self::TAG_SPAWN_COUNT, IntTag::class)){
			$nbt->setInt(self::TAG_SPAWN_COUNT, $this->spawnCount);
		}
		$this->spawnCount = $nbt->getInt(self::TAG_SPAWN_COUNT, $this->spawnCount);

		if(!$nbt->hasTag(self::TAG_SPAWN_RANGE, IntTag::class)){
			$nbt->setInt(self::TAG_SPAWN_RANGE, $this->spawnRange);
		}
		$this->spawnRange = $nbt->getInt(self::TAG_SPAWN_RANGE, $this->spawnRange);

		if(!$nbt->hasTag(self::TAG_MIN_SPAWN_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);
		}
		$this->minSpawnDelay = $nbt->getInt(self::TAG_MIN_SPAWN_DELAY, $this->minSpawnDelay);

		if(!$nbt->hasTag(self::TAG_MAX_SPAWN_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);
		}
		$this->maxSpawnDelay = $nbt->getInt(self::TAG_MAX_SPAWN_DELAY, $this->maxSpawnDelay);

		if(!$nbt->hasTag(self::TAG_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_DELAY, $this->delay);
		}
		$this->delay = $nbt->getInt(self::TAG_MAX_SPAWN_DELAY, $this->delay);
		$this->scheduleUpdate();
	}

	protected function writeSaveData(CompoundTag $nbt): void{
		$this->applyBaseNBT($nbt);
	}
}
