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

namespace CortexPE\block\redstone;

use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;

class Tripwire extends Transparent {

	protected $id = self::TRIPWIRE;

	/**
	 * Tripwire constructor.
	 *
	 * @param int $meta
	 */
	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	/**
	 * @return string
	 */
	public function getName(): string{
		return "Tripwire";
	}

	/**
	 * @return int
	 */
	public function getToolType() : int {
		return BlockToolType::TYPE_SHEARS;
	}

	public function getHardness() : float {
		return 0;
	}

	public function getResistance() : float {
		return 0;
	}

}
