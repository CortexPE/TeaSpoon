<?php

declare(strict_types=1);

namespace CortexPE\entity;

//use CortexPE\inventory\EnderChestInventory;
//use pocketmine\item\Item;
//use pocketmine\nbt\NBT;
//use pocketmine\nbt\tag\ListTag;

class Human extends \pocketmine\entity\Human {
/*
	private $enderChestInventory;

	public function getEnderChestInventory() : EnderChestInventory {
		return $this->enderChestInventory;
	}

	public function initEntity(){
		parent::initEntity();
		$this->enderChestInventory = new EnderChestInventory($this);
		if(isset($this->namedtag->EnderChestInventory) and $this->namedtag->EnderChestInventory instanceof ListTag){
			foreach($this->namedtag->EnderChestInventory as $i => $item){
				$this->enderChestInventory->setItem($item["Slot"], Item::nbtDeserialize($item));
			}
		}
	}

	public function saveNBT(){
		parent::saveNBT();
		if($this->enderChestInventory !== null){
			if(!$this->namedtag->hasTag("EnderChestInventory", ListTag::class)){
				$this->namedtag->setTag(new ListTag("EnderChestInventory", [], NBT::TAG_Compound));
			}

			$slotCount = $this->enderChestInventory->getSize();
			for($slot = 0; $slot < $slotCount; ++$slot){
				$item = $this->enderChestInventory->getItem($slot);
				if($item->getId() !== Item::AIR){
					$this->namedtag->EnderChestInventory[$slot] = $item->nbtSerialize($slot);
				}
			}
		}
	}

	public function close(){
		parent::close();
		if($this->enderChestInventory !== null){
			$this->enderChestInventory->removeAllViewers(true);
			$this->enderChestInventory = null;
		}
	}*/
}