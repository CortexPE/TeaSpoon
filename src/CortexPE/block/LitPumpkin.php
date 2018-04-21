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

/*
     (      (
     )\ )   )\ )    *   )
    (()/(  (()/(  ` )  /(
     /(_))  /(_))  ( )(_))
    (_))   (_))   (_(_())
    | |    |_ _|  |_   _|
    | |__   | |     | |
    |____| |___|    |_|

                            ``--://///////:--``                            
                       .:+osyyyhhhhhhhhhhhhhyyyso+:.                       
                   -/oyyhhhhhhhdddddddddddddhhhhhhhyyo/-                   
                -+syhhhhdddmmNNNNNMMMMMMMMNNNNmmdddhhhhys+-                
             ./syhhhhddmNNNMMMMMMMMMMMMMMMMMMMMMNNNmddhhhhys/.             
           .+yhhhhddmNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNmddhhhhy+.           
         .+yhhhddmNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNmddhhhy+.         
       `/yyhhddmNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNmddhhhs/`       
      .oyhhhddmNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNmdddhhyo.      
     -syhhdddmNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNNmdddhhys-     
    :yhhhdddmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmdddhhhy:    
   -yhhhdddmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmdddhhhy-   
  `syhhddddmmmmmmdhyyyhmmmmmmmmmmmmmmmmmmmmmmmmmmmmmdyyhhdmmmmmmmdddhhys`  
  +yhhdddddmmmhs/:--/+hmmmmmmmmmmmmmmmmmmmmmmmmmmmmmmo/--:/shdmmmddddhhy+  
 -yhhdddddddh+:--:ohdddddddddddddddddddddddddddddddddmdhs:--:/ydddddddhhy- 
 +yhhddddddy:--/shddddddddddddddddddddddddddddddddddddddmdy/---oddddddhhy+ 
`syhhdddddy--:sdddhyysssossyhdddddddddddddddddhhyssooosyhdddy/--+dddddhhys`
.yhhdddddd--oddyo/::::::::-::/oydddddddddddho/:::::::::-:/+yddy:.sdddddhhy.
-yhhdddddd+ydh/-----:/+++////:--+ddddddddd/:-:://+++++/-----/ddhosdddddhhy-
-yhhdddddddhyo-:::--+ddddddhhhysydddddddddoosyhhddddddh:-:::-+shdddddddhhy-
.yhhdddddyo+//+sy+:-sdddddddddddddddddddddddddddddddddd/:/syo///+yhddddhhy.
`syhhdhs++osyhmNm+/-ydddddddddddddddddddddddddddddddddh/:/yNNdyso++ohddhys`
 +yyso+oshdmmmNNh+/.-:/++ossyyyhhhhhhhhhhhhyyyysoo+/::-./+sNNNmmdhso++shy+ 
 -o+osshdmmmmmmmyo//+/::----...----------........----:///osmmmmmmmdhss++o- 
 ./ssyydmddddmmmyo:ymNNNNmmmddddhhhhhhhhhhhhhhddddmmmmmd/osmmmddddmdyyso/` 
`:osyyhhmmmmmmmhs+:ydmNNNNNNMMMMMMMMMMMMMMMMMMMNNNNNNmds:+shmmmmmmmdhyyso-`
`:oyyyhhhddddhhyo:--:/+syhdmmNNNNNNNMNNNNNNNNmmmdhyo/:-::/syhdddddhhhhyyo:`
`.+syyhhhhhhhyyo//+oo++//::::://////////////:::::://++ooo++syyhhhhhhhyys+.`
 `-/osyyyyyyys++yhysooooooooooooooooooooooooooooooooooosydy+osyyyyyyyys+-` 
   `.:+ooooo++shdddhysooooooooooooooooooooooooooooooosyhdddho++ossso+/-``  
      ``./ooyhdddddddhysoooooooooooooooooooooooooossyddddddddhso+/.```     
         `+yhhhdddddddddhyssoooooooooooooooooossyhhdddddddddhhhy+`         
           .+yhhhdddddddddddhyysssssooosssssyhhdddddddddddhhhy+.           
             ./syhhhdddddddddddddddhhhhhdddddddddddddddhhhys/.             
                -+syhhhhdddddddddddddddddddddddddddhhhhys+-                
                   -/oyyhhhhhhdddddddddddddddhhhhhhyyo/-                   
                       .:+osyyyhhhhhhhhhhhhhyyyso+:.                       
                            ``--://///////:--``                            
 */

namespace CortexPE\block;

use CortexPE\entity\mob\IronGolem;
use CortexPE\entity\mob\SnowGolem;
use CortexPE\Main;
use CortexPE\utils\EntityUtils;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\LitPumpkin as PMLitPumpkin;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LitPumpkin extends PMLitPumpkin {
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = \null): bool{
		$parent = parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
		if($player instanceof Player){
			$level = $this->getLevel();
			if(Main::$enableSnowGolemStructures){
				if(EntityUtils::checkSnowGolemStructure($this)[0]){
					$level->setBlock($this, new Air());
					$level->setBlock($this->subtract(0, 1), new Air());
					$level->setBlock($this->subtract(0, 2), new Air());
					$golem = Entity::createEntity(Entity::SNOW_GOLEM, $level, Entity::createBaseNBT($this));
					if($golem instanceof SnowGolem){
						$golem->spawnToAll();
					}
				}
			}
			if(Main::$enableIronGolemStructures){
				$check = EntityUtils::checkIronGolemStructure($this);
				if($check[0]){
					switch($check[1]){
						case "X":
							$level->setBlock($this->subtract(1, 1, 0), new Air());
							$level->setBlock($this->add(1, -1, 0), new Air());
							break;
						case "Z":
							$level->setBlock($this->subtract(0, 1, 1), new Air());
							$level->setBlock($this->add(0, -1, 1), new Air());
							break;
					}
					$level->setBlock($this, new Air());
					$level->setBlock($this->subtract(0, 1), new Air());
					$level->setBlock($this->subtract(0, 2), new Air());

					$golem = Entity::createEntity(Entity::IRON_GOLEM, $level, Entity::createBaseNBT($this));
					if($golem instanceof IronGolem){
						$golem->spawnToAll();
					}
				}
			}
		}

		return $parent;
	}
}