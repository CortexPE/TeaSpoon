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
 * @author NycuRO
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE\utils;

use pocketmine\utils\TextFormat as PMTextFormat;

class TextFormat extends PMTextFormat {

	public const RGB_BLACK = [0,0,0];
	public const RGB_DARK_BLUE = [0,0,42];
	public const RGB_DARK_GREEN = [0,42,0];
	public const RGB_DARK_AQUA = [0,42,42];
	public const RGB_DARK_RED = [42,0,0];
	public const RGB_DARK_PURPLE = [42,0,42];
	public const RGB_GOLD = [42,42,0];
	public const RGB_GRAY = [42,42,42];
	public const RGB_DARK_GRAY = [21,21,21];
	public const RGB_BLUE = [21,21,63];
	public const RGB_GREEN = [21,63,21];
	public const RGB_AQUA = [21,63,63];
	public const RGB_RED = [63,21,21];
	public const RGB_LIGHT_PURPLE = [63,21,63];
	public const RGB_YELLOW = [63,63,21];
	public const RGB_WHITE = [63,63,63];

	public static function center($input): string{
		$clear = TextFormat::clean($input);
		$lines = explode("\n", $clear);
		$max = max(array_map("strlen", $lines));
		$lines = explode("\n", $input);
		foreach($lines as $key => $line){
			$lines[$key] = str_pad($line, $max + self::colorCount($line), " ", STR_PAD_BOTH);
		}

		return implode("\n", $lines);
	}

	public static function colorCount($input): int{
		$colors = "abcdef0123456789lo";
		$count = 0;
		for($i = 0; $i < strlen($colors); $i++){
			$count += substr_count($input, "§" . $colors{$i});
		}

		return $count;
	}
}