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

namespace CortexPE;

use CortexPE\utils\EntityUtils;
use pocketmine\block\BlockFactory;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\Potion;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Color;

class Utils {

	/** @var bool */
	private static $phared = null;
	/** @var bool */
	private static $serverPhared = null;

	public static function isPhared(): bool{
		if(self::$phared == null){
			self::$phared = strlen(\Phar::running()) > 0 ? true : false;

			return self::$phared;
		}

		return self::$phared;
	}

	public static function isServerPhared(): bool{
		if(self::$serverPhared == null){
			try{
				$ref = new \ReflectionClass(Server::class);
				self::$serverPhared = ((strpos($ref->getFileName(), "phar://") !== false) ? true : false);
			}catch(\Exception $e){
				Main::getInstance()->getLogger()->error($e);
			}

			return self::$serverPhared;
		}

		return self::$serverPhared;
	}

	public static function canSeeSky(Level $lvl, Vector3 $pos){
		return ($lvl->getHighestBlockAt($pos->getFloorX(), $pos->getFloorZ()) <= $pos->getY());
	}

	public static function vector3XZDistance(Vector3 $pos1, Vector3 $pos2){
		return (($pos1->x - $pos2->x) + ($pos1->z - $pos2->z));
	}

	public static function getPotionColor(int $effectID): Color{
		return Potion::getPotionEffectsById($effectID)[0]->getColor();
	}

	public static function toggleBool(bool $boolean): bool{
		/*
		A HISTORY OF HOW I SIMPLIFIED THIS...
		Since: Albert Einstein — 'The definition of genius is taking the complex and making it simple.' LMFAO
		 - Sarcasm -
		https://twitter.com/CortexPE/status/938311444961071104

		// first version
		if($boolean){
			return false;
		}else{
			return true;
		}

		// second version
		if($boolean){
			return false;
		}
		return true;

		// third version
		return $boolean ? false : true;

		// fourth version
		return !$boolean ? true : false;
		*/
		// fifth version
		return !$boolean;
	}

	public static function boolToString(bool $boolean): string{
		return $boolean ? "true" : "false";
	}

	public static function isDelayedTeleportCancellable(Player $player, int $destinationDimension): bool{
		switch($destinationDimension){
			case DimensionIds::NETHER:
				return (!EntityUtils::isInsideOfPortal($player));
			case DimensionIds::THE_END:
				return (!EntityUtils::isInsideOfEndPortal($player));
			case DimensionIds::OVERWORLD:
				return (!EntityUtils::isInsideOfEndPortal($player) && !EntityUtils::isInsideOfPortal($player));
		}

		return false;
	}

	public static function in_arrayi($needle, $haystack){
		return in_array(strtolower($needle), array_map('strtolower', $haystack));
	}

	public static function getDimension(Level $level): int{
		if(Main::$registerDimensions){
			if($level->getName() == Main::$netherLevel->getName()){
				return DimensionIds::NETHER;
			}elseif($level->getName() == Main::$endLevel->getName()){
				return DimensionIds::THE_END;
			}
		}

		return DimensionIds::OVERWORLD;
	}

	public static function solveQuadratic($a, $b, $c): array{
		$x[0] = (-$b + sqrt($b ** 2 - 4 * $a * $c)) / (2 * $a);
		$x[1] = (-$b - sqrt($b ** 2 - 4 * $a * $c)) / (2 * $a);
		if($x[0] == $x[1]){
			return [$x[0]];
		}

		return $x;
	}

	public static function stringToASCIIHex(string $string): string{
		$return = "";
		for($i = 0; $i < strlen($string); $i++){
			$return .= "\x" . bin2hex($string[$i]);
		}

		return $return;
	}

	/**
	 * @param Item $item
	 * @return EnchantmentInstance[]
	 */
	public static function getEnchantments(Item $item): array{
		/** @var EnchantmentInstance[] $enchantments */
		$enchantments = [];

		$ench = $item->getNamedTagEntry(Item::TAG_ENCH);
		if($ench instanceof ListTag){
			/** @var CompoundTag $entry */
			foreach($ench as $entry){
				$id = $entry->getShort("id");
				$lvl = $entry->getShort("lvl");
				if($id > 26 || $lvl <= 0){
					continue;
				}
				$e = Enchantment::getEnchantment($id);
				if($e !== null){
					$enchantments[] = new EnchantmentInstance($e, $lvl);
				}
			}
		}

		return $enchantments;
	}
}
