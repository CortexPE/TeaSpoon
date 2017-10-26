<?php

namespace CortexPE\tile;

use pocketmine\tile\Tile as PMTile;

class Tile extends PMTile {
	const ENDER_CHEST = "Ender Chest";

	public static function init(){
		PMTile::registerTile(EnderChest::class);
	}
}