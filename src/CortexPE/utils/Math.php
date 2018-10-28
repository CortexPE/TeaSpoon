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

class Math extends Utils {
	public static function getPercentage(float $min, float $max){
		return ((min($min, $max) / max($min, $max)) * 100);
	}

	public static function clamp($value, $min, $max){
		return $value < $min ? $min : ($value > $max ? $max : $value);
	}

	public static function getDirection(float $d0, $d1){
		if($d0 < 0){
			$d0 = -$d0;
		}

		if($d1 < 0){
			$d1 = -$d1;
		}

		return $d0 > $d1 ? $d0 : $d1;
	}

	public static function wrapDegrees(float $yaw){
		$yaw %= 360.0;
		if($yaw >= 180.0){
			$yaw -= 360.0;
		}
		if($yaw < -180.0){
			$yaw += 360.0;
		}

		return $yaw;
	}
}