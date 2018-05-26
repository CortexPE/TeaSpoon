<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\Player;
use pocketmine\tile\Tile;

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof Player && $sender->isOp()){
			$block = $sender->getTargetBlock(10);
			$tile = $sender->getLevel()->getTile($block);
			$sender->sendMessage("Block: " . get_class($block));
			$sender->sendMessage("HeldItem: " . get_class($sender->getInventory()->getItemInHand()));
			$sender->sendMessage("Tile: " . ($tile instanceof Tile ? get_class($tile) : "null"));
			$sender->sendMessage("Chunk is loaded: " . ($sender->getLevel()->isChunkLoaded($sender->getFloorX() >> 4, $sender->getFloorZ() >> 4) ? "TRUE" : "FALSE"));
			$sender->sendMessage("Pos: " . $sender->asVector3()->__toString());
			if(isset($args[0])){
				switch($args[0]){
					case "duplicate":
						$sender->getInventory()->addItem($sender->getInventory()->getItemInHand());
						break;
					case "decodepk":
						if(isset($args[1])){
							print_r(PacketPool::getPacket(hex2bin($args[1])));
						}
						break;
				}
			}
		}
	}
}
