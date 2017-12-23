<?php

/**
 *
 * MMP""MM""YMM               .M"""bgd
 * P'   MM   `7              ,MI    "Y
 *      MM  .gP"Ya   ,6"Yb.  `MMb.   `7MMpdMAo.  ,pW"Wq.   ,pW"Wq.`7MMpMMMb.
 *      MM ,M'   Yb 8)   MM    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM
 *      MM 8M""""""  ,pm9MM  .     `MM MM    M8 8M     M8 8M     M8 MM    MM
 *      MM YM.    , 8M   MM  Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM
 *    .JMML.`Mbmmd' `Moo9^Yo.P"Ybmmd"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML.
 *                                     MM
 *                                   .JMML.
 * This file is part of TeaSpoon.
 *
 * TeaSpoon is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeaSpoon is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TeaSpoon.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author CortexPE
 * @link https://CortexPE.xyz
 *
 */

declare(strict_types = 1);

namespace CortexPE;

use CortexPE\block\BlockManager;
use CortexPE\commands\CommandManager;
use CortexPE\entity\EntityManager;
use CortexPE\handlers\EnchantHandler;
use CortexPE\handlers\PacketHandler;
use CortexPE\item\{
	enchantment\Enchantment, ItemManager
};
use CortexPE\level\weather\Weather;
use CortexPE\plugin\AllAPILoaderManager;
use CortexPE\task\AsynchronousEvaluator;
use CortexPE\task\CheckPlayersTask;
use CortexPE\task\TickLevelsTask;
use CortexPE\tile\Tile;
use CortexPE\utils\FishingRodLootTable;
use CortexPE\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\DumpMemoryCommand;
use pocketmine\command\defaults\GarbageCollectorCommand;
use pocketmine\command\defaults\StatusCommand;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase {

	const CONFIG_VERSION = 10;

	/** @var Config */
	public static $config;
	/** @var string */
	public static $netherName = "nether";
	/** @var Level */
	public static $netherLevel;
	/** @var string */
	public static $endName = "ender";
	/** @var Level */
	public static $endLevel;
	/** @var bool */
	public static $lightningFire = false;
	/** @var int */
	public static $ePearlDamage = 5;
	/** @var int */
	public static $enderPearlCooldown = 2;
	/** @var int */
	public static $chorusFruitCooldown = 2;
	/** @var bool */
	public static $registerVanillaEntities = true;
	/** @var bool */
	public static $registerVanillaEnchantments = true;
	/** @var bool */
	public static $registerDimensions = true;
	/** @var Weather[] */
	public static $weatherData = [];
	/** @var bool */
	public static $loadAllAPIs = false;
	/** @var bool */
	public static $weatherEnabled = true;
	/** @var int */
	public static $weatherMinTime = 6000;
	/** @var int */
	public static $weatherMaxTime = 12000;
	/** @var bool */
	public static $enableWeatherLightning = true;
	/** @var bool */
	public static $limitedCreative = false;
	/** @var bool */
	public static $debug = false;
	/** @var Main */
	private static $instance;
	/** @var Session[] */
	private $sessions = [];
	/** @var Config */
	public static $cacheFile;

	public static function getInstance(): Main{
		return self::$instance;
	}

	public static function sendVersion(CommandSender $sender){
		$sender->getServer()->dispatchCommand($sender, "ver");
		// anti-skid
		$sender->sendMessage("\x2d\x2d\x2d\x20\x2b\x20\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x20\x2b\x20\x2d\x2d\x2d");
		$sender->sendMessage("\x54\x68\x69\x73\x20\x73\x65\x72\x76\x65\x72\x20\x69\x73\x20\x72\x75\x6e\x6e\x69\x6e\x67\x20" . TextFormat::DARK_GREEN . "\x54\x65\x61" . TextFormat::GREEN . "\x53\x70\x6f\x6f\x6e" . TextFormat::WHITE . "\x20\x76" . self::$instance->getDescription()->getVersion() . "\x20\x66\x6f\x72\x20\x5b" . implode("\x2c\x20", self::$instance->getDescription()->getCompatibleApis()) . "\x5d");
		$sender->sendMessage("\x52\x65\x70\x6f\x73\x69\x74\x6f\x72\x79\x3a\x20\x68\x74\x74\x70\x73\x3a\x2f\x2f\x67\x69\x74\x68\x75\x62\x2e\x63\x6f\x6d\x2f\x43\x6f\x72\x74\x65\x78\x50\x45\x2f\x54\x65\x61\x53\x70\x6f\x6f\x6e");
		$sender->sendMessage("\x57\x65\x62\x73\x69\x74\x65\x3a\x20\x68\x74\x74\x70\x73\x3a\x2f\x2f\x43\x6f\x72\x74\x65\x78\x50\x45\x2e\x78\x79\x7a");
	}

	public function onLoad(){
		if(Utils::checkSpoon()){
			$this->getLogger()->error("This plugin is for PMMP only. It is meant to extend PMMP's functionality.");
			$this->getLogger()->error("The plugin will disable itself after being later enabled by the server to prevent any interference with the existing Spoon features.");
			Server::$isSpoon = true;
		}
		$this->getLogger()->info("Loading resources...");
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
		$this->saveDefaultConfig();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		self::$cacheFile = new Config($this->getDataFolder() . "cache.json", Config::JSON);

		self::$netherName = self::$config->getNested("dimensions.nether.levelName", "nether");
		self::$endName = self::$config->getNested("dimensions.end.levelName", "ender");
		self::$loadAllAPIs = self::$config->getNested("misc.loadAllAPIs", false);
		self::$lightningFire = self::$config->getNested("entities.lightningFire", false);
		self::$enderPearlCooldown = self::$config->getNested("enderPearl.cooldown", 2);
		self::$ePearlDamage = self::$config->getNested("enderPearl.damage", 5);
		self::$chorusFruitCooldown = self::$config->getNested("chorusFruit.cooldown", 2);
		self::$registerVanillaEntities = self::$config->getNested("entities.register", true);
		self::$registerVanillaEnchantments = self::$config->getNested("enchantments.register", true);
		self::$registerDimensions = self::$config->getNested("dimensions.enable", true);
		self::$weatherEnabled = self::$config->getNested("weather.enable", true);
		self::$weatherMinTime = self::$config->getNested("weather.minDuration", 6000);
		self::$weatherMaxTime = self::$config->getNested("weather.maxDuration", 12000);
		self::$enableWeatherLightning = self::$config->getNested("weather.lightning", true);
		self::$limitedCreative = self::$config->getNested("misc.limitedCreative", false);
		self::$debug = self::$config->get("debug", false); // intentionally don't add this on the config...

		if(self::$debug && !Utils::isPhared()){
			$this->getLogger()->warning("Debug Mode is enabled!");
			$this->getServer()->getLogger()->setLogDebug(true);
			$cm = $this->getServer()->getCommandMap();
			if($cm->getCommand("status") === null){
				$cm->register("pocketmine", new StatusCommand("status"));
			}
			if($cm->getCommand("gc") === null){
				$cm->register("pocketmine", new GarbageCollectorCommand("gc"));
			}
			if($cm->getCommand("dumpmemory") === null){
				$cm->register("pocketmine", new DumpMemoryCommand("dumpmemory"));
			}
		}elseif(Utils::isPhared()){
			$this->getLogger()->warning("Debug Mode is enabled but the plugin is in PHAR format... Debug mode will be disabled as an assumption that you'll be using the plugin for production purposes.");
			self::$debug = false;
		}

		self::$instance = $this;
	}

	public function onEnable(){
		if(Server::$isSpoon){
			$this->setEnabled(false);
			return;
		}
		$yr = 2017 . ((2017 != date('Y')) ? '-' . date('Y') : '');
		$stms = TextFormat::DARK_GREEN . "\nMMP\"\"MM\"\"YMM              " . TextFormat::GREEN . " .M\"\"\"bgd                                        " . TextFormat::DARK_GREEN . "\nP'   MM   `7             " . TextFormat::GREEN . " ,MI    \"Y                                        " . TextFormat::DARK_GREEN . "\n     MM  .gP\"Ya   ,6\"Yb.  " . TextFormat::GREEN . "`MMb.   `7MMpdMAo.  ,pW\"Wq.   ,pW\"Wq.`7MMpMMMb.  " . TextFormat::DARK_GREEN . "\n     MM ,M'   Yb 8)   MM" . TextFormat::GREEN . "    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM  " . TextFormat::DARK_GREEN . "\n     MM 8M\"\"\"\"\"\"  ,pm9MM " . TextFormat::GREEN . " .     `MM MM    M8 8M     M8 8M     M8 MM    MM  " . TextFormat::DARK_GREEN . "\n     MM YM.    , 8M   MM  " . TextFormat::GREEN . "Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM  " . TextFormat::DARK_GREEN . "\n   .JMML.`Mbmmd' `Moo9^Yo." . TextFormat::GREEN . "P\"Ybmmd\"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML." . TextFormat::GREEN . "\n                                    MM                                     \n                                  .JMML.  " . TextFormat::YELLOW . Splash::getRandomSplash() . TextFormat::RESET . "\nCopyright (C) CortexPE " . $yr . "\n";
		$this->getLogger()->info("Loading..." . $stms);

		$this->loadEverythingElse();
		$this->getLogger()->info("TeaSpoon is distributed under the AGPL License");
		$this->checkConfigs();
	}

	public function onDisable(){
		self::$cacheFile->save();
	}

	private function loadEverythingElse(){
		CommandManager::init();
		Enchantment::init();
		BlockManager::init();
		ItemManager::init();
		EntityManager::init();
		// LevelManager::init(); EXECUTED VIA EventListener
		Tile::init();
		FishingRodLootTable::init();
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new CheckPlayersTask($this), 5);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PacketHandler($this), $this);
		if(self::$registerVanillaEnchantments){
			$this->getServer()->getPluginManager()->registerEvents(new EnchantHandler($this), $this);
		}
		if(self::$weatherEnabled){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new TickLevelsTask($this), 1);
		}
		if(self::$loadAllAPIs){
			AllAPILoaderManager::init();
		}
	}

	private function checkConfigs(){
		$ver = self::$config->get("version");
		if($ver === null || $ver === false || $ver < self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is Outdated! Keep a backup of it and delete the outdated file.");
		}elseif($ver > self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is from a higher version of TeaSpoon! Please update the plugin from https://github.com/CortexPE/TeaSpoon");
		}

		if(self::$cacheFile->get("date", "") != strval(date("d-m-y"))){
			self::$cacheFile->set("date", strval(date("d-m-y")));
			self::$cacheFile->save(true);
		}
	}

	public function createSession(Player $player): bool{
		if(!isset($this->sessions[$player->getId()])){
			$this->sessions[$player->getId()] = new Session($player);
			$this->getLogger()->debug("Created " . $player->getName() . "'s Session");

			return true;
		}

		return false;
	}

	public function destroySession(Player $player): bool{
		if(isset($this->sessions[$player->getId()])){
			unset($this->sessions[$player->getId()]);
			$this->getLogger()->debug("Destroyed " . $player->getName() . "'s Session");

			return true;
		}

		return false;
	}

	public function getSessionById(int $id){
		if(isset($this->sessions[$id])){
			return $this->sessions[$id];
		}else{
			return null;
		}
	}

	public function getSessionByName(string $name){ // why nawt?
		foreach($this->sessions as $session){
			if($session->getPlayer()->getName() == $name){
				return $session;
			}
		}
		return null;
	}
}