<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\utils;

use CortexPE\Main;
use pocketmine\block\Block;

/**
 * INTERNAL helper for Railway
 * <p>
 * By lmlstarqaq http://snake1999.com/
 * Rewrite by larryTheCoder
 * @package CortexPE\utils
 */
class RailUtils {

	public static function isRailBlock($block): bool{
		if(is_null($block)){
			Main::getPluginLogger()->error("Rail block predicate can not accept null block");

			return false;
		}
		$id = $block;
		if($block instanceof Block){
			$id = $block->getId();
		}

		switch($id){
			case Block::RAIL:
			case Block::POWERED_RAIL:
			case Block::ACTIVATOR_RAIL:
			case Block::DETECTOR_RAIL:
				return true;
			default:
				return false;
		}
	}

}

