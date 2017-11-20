<?php
namespace CortexPE\tile;

use pocketmine\Player;
use pocketmine\Server;

use pocketmine\tile\Tile;
use pocketmine\tile\Spawnable;

use pocketmine\entity\Entity;

use pocketmine\level\Level;
use pocketmine\level\Position;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

use CortexPE\Main as Loader;

/** code by TheAz928 */

class MobSpawner extends Spawnable{
	
	const TAG_DATA = "SpawnData";
	
	/**
	 * @var Int
	 */
	private $minSpawnDelay = 0;
	
	/**
	 * @var Int
	 */
	private $maxSpawnDelay = 0;
	
	/**
	 * @var Int
	 * Aka EntityId
	 */
	private $eid = 0;
	
	/**
	 * @var string
	 */
	private $entity = "";
	
	/**
	 * @var Int
	 */
	private $delay = 0;
	
	/**
	 * @var Int[]
	 */
	private $spawnRange = [
	     "player" => 5,
	     "range" => 4
	];
	
	/**
	 * @var Int
	 */
	private $spawnLimit = 5;
	
	public function __construct(Level $level, CompoundTag $nbt){
	    if(!isset($nbt->SpawnData) or $nbt->SpawnData instanceof CompoundTag == false){
	      $nbt->SpawnData = new CompoundTag(self::TAG_DATA, []);
	    }
	    if(!isset($nbt->SpawnData->SpawnCount) or $nbt->SpawnData->SpawnCount instanceof ShortTag == false){
	      $nbt->SpawnData->SpawnCount = new ShortTag("SpawnCount", Loader::get("spawn.limit", 5));
	    }
	    if(!isset($nbt->SpawnData->SpawnRange) or $nbt->SpawnData->SpawnRange instanceof ShortTag == false){
	      $nbt->SpawnData->SpawnRange = new ShortTag("SpawnRange", Loader::get("range", ["spawn.range" => 5])["spawn.range"]);
	    }
	    if(!isset($nbt->SpawnData->MinSpawnDelay) or $nbt->SpawnData->MinSpawnDelay instanceof ShortTag == false){
	      $nbt->SpawnData->MinSpawnDelay = new ShortTag("MinSpawnDelay", Loader::get("delay", ["min" => 200])["min"]);
	    }
	    if(!isset($nbt->SpawnData->MaxSpawnDelay) or $nbt->SpawnData->MaxSpawnDelay instanceof ShortTag == false){
	      $nbt->SpawnData->MaxSpawnDelay = new ShortTag("MaxSpawnDelay", Loader::get("delay", ["max" => 80000])["max"]);
	    }
	    if(!isset($nbt->SpawnData->Delay) or $nbt->SpawnData->Delay instanceof ShortTag == false){
	      $nbt->SpawnData->Delay = new ShortTag("Delay", 0);
	    }
	    if(!isset($nbt->SpawnData->id) or $nbt->SpawnData->id instanceof StringTag == false){
	      $nbt->SpawnData->id = new StringTag("id", 0);
	    }
	    if(!isset($nbt->SpawnData->RequiredPlayerRange) or $nbt->SpawnData->RequiredPlayerRange instanceof ShortTag == false){
	      $nbt->SpawnData->RequiredPlayerRange = new ShortTag("RequiredPlayerRange", Loader::get("range", ["player.range" => 5])["player.range"]);
	    }
	    parent::__construct($level, $nbt);
	    $this->delay = $nbt->SpawnData->Delay->getValue();
	    $this->minSpawnDelay = $nbt->SpawnData->MinSpawnDelay->getValue();
       $this->maxSpawnDelay = $nbt->SpawnData->MaxSpawnDelay->getValue();
	    $this->entity = $nbt->SpawnData->id->getValue();
	    $this->spawnLimit = $nbt->SpawnData->SpawnCount->getValue();
	    $this->spawnRange = [
	      "player" => $nbt->SpawnData->RequiredPlayerRange->getValue(),
	      "range" => $nbt->SpawnData->SpawnRange->getValue()
	    ];
      $this->setEntityEid($this->configureEntityValue($this->entity, false));
      
	}
	
	/**
	 * @param string $name
	 * @param bool $asString
	 * @return Int|string
	 */
	
	public function configureEntityValue($value, bool $asString = false){
		 $data = [
		    "Chicken" => 10,
		    "Cow" => 11,
	       "Pig" => 12,
	       "Sheep" => 13,
	       "Wolf" => 14,
	       "Villager" => 15,
	       "Mooshroom" => 16,
	       "Squid" => 17,
	       "Rabbit" => 18,
	       "Bat" => 19,
	       "IronGolem" => 20,
	       "SnowGolem" => 21,
	       "Ocelot" => 22,
	       "Horse" => 23,
	       "Donkey" => 24,
	       "Mule" => 25,
	       "SkeletonHorse" => 26,
	       "ZombieHorse" => 27,
	       "PolarBear" => 28,
	       "Llama" => 29,
	       "Parrot" => 30,
	       "Zombie" => 32,
	       "Creeper" => 33,
	       "Skeleton" => 34,
	       "Spider" => 35,
	       "ZombiePigman" => 36,
	       "Slime" => 37,
	       "Enderman" => 38,
	       "Silverfish" => 39,
	       "CaveSpider" => 40,
	       "Ghast" => 41,
	       "MagmaCube" => 42,
	       "Blaze" => 43,
	       "ZombieVillager" => 44,
	       "Witch" => 45,
	       "Stray" => 46,
	       "Husk" => 47,
	       "WitherSkeleton"  => 48,
	       "Guardian" => 49,
	       "Shulker" => 54,
	       "Endermite"  => 55,
	       "Vindicator" => 57
		 ];
		 foreach($data as $name => $id){
		    if(strtolower($value) == strtolower($name) and $asString == false){
			   return $id;
			 }elseif($value == $id and $asString){
				return $name;
			 }
		 }
	return $asString ? "" : 0; # Not matched
	}
	
	/**
	 * @return Int
	 */
	
	public function getMaxSpawnDelay(): Int{
	    return $this->maxSpawnDelay;
	}
	
	/**
	 * @return Int
	 */
	
	public function getMinSpawnDelay(): Int{
	    return $this->minSpawnDelay;
	}
	
	/**
	 * @return Int
	 */
	
	public function getEntityEid(): Int{
	    return $this->eid;
	}
	
	/**
	 * @return string
	 */
	
	public function getEntityId(): string{
	    return $this->entity;
	}
	
	/**
	 * @return Int[]
	 */
	
	public function getSpawnRange(): array{
	    return $this->spawnRange;
	}
	
	/**
	 * @return Int
	 */
	
	public function getSpawnLimit(): Int{
	    return $this->spawnLimit;
	}
	
	/**
	 * @param Int $playerRange
	 * @param Int $spawnRange
	 */
	
	public function setSpawnRange(Int $playerRange, Int $spawnRange): void{
	    $this->spawnRange["player"] = $playerRange;
	    $this->spawnRange["range"] = $spawnRange;
	}
	
	/**
	 * @param Int $limit
	 */
	
	public function setSpawnLimit(Int $limit): void{
	    $this->spawnLimit = $limit;
	}
	
	/**
	 * @param Int $value
	 */
	
	public function setMaxSpawnDelay(Int $value): void{
	    $this->maxSpawnDelay = $value;
	}
	
	/**
	 * @param Int $value
	 */
	
	public function setMinSpawnDelay(Int $value): void{
	    $this->minSpawnDelay = $value;
	}
	
	/**
	 * @param Int $eid
	 */
	
	public function setEntityEid(Int $eid): void{
	     $this->eid = $eid;
	     $this->setEntityId($this->configureEntityValue($eid, true) ?? "");
	}
	
	/**
	 * @param string $id
	 */
	
	public function setEntityId(string $id): void{
	    $this->entity = $id;
	    $this->eid = $this->configureEntityValue($id, false);
	    $this->namedtag->SpawnData->id->setValue($id);
       $this->scheduleUpdate();
	}
	
	/**
	 * @return bool
	 */
	
	public function canSpawn(): bool{
		 if($this->closed){
			return false;
		 }
		 $sr = $this->spawnRange["range"]; # No need here...
		 $pr = $this->spawnRange["player"];
	    $server = Server::getInstance();
	    $maxEntities = $this->getSpawnLimit();
	    $count = 0;
	    $nearbyPlayer = false;
	    foreach($this->getLevel()->getNearbyEntities($this->getLevel()->getBlock($this)->getBoundingBox()->grow($pr, $pr, $pr)) as $ent){
	       $count += $ent::NETWORK_ID == $this->getEntityEid() ? 1 : 0;
	       if($ent instanceof Player){
		      $nearbyPlayer = true;
		    }
	    }
	    if($count >= $maxEntities){
		   return false;
		 }
	return $nearbyPlayer;
	}
	
	/**
	 * @void saveNBT
	 */
	
	public function saveNBT(): void{
	    parent::saveNBT();
	    $this->namedtag->SpawnData->setShort("SpawnCount", $this->spawnLimit);
	    $this->namedtag->SpawnData->setShort("SpawnRange", $this->spawnRange["range"]);
	    $this->namedtag->SpawnData->setShort("MinSpawnDelay", $this->minSpawnDelay);
	    $this->namedtag->SpawnData->setShort("MaxSpawnDelay", $this->maxSpawnDelay);
	    $this->namedtag->SpawnData->setShort("Delay", $this->delay);
	    $this->namedtag->SpawnData->setString("id", $this->entity);
	    $this->namedtag->SpawnData->setShort("RequiredPlayerRange", $this->spawnRange["player"]);
	}
	
	/**
	 * @return Position|null
	 */
	
	public function recalculateSpawnPosition(){
		 $r = $this->spawnRange["range"];
	    $pos = $this->add(rand(-$r, $r), rand(0, 2), rand(-$r, $r));
		 if($this->getLevel()->getBlock($pos)->getId() !== 0){
			return null;
		 }
   return new Position($pos->x, $pos->y, $pos->z, $this->level);
	}
	
	/**
	 * @return bool
	 * If returned false, tile wont tick
	 */
	
	public function onUpdate(): bool{
	    if($this->isClosed()){
		   return false; # Why not?
		 }
		 if($this->canSpawn() == false){
			return true; # Why not again?
		 }
		 $this->delay++;
		 var_dump($this->delay);
		 if($this->delay < $this->minSpawnDelay){
			
		 }else{
		   $this->delay = 0;
		   $pos = $this->recalculateSpawnPosition();
		   if($pos !== null){
			  $this->spawnEntity($pos);
			}
		 }
		 if($this->delay >= rand($this->minSpawnDelay, $this->maxSpawnDelay)){
			$this->delay = 0;
			$spawnCount = 0;
			$ms = rand(0, $this->getSpawnLimit());
			while(($pos = $this->recalculateSpawnPosition()) !== null){
			   $this->spawnEntity($pos);
			   $spawnCount++;
			   if($spawnCount >= $ms){
				  break;
				}
				$pos = $this->recalculateSpawnPosition();
			}
	   }
      $this->scheduleUpdate();
	return true;
	}
	
	/**
	 * @param Position $pos
	 */
	
	public function spawnEntity(Position $pos): void{
	    $nbt = Entity::createBaseNBT($pos, null, rand(0.0, 5.5), rand(0.0, 10.5));
	    $entity = Entity::createEntity($this->eid, $pos->level, $nbt);
	    if($entity !== null){
		   $entity->spawnToAll();
		}
	}
	
	public function addAdditionalSpawnData(CompoundTag $nbt): void{
	    $nbt->SpawnData = $this->namedtag->SpawnData;
	}
}