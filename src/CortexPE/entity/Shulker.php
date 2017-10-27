<?php

namespace CortexPE\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\Monster;
use pocketmine\item\Item;

class Shulker extends Monster {
    const NETWORK_ID = self::SHULKER;

	public $width = 0.5;
	public $length = 0.9;
	public $height = 0;

    public function getName(): string {
        return "Shulker";
    }

	public function initEntity(){
		$this->setMaxHealth(30);
		$this->setDataProperty(Entity::DATA_VARIANT, Entity::DATA_TYPE_INT, mt_rand(0,15)); // TODO: Implement COLORS correctly
		parent::initEntity();
	}

    public function getDrops() : array {
		return [Item::get(Item::SHULKER_SHELL, 0, mt_rand(0, 1))];
    }
}