<?php

namespace CortexPE\entity;

use CortexPE\Main;
use pocketmine\block\Liquid;
use pocketmine\entity\Animal;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;

class Lightning extends Animal {
	const NETWORK_ID = self::LIGHTNING_BOLT;

	public $doneDamage = false;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 1.8;

	public function getName(): string{
		return "Lightning";
	}

	public function initEntity(){
		$this->setMaxHealth(2);
		$this->setHealth(2);
		parent::initEntity();
	}

	public function onUpdate(int $currentTick): bool{
		if(!$this->doneDamage){
			// Tnx Genisys
			if(Main::$lightningFire){
				$this->doneDamage = true;
				$fire = Item::get(Item::FIRE)->getBlock();
				$oldBlock = $this->getLevel()->getBlock($this);
				if($oldBlock instanceof Liquid){

				}elseif($oldBlock->isSolid()){
					$v3 = new Vector3($this->x, $this->y + 1, $this->z);
				}else{
					$v3 = new Vector3($this->x, $this->y, $this->z);
				}
				if(isset($v3)) $this->getLevel()->setBlock($v3, $fire);

				foreach($this->level->getNearbyEntities($this->boundingBox->grow(4, 3, 4), $this) as $entity){
					if($entity instanceof Living){
						$damage = mt_rand(8, 20);
						$ev = new EntityDamageByEntityEvent($this, $entity, 16, $damage); // LIGHTNING
						/*if($entity->attack($ev) === true){
							if($entity instanceof Player){
								$ev->useArmors();
							}
						}*/
						$entity->attack($ev);
						$entity->setOnFire(mt_rand(3, 8));
					}

					/*if($entity instanceof Creeper){
						$entity->setPowered(true, $this);
					}*/
				}
			}
		}
		if($this->age > 10 * 20){
			$this->kill();
			$this->close();
		}

		return parent::onUpdate($currentTick);
	}
}