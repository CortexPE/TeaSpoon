<?php

/*
 * Credits to @thebigsmileXD (XenialDan)
 * Original Repository: https://github.com/thebigsmileXD/fireworks
 * Ported to TeaSpoon as TeaSpoon overrides the fireworks item (as Elytra Booster)
 * Licensed under the MIT License (January 1, 2018)
 *
 * Modified to add explosion damage and a few fixes
 * */

declare(strict_types = 1);

namespace CortexPE\entity\projectile;

use CortexPE\item\Fireworks;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

class FireworkRocket extends Projectile {

	public const NETWORK_ID = self::FIREWORKS_ROCKET;

	public $width = 0.25;
	public $height = 0.25;
	public $gravity = 0.0;
	public $drag = 0.01;
	public $random;
	public $fireworks;
	private $lifeTime = 0;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null, ?Fireworks $item = null, ?Random $random = null){
		$this->random = $random;
		$this->fireworks = $item;
		parent::__construct($level, $nbt, $shootingEntity);
	}

	/**
	 * @param Player[]|Player $player
	 * @param array $data Properly formatted entity data, defaults to everything
	 */
	public function sendData($player, array $data = null): void{
		if(!is_array($player)){
			$player = [$player];
		}
		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->metadata = $data ?? $this->getDataPropertyManager()->getDirty();
		foreach($player as $p){
			if($p === $this){
				continue;
			}
			$p->dataPacket(clone $pk);
		}
		if($this instanceof Player){
			$this->dataPacket($pk);
		}
	}

	public function spawnTo(Player $player): void{
		$this->setMotion($this->getDirectionVector());
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LAUNCH);
		parent::spawnTo($player);
	}

	public function despawnFromAll(): void{
		if(count($this->namedtag->getCompoundTag(Fireworks::TAG_FIREWORKS)->getListTag(Fireworks::TAG_EXPLOSIONS)) > 0){
			foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->expand(5, 5, 5)) as $entity){ // 4 1/2 blocks acording to the wiki
				if($entity instanceof Living){
					$distance = $this->distance($entity);

					$distance = ($distance > 0 ? $distance : 1);

					$k = 22.5; // 4.5 * 5
					$damage = $k / $distance; // inverse variation

					if($damage > 0){
						$dmgEv = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_CUSTOM, $damage); // todo: figure out constant for firework damage
						$entity->attack($dmgEv);
					}
				}
			}
		}

		$this->broadcastEntityEvent(EntityEventPacket::FIREWORK_PARTICLES, 0);
		parent::despawnFromAll();
		$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
	}

	public function entityBaseTick(int $tickDiff = 1): bool{
		if($this->lifeTime-- < 0){
			$this->flagForDespawn();

			return true;
		}else{
			return parent::entityBaseTick($tickDiff);
		}
	}

	protected function initEntity(): void{
		parent::initEntity();
		$random = $this->random ?? new Random();
		$this->setGenericFlag(self::DATA_FLAG_HAS_COLLISION, true);
		$this->setGenericFlag(self::DATA_FLAG_AFFECTED_BY_GRAVITY, true);
		if($this->fireworks instanceof Item){
			$this->getDataPropertyManager()->setItem(16, Item::get($this->fireworks->getId(), $this->fireworks->getDamage(), $this->fireworks->getCount(), $this->fireworks->getCompoundTag()));
		}else{
			$this->getDataPropertyManager()->setItem(16, Item::get(Item::FIREWORKS));
		}
		//id [1][0], meta $d[1][2], count $d[1][1], data $d[1][3]
		$flyTime = 1;
		try{
			if(!is_null($this->namedtag->getCompoundTag(Fireworks::TAG_FIREWORKS))){
				$fireworksNBT = $this->namedtag->getCompoundTag(Fireworks::TAG_FIREWORKS);
				if($fireworksNBT->hasTag(Fireworks::TAG_FLIGHT, ByteTag::class)){
					$flyTime = $fireworksNBT->getByte(Fireworks::TAG_FLIGHT, 1);
				}
			}
		}catch(\Exception $exception){
			$this->server->getLogger()->debug($exception);
		}
		$this->lifeTime = 20 * $flyTime + $random->nextBoundedInt(5) + $random->nextBoundedInt(7);
	}
}
