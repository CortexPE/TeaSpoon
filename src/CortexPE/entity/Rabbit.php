<?php

namespace CortexPE\entity;

use CortexPE\item\enchantment\Enchantment;
use CortexPE\Player;
use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;

class Rabbit extends Animal {
    const NETWORK_ID = self::RABBIT;

	public $width = 0.5;
	public $length = 0.5;
	public $height = 0.5;

	const DATA_RABBIT_TYPE = 18;
	const DATA_JUMP_TYPE = 19;

	const TYPE_BROWN = 0;
	const TYPE_WHITE = 1;
	const TYPE_BLACK = 2;
	const TYPE_BLACK_WHITE = 3;
	const TYPE_GOLD = 4;
	const TYPE_SALT_PEPPER = 5;
	const TYPE_KILLER_BUNNY = 99;

    public function getName(): string {
        return "Rabbit";
    }

	public function initEntity(){
		$this->setMaxHealth(3);
		parent::initEntity();
	}

	public function __construct(Level $level, CompoundTag $nbt){
		if(!isset($nbt->RabbitType)){
			$nbt->RabbitType = new ByteTag("RabbitType", $this->getRandomRabbitType());
		}
		parent::__construct($level, $nbt);

		$this->setDataProperty(self::DATA_RABBIT_TYPE, self::DATA_TYPE_BYTE, $this->getRabbitType());
	}

	public function getRandomRabbitType(): int{
		$arr = [0, 1, 2, 3, 4, 5, 99];

		return $arr[mt_rand(0, count($arr) - 1)];
	}

	public function setRabbitType(int $type){
		$this->namedtag->RabbitType = new ByteTag("RabbitType", $type);
	}

	public function getRabbitType(): int{
		return (int)$this->namedtag["RabbitType"];
	}

    public function getDrops() : array {
		$lootingL = 0;
		$cause = $this->lastDamageCause;
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$lootingL = $damager->getInventory()->getItemInHand()->getEnchantment(Enchantment::LOOTING)->getLevel();
			}
		}
		$drops = [Item::get(Item::RABBIT_HIDE, 0, mt_rand(0, 1))];
		if($this->getLastDamageCause() === EntityDamageEvent::CAUSE_FIRE){
			$drops[] = Item::get(Item::COOKED_RABBIT, 0, mt_rand(0, 1));
		}else{
			$drops[] = Item::get(Item::RAW_RABBIT, 0, mt_rand(0, 1));
		}
		if(mt_rand(1, 200) <= (5 + 2 * $lootingL)){
			$drops[] = Item::get(Item::RABBIT_FOOT, 0, 1);
		}

		return $drops;
    }
}