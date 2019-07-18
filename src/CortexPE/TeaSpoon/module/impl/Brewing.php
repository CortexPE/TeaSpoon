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
 * @author CortexPE, and contributors
 * @link   https://github.com/CortexPE/TeaSpoon
 *
 */

declare(strict_types=1);

namespace CortexPE\TeaSpoon\module\impl;


use CortexPE\TeaSpoon\block\BrewingStand as BrewingStandBlock;
use CortexPE\TeaSpoon\module\ModuleBase;
use CortexPE\TeaSpoon\tile\BrewingStand as BrewingStandTile;
use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\Potion;
use pocketmine\tile\Tile;

class Brewing extends ModuleBase {
	/** @var Item[][] */
	private static $recipes = [];

	public function onLoad(): void {
		parent::onLoad();
		if(!file_exists(($fName = $this->getDataFolder() . "brewing_recipes.json"))) {
			$potions = [
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "NETHER_WART"],
					"result" => ["id" => "POTION", "damage" => Potion::AWKWARD]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::THICK]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "GHAST_TEAR"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "GLISTERING_MELON"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "BLAZE_POWDER"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "MAGMA_CREAM"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "SUGAR"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "RABBIT_FOOT"],
					"result" => ["id" => "POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::MUNDANE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::THICK],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_MUNDANE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_WEAKNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::WEAKNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_WEAKNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GHAST_TEAR"],
					"result" => ["id" => "POTION", "damage" => Potion::REGENERATION]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::REGENERATION],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_REGENERATION]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::REGENERATION],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_REGENERATION]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "BLAZE_POWDER"],
					"result" => ["id" => "POTION", "damage" => Potion::STRENGTH]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::STRENGTH],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_STRENGTH]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::STRENGTH],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_STRENGTH]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::POISON]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_POISON]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_POISON]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GLISTERING_MELON"],
					"result" => ["id" => "POTION", "damage" => Potion::HEALING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::HEALING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_HEALING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "PUFFERFISH"],
					"result" => ["id" => "POTION", "damage" => Potion::WATER_BREATHING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::WATER_BREATHING],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_WATER_BREATHING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::WATER_BREATHING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::HEALING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::HARMING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::STRONG_HEALING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_POISON],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "SUGAR"],
					"result" => ["id" => "POTION", "damage" => Potion::SWIFTNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_SWIFTNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_SWIFTNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "MAGMA_CREAM"],
					"result" => ["id" => "POTION", "damage" => Potion::FIRE_RESISTANCE]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::FIRE_RESISTANCE],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_FIRE_RESISTANCE]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "RABBIT_FOOT"],
					"result" => ["id" => "POTION", "damage" => Potion::LEAPING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_LEAPING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "POTION", "damage" => Potion::STRONG_LEAPING]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::FIRE_RESISTANCE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_FIRE_RESISTANCE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_LEAPING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_SWIFTNESS],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::SLOWNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GOLDEN_CARROT"],
					"result" => ["id" => "POTION", "damage" => Potion::NIGHT_VISION]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::NIGHT_VISION],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_NIGHT_VISION]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::NIGHT_VISION],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::INVISIBILITY]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::INVISIBILITY],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_INVISIBILITY]
				],
				[
					"potion" => ["id" => "POTION", "damage" => Potion::LONG_NIGHT_VISION],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "POTION", "damage" => Potion::LONG_INVISIBILITY]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "NETHER_WART"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::THICK]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "GHAST_TEAR"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "GLISTERING_MELON"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "BLAZE_POWDER"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "MAGMA_CREAM"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "SUGAR"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "RABBIT_FOOT"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::MUNDANE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::THICK],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::WEAKNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_MUNDANE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_WEAKNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::WEAKNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_WEAKNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GHAST_TEAR"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::REGENERATION]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::REGENERATION],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_REGENERATION]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::REGENERATION],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_REGENERATION]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "BLAZE_POWDER"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRENGTH]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::STRENGTH],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_STRENGTH]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::STRENGTH],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_STRENGTH]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::POISON]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_POISON]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_POISON]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GLISTERING_MELON"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::HEALING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::HEALING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_HEALING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "PUFFERFISH"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::WATER_BREATHING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::WATER_BREATHING],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_WATER_BREATHING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::WATER_BREATHING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::HEALING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::POISON],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::HARMING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_HEALING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_POISON],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_HARMING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "SUGAR"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::SWIFTNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SWIFTNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_SWIFTNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "MAGMA_CREAM"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::FIRE_RESISTANCE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::FIRE_RESISTANCE],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_FIRE_RESISTANCE]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "RABBIT_FOOT"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LEAPING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_LEAPING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "GLOWSTONE_DUST"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::STRONG_LEAPING]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::FIRE_RESISTANCE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::SWIFTNESS],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LEAPING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_FIRE_RESISTANCE],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_LEAPING],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SWIFTNESS],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::SLOWNESS],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_SLOWNESS]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::AWKWARD],
					"ingredient" => ["id" => "GOLDEN_CARROT"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::NIGHT_VISION]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::NIGHT_VISION],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_NIGHT_VISION]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::NIGHT_VISION],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::INVISIBILITY]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::INVISIBILITY],
					"ingredient" => ["id" => "REDSTONE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_INVISIBILITY]
				],
				[
					"potion" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_NIGHT_VISION],
					"ingredient" => ["id" => "FERMENTED_SPIDER_EYE"],
					"result" => ["id" => "SPLASH_POTION", "damage" => Potion::LONG_INVISIBILITY]
				],
				[
					"potion" => ["id" => "POTION"],
					"ingredient" => ["id" => "GUNPOWDER"],
					"result" => ["id" => "SPLASH_POTION"]
				],
				[
					"potion" => ["id" => "SPLASH_POTION"],
					"ingredient" => ["id" => "DRAGON_BREATH"],
					"result" => ["id" => "LINGERING_POTION"]
				]
			];
			$ref = new \ReflectionClass(Potion::class);
			$refPots = array_diff_assoc($ref->getConstants(), $ref->getParentClass()->getConstants());
			foreach($refPots as $potion) {
				$potions[] = [
					"potion" => ["id" => "POTION", "damage" => $potion],
					"ingredient" => ["id" => "GUNPOWDER"],
					"result" => ["id" => "SPLASH_POTION", "damage" => $potion]
				];
				$potions[] = [
					"potion" => ["id" => "SPLASH_POTION", "damage" => $potion],
					"ingredient" => ["id" => "DRAGON_BREATH"],
					"result" => ["id" => "LINGERING_POTION", "damage" => $potion]
				];
			}
			file_put_contents($fName, json_encode($potions));
		} else {
			$potions = json_decode(file_get_contents($fName), true);
		}
		foreach($potions as $recipeData) {
			self::$recipes[] = [
				"potion" => self::decodeRecipeItem($recipeData["potion"]),
				"ingredient" => self::decodeRecipeItem($recipeData["ingredient"]),
				"result" => self::decodeRecipeItem($recipeData["result"]),
			];
		}
	}

	private static function decodeRecipeItem(array $itemData):Item {
		$itemData["id"] = ItemFactory::fromString($itemData["id"])->getId();
		return Item::jsonDeserialize($itemData);
	}

	public static function resolveResult(Item $ingredient, Item $potion): ?Item {
		foreach(self::$recipes as $recipe) {
			if($recipe["potion"]->equals($potion) && $recipe["ingredient"]->equals($ingredient)) {
				return clone $recipe["result"];
			}
		}

		return null;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function onInitialize(): void {
		parent::onInitialize();
		BlockFactory::registerBlock(new BrewingStandBlock(), true);
		Tile::registerTile(BrewingStandTile::class, [Tile::BREWING_STAND, "minecraft:brewing_stand"]);
	}
}