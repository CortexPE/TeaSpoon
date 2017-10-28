<?php

// Andrew Gold - Spooky Scary Skeletons

/**
 * Spooky, scary skeletons
 * Send shivers down your spine
 * Shrieking skulls will shock your soul
 * Seal your doom tonight
 * Spooky, scary skeletons
 * Speak with such a screech
 * You'll shake and shudder in surprise
 * When you hear these zombies shriek
 * We're sorry skeletons, you're so misunderstood
 * You only want to socialize, but I don't think we should
 */

namespace CortexPE\entity;

use pocketmine\entity\Monster;
use pocketmine\item\Item;

class Skeleton extends Monster {
	const NETWORK_ID = self::SKELETON;

	public function getName(): string{
		return "Skeleton";
	}

	public function getDrops(): array{
		return [
			Item::get(Item::ARROW, 0, mt_rand(0, 2)),
			Item::get(Item::BONE, 0, mt_rand(0, 2)),
		];
	}
}