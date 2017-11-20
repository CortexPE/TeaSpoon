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

namespace CortexPE\block;

use pocketmine\block\{
	Block, BlockFactory
};

class BlockManager {
	public static function init(){
		self::register(Block::PORTAL, new Portal());
		self::register(Block::END_PORTAL, new EndPortal());
		self::register(Block::ENDER_CHEST, new EnderChest());
		self::register(Block::OBSIDIAN, new Obsidian(), true);
		self::register(Block::DRAGON_EGG, new DragonEgg());
		self::register(Block::BEACON, new Beacon());
		self::register(Block::FIRE, new Fire(), true);
		self::register(Block::BED_BLOCK, new Bed(), true);
		self::register(Block::SLIME_BLOCK, new SlimeBlock());
		self::register(Block::END_PORTAL_FRAME, new EndPortalFrame(), true);
    self::register(Block::MONSTER_SPAWNER, new MonsterSpawner(), true);
	}

	public static function register(int $id, Block $block, bool $overwrite = false): bool{
		if(!BlockFactory::isRegistered($id) && !$overwrite){
			BlockFactory::registerBlock($block);

			return true;
		}elseif($overwrite){
			BlockFactory::registerBlock($block, true);

			return true;
		}

		return false;
	}
}