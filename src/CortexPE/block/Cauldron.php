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

namespace CortexPE\block;

use CortexPE\Main;
use CortexPE\tile\Cauldron as TileCauldron;
use CortexPE\tile\Tile;
use pocketmine\{
  block\Block,
  block\BlockToolType,
  block\Solid,
  entity\Effect,
  event\player\PlayerBucketEmptyEvent,
  event\player\PlayerBucketFillEvent,
  item\Armor,
  item\Item,
  item\Potion,
  item\Tool,
  nbt\tag\ByteTag,
  nbt\tag\CompoundTag,
  nbt\tag\IntTag,
  nbt\tag\ListTag,
  nbt\tag\ShortTag,
  nbt\tag\StringTag,
  Player,
  Server,
  utils\Color
};

class Cauldron extends Solid {
  protected $id = self::CAULDRON_BLOCK;
	
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
  
	public function canBeActivated(): bool{
		return true;
	}
  
	public function getToolType(): int{
		return BlockToolType::TYPE_PICKAXE;
	}
  
	public function getName(): string{
		return "Cauldron";
	}
	
	public function getHardness(){
		return 2;
	}
	
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{
		$this->getLevel()->setBlock($block, $this, true, true);
    
    $nbt = new CompoundTag("", [
			new StringTag("id", Tile::CAULDRON),
			new IntTag("x", $block->x),
			new IntTag("y", $block->y),
			new IntTag("z", $block->z),
			new ShortTag("PotionId", 0xffff),
			new ByteTag("SplashPotion", 0),
      new IntTag("CustomColor", 0)
		]);
    
    if($item->hasCustomName()){
			$nbt->setString("CustomName", $item->getCustomName());
		}
    
		if($item->hasCustomBlockData()){
			foreach($item->getCustomBlockData() as $key => $v){
				$nbt->{$key} = $v;
			}
		}
    
		Tile::createTile(Tile::CAULDRON, $this->getLevel(), $nbt);
    
		return true;
	}

	public function getDrops(Item $item) : array{
		if($item->isPickaxe() >= 1){
			return [Item::get(Item::CAULDRON, 0, 1)];
		}
		return [];
	}

	public function isEmpty() : bool{
		return $this->meta === 0x00;
	}
  
	public function isFull() : bool{
		return $this->meta === 0x06;
	}
  
  public function getColor(int $meta){
		$effect = Effect::getEffect(self::getEffectId($meta));
		if($effect !== null){
			return $effect->getColor();
		}
		return [0, 0, 0];
	}

	public function onActivate(Item $item, Player $player = null) : bool{
    if(Main::$cauldronEnabled){
      $tile = $this->getLevel()->getTile($this);
      if(!($tile instanceof TileCauldron)){
        return false;
      }
      switch($item->getId()){
        case Item::BUCKET:
          if($item->getDamage() === 0){
            if(!$this->isFull() or $tile->isCustomColor() or $tile->hasPotion()){
              break;
            }
            $bucket = clone $item;
            $bucket->setDamage(8);
            Server::getInstance()->getPluginManager()->callEvent($ev = new PlayerBucketFillEvent($player, $this, 0, $item, $bucket));
            if(!$ev->isCancelled()){
              if($player->isSurvival()){
                $player->getInventory()->setItemInHand($ev->getItem());
              }
              $this->meta = 0;
              $this->getLevel()->setBlock($this, $this, true);
              $tile->clearCustomColor();
            }
          }elseif($item->getDamage() === 8){
            if($this->isFull() and !$tile->isCustomColor() and !$tile->hasPotion()){
              break;
            }
            $bucket = clone $item;
            $bucket->setDamage(0);
            Server::getInstance()->getPluginManager()->callEvent($ev = new PlayerBucketEmptyEvent($player, $this, 0, $item, $bucket));
            if(!$ev->isCancelled()){
              if($player->isSurvival()){
                $player->getInventory()->setItemInHand($ev->getItem());
              }
              if($tile->hasPotion()){
                $this->meta = 0;
                $tile->setPotionId(0xffff);
                $tile->setSplashPotion(false);
                $tile->clearCustomColor();
                $this->getLevel()->setBlock($this, $this, true);
              }else{
                $this->meta = 6;
                $tile->clearCustomColor();
                $this->getLevel()->setBlock($this, $this, true);
              }
              $this->onScheduledUpdate();
            }
          }
          break;
        case Item::DYE:
          if($tile->hasPotion()) break;
          $color = Color::getDyeColor($item->getDamage());
          if($tile->isCustomColor()){
            $color = Color::averageColor($color, $tile->getCustomColor());
          }
          if($player->isSurvival()){
            $item->setCount($item->getCount() - 1);
          }
          $tile->setCustomColor($color);
          $this->onScheduledUpdate();
          break;
        case Item::LEATHER_CAP:
        case Item::LEATHER_TUNIC:
        case Item::LEATHER_PANTS:
        case Item::LEATHER_BOOTS:
          if($this->isEmpty()) break;
          if($tile->isCustomColor()){
            --$this->meta;
            $this->getLevel()->setBlock($this, $this, true);
            $newItem = clone $item;
            $newItem->setCustomColor($tile->getCustomColor());
            $player->getInventory()->setItemInHand($newItem);
            if($this->isEmpty()){
              $tile->clearCustomColor();
            }
          }else{
            --$this->meta;
            $this->getLevel()->setBlock($this, $this, true);
            $newItem = clone $item;
            $newItem->clearCustomColor();
            $player->getInventory()->setItemInHand($newItem);
          }
          break;
        case Item::POTION:
        case Item::SPLASH_POTION:
          if(!$this->isEmpty() and (($tile->getPotionId() !== $item->getDamage() and $item->getDamage() !== Potion::WATER) or
              ($item->getId() === Item::POTION and $tile->getSplashPotion()) or
              ($item->getId() === Item::SPLASH_POTION and !$tile->getSplashPotion()) and $item->getDamage() !== 0 or
              ($item->getDamage() === Potion::WATER and $tile->hasPotion()))
          ){
            $this->meta = 0x00;
            $this->getLevel()->setBlock($this, $this, true);
            $tile->setPotionId(0xffff);
            $tile->setSplashPotion(false);
            $tile->clearCustomColor();
            if($player->isSurvival()){
              $player->getInventory()->setItemInHand(Item::get(Item::GLASS_BOTTLE));
            }
          }elseif($item->getDamage() === Potion::WATER){
            $this->meta += 2;
            if($this->meta > 0x06) $this->meta = 0x06;
            $this->getLevel()->setBlock($this, $this, true);
            if($player->isSurvival()){
              $player->getInventory()->setItemInHand(Item::get(Item::GLASS_BOTTLE));
            }
            $tile->setPotionId(0xffff);
            $tile->setSplashPotion(false);
            $tile->clearCustomColor();
          }elseif(!$this->isFull()){
            $this->meta += 2;
            if($this->meta > 0x06) $this->meta = 0x06;
            $tile->setPotionId($item->getDamage());
            $tile->setSplashPotion($item->getId() === Item::SPLASH_POTION);
            $tile->clearCustomColor();
            $this->getLevel()->setBlock($this, $this, true);
            if($player->isSurvival()){
              $player->getInventory()->setItemInHand(Item::get(Item::GLASS_BOTTLE));
            }
            $color = $this->getColor($item->getDamage());
          }
          break;
        case Item::GLASS_BOTTLE:
          if($ev->isCancelled()){
            return false;
          }
          if($this->meta < 2){
            break;
          }
          if($tile->hasPotion()){
            $this->meta -= 2;
            if($tile->getSplashPotion() === true){
              $result = Item::get(Item::SPLASH_POTION, $tile->getPotionId());
            }else{
              $result = Item::get(Item::POTION, $tile->getPotionId());
            }
            if($this->isEmpty()){
              $tile->setPotionId(0xffff);
              $tile->setSplashPotion(false);
              $tile->clearCustomColor();
            }
            $this->getLevel()->setBlock($this, $this, true);
            $this->addItem($item, $player, $result);
            $color = $this->getColor($result->getDamage());
          }else{
            $this->meta -= 2;
            $this->getLevel()->setBlock($this, $this, true);
            if($player->isSurvival()){
              $result = Item::get(Item::POTION, Potion::WATER);
              $this->addItem($item, $player, $result);
            }
          }
          break;
      }
      return true;
    }
    return true;
    }
}
