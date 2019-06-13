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

class Dolphin extends WaterAnimal{
    public const NETWORK_ID = self::DOLPHIN;

    public $width = 0.95;
    public $height = 0.95;

    /** @var Vector3 */
    public $swimDirection = null;
    public $swimSpeed = 0.45;

    private $switchDirectionTicker = 0;

    public function initEntity() : void{
        $this->setMaxHealth(35);
        parent::initEntity();
    }

    public function getName() : string{
        return "Dolphin";
    }

    private function generateRandomDirection() : Vector3{
        return new Vector3(mt_rand(-1000, 1000) / 1000, mt_rand(-500, 500) / 1000, mt_rand(-1000, 1000) / 1000);
    }

    public function entityBaseTick(int $tickDiff = 1) : bool{
        if($this->closed){
            return false;
        }

        if(++$this->switchDirectionTicker === 100 or $this->isCollided){
            $this->switchDirectionTicker = 0;
            if(mt_rand(0, 100) < 50){
                $this->swimDirection = null;
            }
        }

        $hasUpdate = parent::entityBaseTick($tickDiff);

        if($this->isAlive()){

            if($this->y > 62 and $this->swimDirection !== null){
                $this->swimDirection->y = -0.5;
            }

            $inWater = $this->isUnderwater();
            if(!$inWater){
                $this->swimDirection = null;
            }elseif($this->swimDirection !== null){
                if($this->motion->lengthSquared() <= $this->swimDirection->lengthSquared()){
                    $this->motion = $this->swimDirection->multiply($this->swimSpeed);
                }
            }else{
                $this->swimDirection = $this->generateRandomDirection();
                $this->swimSpeed = mt_rand(50, 100) / 2000;
            }

            $f = sqrt(($this->motion->x ** 2) + ($this->motion->z ** 2));
            $this->yaw = (-atan2($this->motion->x, $this->motion->z) * 180 / M_PI);
            $this->pitch = (-atan2($f, $this->motion->y) * 180 / M_PI);
        }

        return $hasUpdate;
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