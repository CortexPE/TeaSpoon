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
 * @author Infernus101
 * @link https://CortexPE.xyz
 *
 */
 
declare(strict_types = 1);

namespace CortexPE\tile;

use CortexPE\Main;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\LongTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\Color;

class Cauldron extends Spawnable {

  public const
      TAG_POTION_ID = "PotionId",
      TAG_SPLASH_POTION = "SplashPotion",
      TAG_CUSTOM_COLOR = "CustomColor";
  
  private $nbt;

	public function __construct(Level $level, CompoundTag $nbt){
    if($nbt->hasTag(self::TAG_POTION_ID, LongTag::class) || $nbt->hasTag(self::TAG_SPLASH_POTION, ByteTag::class) || $nbt->hasTag(self::TAG_CUSTOM_COLOR, IntTag::class)){
		  $nbt->removeTag(self::TAG_POTION_ID);
			$nbt->removeTag(self::TAG_SPLASH_POTION);
			$nbt->removeTag(self::TAG_CUSTOM_COLOR);
    }
    if(!$nbt->hasTag(self::TAG_POTION_ID, LongTag::class)){
      $nbt->setLong(self::TAG_POTION_ID, 0xffff);
		}
    if(!$nbt->hasTag(self::TAG_SPLASH_POTION, ByteTag::class)){
      $nbt->setByte(self::TAG_SPLASH_POTION, 0);
		}
		if(!$nbt->hasTag(self::TAG_CUSTOM_COLOR, IntTag::class)){
      $nbt->setInt(self::TAG_CUSTOM_COLOR, 0);
		}
		parent::__construct($level, $nbt);
	}

	public function getPotionId(){
		return $this->getNBT()->getLong(self::TAG_POTION_ID);
	}
  
  public function getNBT(): CompoundTag{
		return $this->nbt;
	}
	
	public function setPotionId($potionId){
		$this->getNBT()->setLong(self::TAG_POTION_ID, $id);
		$this->onChanged();
    $this->scheduleUpdate();
	}

	public function hasPotion(){
		return $this->getNBT()->getLong(self::TAG_POTION_ID) !== 0xffff;
	}

	public function getSplashPotion(){
		return ($this->getNBT()->getByte(self::TAG_SPLASH_POTION) == true);
	}

	public function setSplashPotion($bool){
    $this->getNBT()->setByte(self::TAG_SPLASH_POTION_ID, ($bool == true) ? 1 : 0);
		$this->onChanged();
    $this->scheduleUpdate();
	}

	public function getCustomColor(){
		if($this->isCustomColor()){
			$color = $this->getNBT()->getInt(self::TAG_CUSTOM_COLOR);
			$green = ($color >> 8) & 0xff;
			$red = ($color >> 16) & 0xff;
			$blue = ($color) & 0xff;
			return Color::getRGB($red, $green, $blue);
		}
		return null;
	}

	public function getCustomColorRed(){
		return ($this->getNBT()->getInt(self::TAG_CUSTOM_COLOR) >> 16) & 0xff;
	}

	public function getCustomColorGreen(){
		return ($this->getNBT()->getInt(self::TAG_CUSTOM_COLOR) >> 8) & 0xff;
	}

	public function getCustomColorBlue(){
		return ($this->getNBT()->getInt(self::TAG_CUSTOM_COLOR)) & 0xff;
	}

	public function isCustomColor(){
		if($this->getNBT()->getInt(self::TAG_CUSTOM_COLOR) == 0)  return false;
    else return true;
	}

	public function setCustomColor($r, $g = 0xff, $b = 0xff){
		if($r instanceof Color){
			$color = ($r->getRed() << 16 | $r->getGreen() << 8 | $r->getBlue()) & 0xffffff;
		}else{
			$color = ($r << 16 | $g << 8 | $b) & 0xffffff;
		}
    $this->getNBT()->setInt(self::TAG_CUSTOM_COLOR, $color);
		$this->onChanged();
    $this->scheduleUpdate();
	}
  
	public function clearCustomColor(){
		if($this->isCustomColor()){
			$this->getNBT()->setInt(self::TAG_CUSTOM_COLOR, 0);
		}
		$this->onChanged();
    $this->scheduleUpdate();
	}
  
  public function addAdditionalSpawnData(CompoundTag $nbt): void{
		$this->baseData($nbt);
	}
  
	private function baseData(CompoundTag $nbt): void{
		$nbt->setLong(self::TAG_POTION_ID, $this->getNBT()->getLong(self::TAG_POTION_ID));
		$nbt->setByte(self::TAG_SPLASH_POTION, $this->getNBT()->getByte(self::TAG_SPLASH_POTION));
		$nbt->setInt(self::TAG_CUSTOM_COLOR, $this->getNBT()->getInt(self::TAG_CUSTOM_COLOR));
	}
  
	protected function readSaveData(CompoundTag $nbt): void{
		$this->nbt = $nbt;
	}
  
	protected function writeSaveData(CompoundTag $nbt): void{
		$this->baseData($nbt);
	}
  
}
