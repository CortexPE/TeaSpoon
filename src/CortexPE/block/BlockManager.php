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

use CortexPE\Main;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class BlockManager {
	public static function init(): void{
		Main::getPluginLogger()->debug("Registering Blocks...");
		BlockFactory::registerBlock(new Portal(), true);
		BlockFactory::registerBlock(new EndPortal(), true);
		BlockFactory::registerBlock(new Obsidian(), true);
		BlockFactory::registerBlock(new DragonEgg(), true);
		BlockFactory::registerBlock(new Beacon(), true);
		BlockFactory::registerBlock(new Fire(), true);
		BlockFactory::registerBlock(new Bed(), true);
		BlockFactory::registerBlock(new SlimeBlock(), true);
		BlockFactory::registerBlock(new EndPortalFrame(), true);
		BlockFactory::registerBlock(new Lava(), true);
		BlockFactory::registerBlock(new StillLava(), true);
		BlockFactory::registerBlock(new MonsterSpawner(), true);
		BlockFactory::registerBlock(new FrostedIce(), true);
		BlockFactory::registerBlock(new ShulkerBox(Block::UNDYED_SHULKER_BOX), true);
		BlockFactory::registerBlock(new ShulkerBox(), true);
		BlockFactory::registerBlock(new Hopper(), true);
		BlockFactory::registerBlock(new EnchantingTable(), true);
		BlockFactory::registerBlock(new Anvil(), true);
		BlockFactory::registerBlock(new Pumpkin(), true);
		BlockFactory::registerBlock(new LitPumpkin(), true);
		BlockFactory::registerBlock(new SnowLayer(), true);
		BlockFactory::registerBlock(new BrewingStand(), true);
		BlockFactory::registerBlock(new Rail(), true);
		BlockFactory::registerBlock(new Cauldron(), true);
		//BlockFactory::registerBlock(new Jukebox(), true);
	}
}
