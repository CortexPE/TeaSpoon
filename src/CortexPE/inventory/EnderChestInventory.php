<?php

/*
 Credits to RealDevs Organization for the nice Method of saving the EnderChest Inventory...
  -- Modded to add more stuff such as Animation and Sound
 Original File URL: https://github.com/RealDevs/TableSpoon/blob/master/src/tablespoon/inventory/EnderChestInventory.php
*/

declare(strict_types = 1);

namespace CortexPE\inventory;

use CortexPE\tile\EnderChest;
use pocketmine\inventory\ContainerInventory;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\{
	NBT, tag\ListTag
};
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnderChestInventory extends ContainerInventory {

	/** @var Player */
	private $user;

	/**
	 * @return EnderChest
	 */
	public function getHolder(){
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->holder;
	}

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

		if(count($this->getViewers()) === 1 and ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), true);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_CHEST_OPEN);
		}
	}

	public function onClose(Player $who) : void{
		if(count($this->getViewers()) === 1 and ($level = $this->getHolder()->getLevel()) instanceof Level){
			$this->broadcastBlockEventPacket($this->getHolder(), false);
			$level->broadcastLevelSoundEvent($this->getHolder()->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
		}
		parent::onClose($who);
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

	protected function broadcastBlockEventPacket(Vector3 $vector, bool $isOpen) : void{
		$pk = new BlockEventPacket();
		$pk->x = (int) $vector->x;
		$pk->y = (int) $vector->y;
		$pk->z = (int) $vector->z;
		$pk->eventType = 1; //it's always 1 for a chest
		$pk->eventData = $isOpen ? 1 : 0;
		$this->getHolder()->getLevel()->addChunkPacket($this->getHolder()->getX() >> 4, $this->getHolder()->getZ() >> 4, $pk);
	}
}
