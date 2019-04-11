<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use CortexPE\item\EnchantedBook;
use CortexPE\tile\Beacon;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\entity\Effect;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
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
					case "beacon_effect":
						foreach($sender->getLevel()->getTiles() as $tile){
							if($tile instanceof Beacon){
								$tile->setPrimaryEffect(Effect::JUMP);
								$tile->setPrimaryEffect(Effect::RESISTANCE);
							}
						}
						break;
					case "nofadetitle":
						$sender->addTitle("TITLE", "SUBTITLE", 0, -1, 0);
						break;
					case "actionbar":
						$sender->addActionBarMessage("ACTIONBAR");
						break;
					case "opEnchBook":
						$i = ItemFactory::get(Item::ENCHANTED_BOOK);
						if($i instanceof EnchantedBook){
							foreach([
								new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 32767),
								new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::KNOCKBACK), 32767),
								new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 32767),
								new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::LOOTING), 32767),
							] as $enchantment){
								$i->addEnchantment($enchantment);
							}
							$sender->getInventory()->addItem($i);
						}
						break;
					case "skin2b64":
						$data = '';
						$image = imagecreatefrompng("skin.png");
						for($y = 0, $height = imagesy($image); $y < $height; $y++){
							for($x = 0, $width = imagesx($image); $x < $width; $x++){
								$color = imagecolorat($image, $x, $y);
								$data .= pack("c", ($color >> 16) & 0xFF) //red
									. pack("c", ($color >> 8) & 0xFF) //green
									. pack("c", $color & 0xFF) //blue
									. pack("c", 255 - (($color & 0x7F000000) >> 23)); //alpha
							}
						}
						file_put_contents("skin.txt", base64_encode(zlib_encode($data, ZLIB_ENCODING_GZIP)));
						break;
				}
			}
		}
	}
}
