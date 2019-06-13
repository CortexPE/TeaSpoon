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

class CodFish extends WaterAnimal{
    public const NETWORK_ID = self::COD;

    public $width = 0.4;
    public $height = 0.8;

    /** @var Vector3 */
    public $swimDirection = null;
    public $swimSpeed = 0.35;

    private $switchDirectionTicker = 0;

    public function initEntity() : void{
        $this->setMaxHealth(7);
        parent::initEntity();
    }

    public function getName() : string{
        return "Cod";
    }

    protected function applyGravity() : void{
        if(!$this->isUnderwater()){
            parent::applyGravity();
        }
    }


    public function getDrops() : array{
        return [
            ItemFactory::get(Item::FISH, 0, mt_rand(1, 3))
        ];
    }
}