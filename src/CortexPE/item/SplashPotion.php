<?php

namespace CortexPE\item;

use pocketmine\nbt\tag\ShortTag;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\Item;
use pocketmine\item\ProjectileItem;
use pocketmine\level\sound\LaunchSound;
use pocketmine\math\Vector3;

class SplashPotion extends ProjectileItem {

	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::SPLASH_POTION, $meta, $count, $this->getNameByMeta($meta));
	}

	public function getNameByMeta(int $meta){
		return "Splash " . Potion::getNameByMeta($meta);
	}

	public function getProjectileEntityType() : string{
		return "SplashPotion";
	}

	public function getThrowForce(): float{
		return 1.1;
	}

	public function getMaxStackSize(): int{
		return 16;
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		$nbt = Entity::createBaseNBT($player->add(0, $player->getEyeHeight(), 0), $directionVector, $player->yaw, $player->pitch);
		$nbt["PotionId"] = new ShortTag("PotionId", $this->meta);

		$projectile = Entity::createEntity($this->getProjectileEntityType(), $player->getLevel(), $nbt, $player);
		if($projectile !== null){
			$projectile->setMotion($projectile->getMotion()->multiply($this->getThrowForce()));
		}

		$this->count--;

		if($projectile instanceof Projectile){
			$player->getServer()->getPluginManager()->callEvent($projectileEv = new ProjectileLaunchEvent($projectile));
			if($projectileEv->isCancelled()){
				$projectile->kill();
			}else{
				$projectile->spawnToAll();
				$player->getLevel()->addSound(new LaunchSound($player), $player->getViewers());
			}
		}else{
			$projectile->spawnToAll();
		}

		return true;
	}

}