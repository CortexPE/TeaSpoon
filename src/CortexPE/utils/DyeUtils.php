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


use CortexPE\Utils;
use pocketmine\utils\Color;

class DyeUtils extends Utils {
	public const DYE_BLACK = 0;
	public const DYE_RED = 1;
	public const DYE_GREEN = 2;
	public const DYE_BROWN = 3;
	public const DYE_BLUE = 4;
	public const DYE_PURPLE = 5;
	public const DYE_CYAN = 6;
	public const DYE_LIGHT_GRAY = 7, DYE_SILVER = 7;
	public const DYE_GRAY = 8;
	public const DYE_PINK = 9;
	public const DYE_LIME = 10;
	public const DYE_YELLOW = 11;
	public const DYE_LIGHT_BLUE = 12;
	public const DYE_MAGENTA = 13;
	public const DYE_ORANGE = 14;
	public const DYE_WHITE = 15;

	public static function getDyeColor(int $id) : Color {
		switch($id){
			case self::DYE_BLACK:
				return new Color(30, 27, 27);
			case self::DYE_RED:
				return new Color(179, 49, 44);
			case self::DYE_GREEN:
				return new Color(61, 81, 26);
			case self::DYE_BROWN:
				return new Color(81, 48, 26);
			case self::DYE_BLUE:
				return new Color(37, 49, 146);
			case self::DYE_PURPLE:
				return new Color(123, 47, 190);
			case self::DYE_CYAN:
				return new Color(40, 118, 151);
			case self::DYE_SILVER:
				return new Color(153, 153, 153);
			case self::DYE_GRAY:
				return new Color(67, 67, 67);
			case self::DYE_PINK:
				return new Color(216, 129, 152);
			case self::DYE_LIME:
				return new Color(65, 205, 52);
			case self::DYE_YELLOW:
				return new Color(222, 207, 42);
			case self::DYE_LIGHT_BLUE:
				return new Color(102, 137, 211);
			case self::DYE_MAGENTA:
				return new Color(195, 84, 205);
			case self::DYE_ORANGE:
				return new Color(235, 136, 68);
			case self::DYE_WHITE:
				return new Color(240, 240, 240);
		}
		return new Color(0, 0, 0);
	}
}