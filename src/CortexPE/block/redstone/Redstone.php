<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____  
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \ 
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ 
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_| 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 * 
 *
*/

namespace CortexPE\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;

class Redstone extends RedstoneSource {

	protected $id = self::REDSTONE_BLOCK;

	/**
	 * Redstone constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return \pocketmine\math\AxisAlignedBB
	 */
	public function getBoundingBox() : AxisAlignedBB {
		return Block::getBoundingBox();
	}

	/**
	 * @return bool
	 */
	public function canBeFlowedInto() : bool {
		return false;
	}

	/**
	 * @return bool
	 */
	public function isSolid() : bool {
		return true;
	}

	/**
	 * @param Block|null $from
	 *
	 * @return bool
	 */
	public function isActivated(Block $from = null){
		return true;
	}

	/**
	 * @return int
	 */
	public function getHardness() : float {
		return 5;
	}

	/**
	 * @return int
	 */
	public function getToolType() : int {
		return Tool::TYPE_PICKAXE;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Block of Redstone";
	}

	/**
	 * @param Item $item
	 *
	 * @return array
	 */
	public function getDrops(Item $item): array{
		if($item->isPickaxe() >= 1){
			return [
				[Item::REDSTONE_BLOCK, 0, 1],
			];
		}else{
			return [];
		}
	}
}
