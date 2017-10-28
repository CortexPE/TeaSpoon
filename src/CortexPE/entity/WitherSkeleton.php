<?php

namespace CortexPE\entity;

use pocketmine\item\Item;

class WitherSkeleton extends Skeleton {
	const NETWORK_ID = self::WITHER_SKELETON;

	public $width = 0.3;
	public $length = 0.9;
	public $height = 0;

	public function getName(): string{
		return "Wither Skeleton";
	}

	public function initEntity(){
		$this->setMaxHealth(20);
		parent::initEntity();
	}

	public function getDrops(): array{
		return [
			Item::get(Item::COAL, 0, mt_rand(0, 1)),
			Item::get(Item::BONE, 0, mt_rand(0, 2)),
		];
	}
}