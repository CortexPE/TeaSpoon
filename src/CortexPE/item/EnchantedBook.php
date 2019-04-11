<?php


namespace CortexPE\item;


use pocketmine\item\Item;

class EnchantedBook extends Item {
	public function __construct(int $meta = 0) {
		parent::__construct(self::ENCHANTED_BOOK, $meta, "Enchanted Book");
	}

	public function getMaxStackSize(): int {
		return 1;
	}
}