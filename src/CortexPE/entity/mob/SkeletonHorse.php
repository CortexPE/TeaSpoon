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

// Andrew Gold - Spooky Scary Skeletons

/**
 * Spooky, scary skeletons
 * Send shivers down your spine
 * Shrieking skulls will shock your soul
 * Seal your doom tonight
 * Spooky, scary skeletons
 * Speak with such a screech
 * You'll shake and shudder in surprise
 * When you hear these zombies shriek
 * We're sorry skeletons, you're so misunderstood
 * You only want to socialize, but I don't think we should
 */

namespace CortexPE\entity\mob;

use pocketmine\item\Item;

class SkeletonHorse extends Horse {

	public const NETWORK_ID = self::SKELETON_HORSE;

	public function getName(): string{
		return "Skeleton Horse";
	}

	public function initEntity(): void{
		$this->setMaxHealth(30);
		parent::initEntity();
	}

	public function getDrops(): array{
		return $drops = [
			Item::get(Item::BONE, 0, mt_rand(0, 2)),
		];
	}
}
