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


use CortexPE\item\utils\FireworksData;
use CortexPE\Utils;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

class Firework extends Utils {
	public static function fireworkData2NBT(FireworksData $data){
		// https://github.com/thebigsmileXD/fireworks/blob/master/src/xenialdan/fireworks/item/Fireworks.php#L54-L74
		$value = [];
		$root = new CompoundTag();
		foreach($data->explosions as $explosion){
			$tag = new CompoundTag();
			$tag->setByteArray("FireworkColor", (string)$explosion->fireworkColor[0]);
			$tag->setByteArray("FireworkFade", (string)$explosion->fireworkFade[0]);
			$tag->setByte("FireworkFlicker", ($explosion->fireworkFlicker ? 1 : 0));
			$tag->setByte("FireworkTrail", ($explosion->fireworkTrail ? 1 : 0));
			$tag->setByte("FireworkType", $explosion->fireworkType);
			$value[] = $tag;
		}
		$explosions = new ListTag("Explosions", $value, NBT::TAG_Compound);
		$root->setTag(new CompoundTag("Fireworks",
				[
					$explosions,
					new ByteTag("Flight", $data->flight),
				])
		);

		return $root;
	}
}