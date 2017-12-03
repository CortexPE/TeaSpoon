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

use CortexPE\block\{
	EndPortal, Portal
};
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\item\ItemFactory;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\DimensionIds;
use pocketmine\Player as PMPlayer;

class Utils {

	/** @var bool */
	private static $phared = null;

	public static function isPhared() : bool {
		if(self::$phared == null){
			self::$phared = strlen(\Phar::running()) > 0 ? true : false;
			return self::$phared;
		} else {
			return self::$phared;
		}
	}

	public static function canBlockSeeSky(Level $lvl, Block $block){
		return ($lvl->getHighestBlockAt($block->getFloorX(), $block->getFloorZ()) <= $block->getY());
	}

	public static function checkSpoon(){
		return (
			Server::getInstance()->getName() !== "PocketMine-MP" ||
			!class_exists(BlockFactory::class) ||
			!class_exists(ItemFactory::class) ||
			class_exists("pocketmine\\network\\protocol\\Info")
		);
	}

	public static function toggleBool(bool $boolean): bool{
		if($boolean){
			return false;
		}else{
			return true;
		}
	}

	public static function boolToString(bool $boolean): string{
		if($boolean){
			return "true";
		}else{
			return "false";
		}
	}

	public static function isDelayedTeleportCancellable(PMPlayer $player): bool{
		if(self::isInsideOfEndPortal($player) === false && self::isInsideOfPortal($player) === false){
			return true;
		}

		return false;
	}

	public static function isInsideOfEndPortal(Entity $entity): bool{
		$block = $entity->getLevel()->getBlock($entity);
		if($block instanceof EndPortal){
			return true;
		}

		return false;
	}

	public static function isInsideOfPortal(Entity $entity): bool{
		$block = $entity->getLevel()->getBlock($entity);
		if($block instanceof Portal){
			return true;
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

	public static function getLevelFromXp(int $xp): array{
		$xp &= 0x7fffffff;

		/** These values are correct up to and including level 16 */
		$a = 1;
		$b = 6;
		$c = -$xp;
		if($xp > self::getTotalXpRequirement(16)){
			/** Modify the coefficients to fit the relevant equation */
			if($xp <= self::getTotalXpRequirement(31)){
				/** Levels 16-31 */
				$a = 2.5;
				$b = -40.5;
				$c += 360;
			}else{
				/** Level 32+ */
				$a = 4.5;
				$b = -162.5;
				$c += 2220;
			}
		}

		$answer = max(self::solveQuadratic($a, $b, $c)); //Use largest result value
		$level = floor($answer);
		$progress = $answer - $level;

		return [$level, $progress];
	}

	public static function getTotalXpRequirement(int $level): int{
		if($level <= 16){
			return ($level ** 2) + (6 * $level);
		}elseif($level <= 31){
			return (2.5 * ($level ** 2)) - (40.5 * $level) + 360;
		}elseif($level <= 21863){
			return (4.5 * ($level ** 2)) - (162.5 * $level) + 2220;
		}

		return PHP_INT_MAX; //prevent float returns for invalid levels on 32-bit systems
	}

	public static function solveQuadratic($a, $b, $c): array{
		$x[0] = (-$b + sqrt($b ** 2 - 4 * $a * $c)) / (2 * $a);
		$x[1] = (-$b - sqrt($b ** 2 - 4 * $a * $c)) / (2 * $a);
		if($x[0] == $x[1]){
			return [$x[0]];
		}

		return $x;
	}

	public static function getDirectionString(int $direction) : string {
		switch($direction){
			case 0:
				return "south";

			case 1:
				return "west";

			case 2:
				return "north";

			case 3:
				return "east";
		}
		return "invalid";
	}

	public static function stringToASCIIHex(string $string) : string {
		$return = "";
		for ($i = 0; $i < strlen($string); $i++) {
			$return .= "\x".str_pad(dechex(ord($string[$i])), 2, '0', STR_PAD_LEFT);
		}
		return $return;
	}
}