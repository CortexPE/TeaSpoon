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

use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\block\Block;
use pocketmine\level\Position;

class Sponge extends Transparent{

    protected $id = self::SPONGE;

    public function __construct(){
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
        if($this->getDamage() == 0){
            if(self::absorbWater(new Position($this->x, $this->y, $this->z, $this->getLevel()))){
                return $this->getLevel()->setBlock($this, Block::get(Block::SPONGE, 1), true, true);
            }else{
                return $this->getLevel()->setBlock($this, $this, true, true);
            }
        }else{
            return $this->getLevel()->setBlock($this, $this, true, true);
        }
    }

    public function getName() : string{
        return "Sponge";
    }
    private function absorbWater(Position $center){
        $world = $center->getLevel();
        $waterRemoved = 0;
        $yBlock = $center->getY();
        $zBlock = $center->getZ();
        $xBlock = $center->getX();
        $radius = 5;
        $l = false;
        $touchingWater = false;
        for($x = -1; $x <= 1; ++$x){
            for($y = -1; $y <= 1; ++$y){
                for($z = -1; $z <= 1; ++$z){
                    $block = $world->getBlockAt($xBlock + $x, $yBlock + $y, $zBlock + $z);
                    if($block->getId() == 9 || $block->getId() == 8){
                        $touchingWater = true;
                    }
                }
            }
        }
        if($touchingWater){
            for ($x = $center->getX()-$radius; $x <= $center->getX()+$radius; $x++) {
                $xsqr = ($center->getX()-$x) * ($center->getX()-$x);
                for ($y = $center->getY()-$radius; $y <= $center->getY()+$radius; $y++) {
                    $ysqr = ($center->getY()-$y) * ($center->getY()-$y);
                    for ($z = $center->getZ()-$radius; $z <= $center->getZ()+$radius; $z++) {
                        $zsqr = ($center->getZ()-$z) * ($center->getZ()-$z);
                        if(($xsqr + $ysqr + $zsqr) <= ($radius*$radius)) {
                            if($y > 0) {
                                $level = $center->getLevel();
                                if($level->getBlockAt($x,$y,$z)->getId() == 9 || $level->getBlockAt($x,$y,$z)->getId() == 8){
                                    $l = true;
                                    $level->setBlock(new Vector3($x, $y, $z), Block::get(0,0));
                                }

                            }
                        }
                    }
                }
            }
        }
        return $l;
    }
}
