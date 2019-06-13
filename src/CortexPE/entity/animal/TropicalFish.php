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

declare(strict_types=1);

namespace CortexPE\entity\animal;

use pocketmine\entity\WaterAnimal;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\EntityEventPacket;

class TropicalFish extends WaterAnimal{
    public const NETWORK_ID = self::TROPICAL_FISH;

    public $width = 0.5;
    public $height = 0.4;

    /** @var Vector3 */
    public $swimDirection = null;
    public $swimSpeed = 0.35;

    private $switchDirectionTicker = 0;

    public function initEntity() : void{
        $this->setMaxHealth(7);
        $this->propertyManager->setInt(self::DATA_VARIANT, rand(0, 235340288));
        parent::initEntity();
    }

    public function getName() : string{
        return "Tropical Fish";
    }

    protected function applyGravity() : void{
        if(!$this->isUnderwater()){
            parent::applyGravity();
        }
    }

    public function getDrops() : array{
        $chance = mt_rand(0, 2);
        switch($chance){
            case 0:
                return [
                    ItemFactory::get(Item::TROPICAL_FISH, 0, 1)
                ];
            case 1:
                return [
                    ItemFactory::get(Item::BONE, 0, mt_rand(0, 2))
                ];
            case 2:
                return [
                    ItemFactory::get(Item::FISH, 0, mt_rand(1, 2))
                ];
        }
    }
}