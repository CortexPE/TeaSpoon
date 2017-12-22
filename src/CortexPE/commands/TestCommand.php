<?php

/*
 * JUST FOR TESTING STUFF
 */

declare(strict_types = 1);

namespace CortexPE\commands;

use CortexPE\item\Potion;
use pocketmine\command\{
	CommandSender, defaults\VanillaCommand
};
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\Player;

class TestCommand extends VanillaCommand {

	public function __construct($name){
		parent::__construct(
			$name,
			"CortexPE's Command to test stuff before using it",
			"/test"
		);
	}

	public function execute(CommandSender $sender, $currentAlias, array $args){
		if($sender instanceof Player){
			Potion::registerPotion(101, "LOL", [Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setAmplifier(30)->setDuration(100 * 20), Effect::getEffect(Effect::STRENGTH)->setAmplifier(30)->setDuration(100 * 20)]);
			$sender->getInventory()->addItem(Item::get(Item::POTION, 101, 64));
		}
	}
}
