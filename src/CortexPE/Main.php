<?php

declare(strict_types = 1);

namespace CortexPE;

use CortexPE\block\BlockManager;
use CortexPE\commands\CommandManager;
use CortexPE\entity\EntityManager;
use CortexPE\item\enchantment\Enchantment;
use CortexPE\item\ItemManager;
use CortexPE\plugin\AllAPILoaderManager;
use CortexPE\task\CheckPlayersTask;
use CortexPE\tile\Tile;
use pocketmine\level\Level;
use pocketmine\Player as PMPlayer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\Terminal;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {
// Use static variables if it's going to be accessed by other Classes :)

	/** @var string */
	public static $netherName = "nether";
	/** @var Level */
	public static $netherLevel;

	/** @var string */
	public static $endName = "ender";
	/** @var Level */
	public static $endLevel;

	/** @var Config */
	public static $config;

	/** @var string */
	public static $checkingMode = "task";
	/** @var bool */
	public static $lightningFire = false;
	/** @var string[] */
	public static $teleporting = [];
	/** @var bool */
	public $loadAllAPIs = false;
	/** @var PMPlayer[] */
	public $lastUses = [];
	private $splashes = [
		'Low-Calorie blend',
		"Don't panic! Have a cup of tea",
		"In England, Everything stops for tea",
		"ENGLAND IS MY CITY (not really)",
		"POWERED By Dubstep",
		"A E S T H E T H I C S",
		"WHO PUT THAT HERE?",
		"#BlameShoghi",
		"ERMAHGERD",
		"Written in PHP!",
		"This is a splash text.",
		"YOUR NAME",
		"ONE LOVE",
		"I KILLED THE SHERIFF... But not the deputy.",
		"Oops.",
		"rip.",
		"Fixed Typo!",
		"Fixed Typo! 2",
		"Fixed Typo! 2 FINAL",
		"Fixed Typo! 2 FINALFINAL",
		"Fixed Typo! 2 FINALFINANFINAL",
		"This splash text is a joke.",
		"Who made this?!",
		"How may I help you?",
		"asymmetricalacirtemmysa!",
		"SUPERCALIFRAGILISTICEXPIALIDOCIOUS!",
		"Well this exists.",
		"IE EXISTS TO DOWNLOAD CHROME!",
		"I'm sorry Dave. I'm afraid I can't do that.",
		"I might have killed it.",
		"PUNCHING TREES!",
		"Bug Fix",
		"Bug Fix 2",
		"Bug Fix 2 FINAL",
		"Bug Fix 2 FINALFINAL",
		"Bug Fix 2 FINALFINANFINAL",
		"We have VCS Systems. :P",
		":shrug:",
		// Add more splashes fur fun. xD
	];

	public function onLoad(){
		if(Utils::checkSpoon()){
			$this->getLogger()->error("This plugin is for PMMP only. It is meant to extend PMMP's functionality.");
			$this->getLogger()->error("The plugin will now disable itself to prevent any interference with the existing Spoon features.");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

		self::$netherName = self::$config->get("netherName", "nether");
		self::$endName = self::$config->get("endName", "ender");
		self::$checkingMode = self::$config->get("dimensionDetectionType", "task");
		$this->loadAllAPIs = self::$config->get("loadAllAPIs", false);
		self::$lightningFire = self::$config->get("lightningFire", false);
	}

	public function onEnable(){
		$rm = $this->splashes[array_rand($this->splashes)];
		$stms = TextFormat::AQUA . '
CortexPE\'s' . TextFormat::DARK_GREEN . '
         MMP""MM""YMM              ' . TextFormat::GREEN . ' .M"""bgd                                        ' . TextFormat::DARK_GREEN . '
         P\'   MM   `7             ' . TextFormat::GREEN . ' ,MI    "Y                                        ' . TextFormat::DARK_GREEN . '
              MM  .gP"Ya   ,6"Yb.  ' . TextFormat::GREEN . '`MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.  ' . TextFormat::DARK_GREEN . '
              MM ,M\'   Yb 8)   MM' . TextFormat::GREEN . '    `YMMNq. MM   `Wb 6W\'   `Wb 6W\'   `Wb MM    MM  ' . TextFormat::DARK_GREEN . '
              MM 8M""""""  ,pm9MM ' . TextFormat::GREEN . ' .     `MM MM    M8 8M     M8 8M     M8 MM    MM  ' . TextFormat::DARK_GREEN . '
              MM YM.    , 8M   MM  ' . TextFormat::GREEN . 'Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM  ' . TextFormat::DARK_GREEN . '
            .JMML.`Mbmmd\' `Moo9^Yo.' . TextFormat::GREEN . 'P"Ybmmd"  MMbmmd\'   `Ybmd9\'   `Ybmd9\'.JMML  JMML.' . TextFormat::GREEN . '
                                             MM                                     
                                           .JMML.  ' . TextFormat::UNDERLINE . TextFormat::YELLOW . $rm . TextFormat::RESET;
		//$this->getServer()->getLogger()->info($stms);

		$message = TextFormat::toANSI($stms . TextFormat::RESET);
		$cleanMessage = TextFormat::clean($message);

		if(!Terminal::hasFormattingCodes()){
			echo $cleanMessage . PHP_EOL;
		}else{
			echo $message . PHP_EOL;
		}

		// A E S T H E T H I C S
		$parts = 10;
		$offset = 100 / $parts;
		$percent = 0;

		echo "Loading " . ($percent += $offset) . "%...   \r";
		CommandManager::init();
		echo "Loading " . ($percent += $offset) . "%...   \r";
		Enchantment::init();
		echo "Loading " . ($percent += $offset) . "%...   \r";
		BlockManager::init();
		echo "Loading " . ($percent += $offset) . "%...   \r";
		ItemManager::init();
		echo "Loading " . ($percent += $offset) . "%...   \r";
		EntityManager::init();
		echo "Loading " . ($percent += $offset) . "%...   \r";
		// LevelManager::init(); EXECUTED VIA EventListener
		echo "Loading " . ($percent += $offset) . "%...   \r";
		if($this->loadAllAPIs){
			AllAPILoaderManager::init();
		}
		echo "Loading " . ($percent += $offset) . "%...   \r";
		Tile::init();

		echo "Loading " . ($percent += $offset) . "%...   \r";
		if(self::$checkingMode == "task"){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckPlayersTask($this), 10);
		}
		echo "Loading " . ($percent += $offset) . "%...   \r";
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		echo "Loading " . $percent . "% Completed...   \r";
		echo "                                         \r" . PHP_EOL;
	}
}