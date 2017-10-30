<?php

namespace CortexPE\inventory;

use pocketmine\inventory\ContainerInventory;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnderChestInventory extends ContainerInventory {

	/** @var Player */
	private $user;

	public function onOpen(Player $player): void{
		$this->user = $player;
		if(isset($player->namedtag->EnderChestInventory) && $player->namedtag->EnderChestInventory instanceof ListTag){
			foreach($player->namedtag->EnderChestInventory as $slot => $itemNBT){
				$this->slots[$slot] = Item::nbtDeserialize($itemNBT);
			}
		}else{
			$player->namedtag->EnderChestInventory = new ListTag("EnderChestInventory", array_fill(0, 27, Item::get(Item::AIR, 0, 0)->nbtSerialize()));
			$player->namedtag->EnderChestInventory->setTagType(NBT::TAG_Compound);
		}
		parent::onOpen($player);
	}

	public function setItem(int $index, Item $item, bool $send = true): bool{
		if(parent::setItem($index, $item, $send)){
			if($this->user !== null){
				$this->user->namedtag->EnderChestInventory->{$index} = $item->nbtSerialize($index);
			}

			//TODO: Send debug message
			return true;
		}

		return false;
	}

	public function getNetworkType(): int{
		return WindowTypes::CONTAINER;
	}

	public function getName(): string{
		return "EnderChest";
	}

	public function getDefaultSize(): int{
		return 27;
	}
}