<?php

namespace CortexPE\entity;

use pocketmine\entity\Animal;
use pocketmine\entity\Entity;
use pocketmine\item\Item;

class Llama extends Animal {
	const NETWORK_ID = self::LLAMA;

	const CREAMY = 0;
	const WHITE = 1;
	const BROWN = 2;
	const GRAY = 3;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

	public function getName(): string{
		return "Llama";
	}

	public function initEntity(){
		$this->setMaxHealth(30);
		$this->setDataProperty(Entity::DATA_VARIANT, Entity::DATA_TYPE_INT, rand(0, 3));
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			Item::get(Item::LEATHER, 0, mt_rand(0, 2)),
		];
	}
}