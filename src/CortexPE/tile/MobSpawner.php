<?php

/*
 * Credits: https://github.com/thebigsmileXD/SimpleSpawner
*/

declare(strict_types = 1);

namespace CortexPE\tile;

use CortexPE\Main;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class MobSpawner extends Spawnable {

	public const TAG_ENTITY_ID = "EntityId";
	public const TAG_SPAWN_COUNT = "SpawnCount";
	public const TAG_SPAWN_RANGE = "SpawnRange";
	public const TAG_MIN_SPAWN_DELAY = "MinSpawnDelay";
	public const TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay";
	public const TAG_DELAY = "Delay";

	public function __construct(Level $level, CompoundTag $nbt){
		if(!$nbt->hasTag(self::TAG_ENTITY_ID, IntTag::class)){
			$nbt->setInt(self::TAG_ENTITY_ID, 0);
		}
		if(!$nbt->hasTag(self::TAG_SPAWN_COUNT, IntTag::class)){
			$nbt->setInt(self::TAG_SPAWN_COUNT, 4);
		}
		if(!$nbt->hasTag(self::TAG_SPAWN_RANGE, IntTag::class)){
			$nbt->setInt(self::TAG_SPAWN_RANGE, 4);
		}
		if(!$nbt->hasTag(self::TAG_MIN_SPAWN_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_MIN_SPAWN_DELAY, 200);
		}
		if(!$nbt->hasTag(self::TAG_MAX_SPAWN_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_MAX_SPAWN_DELAY, 800);
		}
		if(!$nbt->hasTag(self::TAG_DELAY, IntTag::class)){
			$nbt->setInt(self::TAG_DELAY, mt_rand($nbt->getInt(self::TAG_MIN_SPAWN_DELAY), $nbt->getInt(self::TAG_MAX_SPAWN_DELAY)));
		}
		parent::__construct($level, $nbt);
		if($this->getEntityId() > 0){
			$this->scheduleUpdate();
		}
	}

	public function getEntityId(){
		return $this->getNBT()->getInt(self::TAG_ENTITY_ID);
	}

	public function setEntityId(int $id){
		$this->getNBT()->setInt(self::TAG_ENTITY_ID, $id);
		$this->onChanged();
		$this->scheduleUpdate();
	}

	public function setSpawnCount(int $value){
		$this->getNBT()->setInt(self::TAG_SPAWN_COUNT, $value);
	}

	public function setSpawnRange(int $value){
		$this->getNBT()->setInt(self::TAG_SPAWN_RANGE, $value);
	}

	public function setMinSpawnDelay(int $value){
		$this->getNBT()->setInt(self::TAG_MIN_SPAWN_DELAY, $value);
	}

	public function setMaxSpawnDelay(int $value){
		$this->getNBT()->setInt(self::TAG_MAX_SPAWN_DELAY, $value);
	}

	public function getName(): string{
		return "Monster Spawner";
	}

	public function onUpdate(): bool{
		if($this->closed === true){
			return false;
		}

		$this->timings->startTiming();

		if(!($this->chunk instanceof Chunk)){
			return false;
		}
		if($this->canUpdate() && Main::$mobSpawnerEnable){
			if($this->getDelay() <= 0){
				$success = 0;
				for($i = 0; $i < $this->getSpawnCount(); $i++){
					$pos = $this->add(mt_rand() / mt_getrandmax() * $this->getSpawnRange(), mt_rand(-1, 1), mt_rand() / mt_getrandmax() * $this->getSpawnRange());
					$target = $this->getLevel()->getBlock($pos);
					if($target->getId() == Item::AIR){
						$success++;
						$entity = Entity::createEntity($this->getEntityId(), $this->getLevel(), Entity::createBaseNBT($target->add(0.5, 0, 0.5), null, lcg_value() * 360, 0));
						if($entity instanceof Entity){
							$entity->spawnToAll();
						}
					}
				}
				if($success > 0){
					$this->setDelay(mt_rand($this->getMinSpawnDelay(), $this->getMaxSpawnDelay()));
				}
			}else{
				$this->setDelay($this->getDelay() - 1);
			}
		}

		$this->timings->stopTiming();

		return true;
	}

	public function canUpdate(): bool{
		if($this->getEntityId() === 0) return false;
		$hasPlayer = false;
		$count = 0;
		foreach($this->getLevel()->getEntities() as $e){
			if($e instanceof Player){
				if($e->distance($this->getBlock()) <= 15) $hasPlayer = true;
			}
			if($e::NETWORK_ID == $this->getEntityId()){
				$count++;
			}
		}
		if($hasPlayer and $count < 15){ // Spawn limit = 15
			return true;
		}

		return false;
	}

	public function getDelay(){
		return $this->getNBT()->getInt(self::TAG_DELAY);
	}

	public function getSpawnCount(){
		return $this->getNBT()->getInt(self::TAG_SPAWN_COUNT);
	}

	public function getSpawnRange(){
		return $this->getNBT()->getInt(self::TAG_SPAWN_RANGE);
	}

	public function setDelay(int $value){
		$this->getNBT()->setInt(self::TAG_DELAY, $value);
	}

	public function getMinSpawnDelay(){
		return $this->getNBT()->getInt(self::TAG_MIN_SPAWN_DELAY);
	}

	public function getMaxSpawnDelay(){
		return $this->getNBT()->getInt(self::TAG_MAX_SPAWN_DELAY);
	}

	public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$nbt->setInt(self::TAG_ENTITY_ID, $this->getNBT()->getInt(self::TAG_ENTITY_ID));
		$nbt->setInt(self::TAG_DELAY, $this->getNBT()->getInt(self::TAG_DELAY));
		$nbt->setInt(self::TAG_SPAWN_COUNT, $this->getNBT()->getInt(self::TAG_SPAWN_COUNT));
		$nbt->setInt(self::TAG_SPAWN_RANGE, $this->getNBT()->getInt(self::TAG_SPAWN_RANGE));
		$nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->getNBT()->getInt(self::TAG_MIN_SPAWN_DELAY));
		$nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->getNBT()->getInt(self::TAG_MAX_SPAWN_DELAY));
	}
}
