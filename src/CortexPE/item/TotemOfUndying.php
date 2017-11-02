<?php
declare(strict_types=1);

namespace CortexPE\item;

use pocketmine\item\Item;

class TotemOfUndying extends Item {
	public function __construct($meta = 0, $count = 1){
		parent::__construct(Item::TOTEM, $meta, "Totem Of Undying");
	}
}