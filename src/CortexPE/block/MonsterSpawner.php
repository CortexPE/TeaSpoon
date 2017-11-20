<?php
namespace CortexPE\block;

use pocketmine\Player;

use pocketmine\item\Item;
use pocketmine\item\Tool;

use pocketmine\math\Vector3;

use pocketmine\block\Block;
use pocketmine\block\Transparent;

use CortexPE\Main as Loader;
use CortexPE\tile\MobSpawner;

use pocketmine\tile\Tile;

class MonsterSpawner extends Transparent{

	protected $id = self::MONSTER_SPAWNER;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function getHardness() : float{
		return 5;
	}

	public function getToolType() : Int{
		return Tool::TYPE_PICKAXE;
	}

	public function getName() : string{
		return "Monster Spawner";
	}

	public function getDrops(Item $item) : array{
		 $return = [];
		 if(Loader::get("silk.enabled") and $item->hasEnchantment(16)){
	      $item = Item::get(self::$id, $this->meta, 1);
	      $item->setCustomName($item->getName()."\n§r§7EntityId: ".$this->meta);
	    }
	return $return;
	}
	
	public function place(Item $item, Block $blockReplace, Block $blockClicked, Int $face, Vector3 $clickVector, Player $player = null) : bool{
		 $this->getLevel()->setBlock($blockReplace, $this, true, true);
		 $tile = Tile::createTile("MobSpawner", $this->getLevel(), MobSpawner::createNBT($blockReplace, $face, $item, $player));
		 $tile->setEntityEid($item->getDamage());
   return true;
	}
	
	public function onActivate(Item $item, Player $player = null) : bool{
		  if($item->getId() == Item::SPAWN_EGG){
			 if(($tile = $this->getLevel()->getTile($this)) instanceof MobSpawner){
				$tile->setEntityEid($item->getDamage());
			   if($player !== null and $player->isSurvival()){
				  $item->setCount(1);
				  $player->getInventory()->removeItem(1);
				}
			 }else{
				$tile = Tile::createTile("MobSpawner", $this->getLevel(), MobSpawner::createNBT($this));
			   $tile->setEntityEid($item->getDamage());
			   if($player !== null and $player->isSurvival()){
				  $item->setCount(1);
				  $player->getInventory()->removeItem(1);
				}
			 }
			 $this->meta = $item->getDamage();
		  }
	return true;
	}
}