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

use CortexPE\Utils;
use pocketmine\block\BlockFactory;

class BlockManager {

	public static function register(int $id, Block $block, bool $overwrite = false): bool{
		if(!BlockFactory::isRegistered($id) && !$overwrite){
			BlockFactory::registerBlock($block);
    }
  }
	public static function init(): void{
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
		BlockFactory::registerBlock(new ShulkerBox(), true);
		BlockFactory::registerBlock(new Hopper(), true);
		BlockFactory::registerBlock(new EnchantingTable(), true);
		BlockFactory::registerBlock(new Anvil(), true);
    		// Redstone
		self::register(Block::ACTIVATOR_RAIL, new redstone\ActivatorRail(), true);
		self::register(Block::LIT_REDSTONE_LAMP, new redstone\ActiveRedstoneLamp(), true);
		self::register(Block::DAYLIGHT_DETECTOR, new redstone\DaylightDetector(), true);
		self::register(Block::DAYLIGHT_DETECTOR_INVERTED, new redstone\DaylightDetectorInverted(), true);
		self::register(Block::DETECTOR_RAIL, new redstone\DetectorRail(), true);
		self::register(Block::DISPENSER, new redstone\Dispenser(), true);
		// TODO: DOORS
		self::register(Block::DROPPER, new redstone\Dropper(), true);
		// TODO: FENCEGATES
		self::register(Block::HEAVY_WEIGHTED_PRESSURE_PLATE, new redstone\HeavyWeightedPressurePlate(), true);
		self::register(Block::REDSTONE_LAMP, new redstone\InactiveRedstoneLamp(), true);
		self::register(Block::LEVER, new Lever(), true);
		self::register(Block::LIGHT_WEIGHTED_PRESSURE_PLATE, new redstone\LightWeightedPressurePlate(), true);
		self::register(Block::LIT_REDSTONE_LAMP, new redstone\LitRedstoneLamp(), true);
		// TODO: NOTEBLOCKS self::register(Block::NOTE_BLOCK, new NoteBlock(), true);
		self::register(Block::POWERED_RAIL, new redstone\PoweredRail(), true);
		self::register(Block::POWERED_REPEATER, new redstone\PoweredRepeater(), true);
		self::register(Block::STONE_PRESSURE_PLATE, new redstone\PressurePlate(), true);
		self::register(Block::RAIL, new redstone\Rail(), true);
		self::register(Block::REDSTONE_BLOCK, new redstone\Redstone(), true);
		self::register(Block::REDSTONE_TORCH, new redstone\RedstoneTorch(), true);
		self::register(Block::REDSTONE_WIRE, new redstone\RedstoneWire(), true);
		self::register(Block::STONE_BUTTON, new redstone\StoneButton(), true);
		// TODO: TNT self::register(Block::TNT, new StoneButton(), true);
		self::register(Block::TRAPDOOR, new redstone\Trapdoor(), true);
		self::register(Block::TRAPPED_CHEST, new redstone\TrappedChest(), true);
		self::register(Block::TRIPWIRE, new redstone\Tripwire(), true);
		self::register(Block::TRIPWIRE_HOOK, new redstone\TripwireHook(), true);
		self::register(Block::WOODEN_BUTTON, new redstone\WoodenButton(), true);
		self::register(Block::WOODEN_PRESSURE_PLATE, new redstone\WoodenPressurePlate(), true);

		if(!Utils::isPhared()){ // beta
			BlockFactory::registerBlock(new Jukebox(), true);
		}
	}
}
