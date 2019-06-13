<?php
/**
 *
 *     ███╗   ███╗ █████╗ ███╗   ██╗██╗   ██╗ ██╗██████╗ ██████╗ ███████╗
 *    ████╗ ████║██╔══██╗████╗  ██║╚██╗ ██╔╝███║╚════██╗╚════██╗╚════██║
 *   ██╔████╔██║███████║██╔██╗ ██║ ╚████╔╝ ╚██║ █████╔╝ █████╔╝    ██╔╝
 *  ██║╚██╔╝██║██╔══██║██║╚██╗██║  ╚██╔╝   ██║ ╚═══██╗ ╚═══██╗   ██╔╝
 * ██║ ╚═╝ ██║██║  ██║██║ ╚████║   ██║    ██║██████╔╝██████╔╝   ██║
 *╚═╝     ╚═╝╚═╝  ╚═╝╚═╝  ╚═══╝   ╚═╝    ╚═╝╚═════╝ ╚═════╝    ╚═╝
 *
 * @author many1337
 * @link https://github.com/many1337
 *
 */

declare(strict_types = 1);

namespace CortexPE\entity\animal;

use pocketmine\entity\Animal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\EntityEventPacket;

class Panda extends Animal{
    public const NETWORK_ID = self::PANDA;

    public $width = 1.85;
    public $height = 1.45;

    const BROWN = 0;
    const LAZY = 1;
    const WORRIED = 2;
    const PLAYFUL = 3;
    const WEAK = 4;
    const AGGRESSIVE = 5;
    const NORMAL = 6;

    public function getName() : string{
        return "Panda";
    }

    public function getDrops() : array{
        $drops = [
            ItemFactory::get(Item::BAMBOO, 0, mt_rand(0, 3))
        ];
        return $drops;
    }

    public function initEntity() : void{
        $this->setMaxHealth(25);
        $this->propertyManager->setInt(self::DATA_VARIANT, rand(0, 6));
        parent::initEntity();
    }

    public function getXpDropAmount() : int{
        return 5;
    }
}