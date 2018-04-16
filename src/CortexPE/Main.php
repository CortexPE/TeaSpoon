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
use CortexPE\handlers\{
	EnchantHandler, PacketHandler
};
use CortexPE\item\{
	enchantment\Enchantment, ItemManager
};
use CortexPE\level\weather\Weather;
use CortexPE\network\PacketManager;
use CortexPE\plugin\AllAPILoaderManager;
use CortexPE\task\TickLevelsTask;
use CortexPE\tile\Tile;
use CortexPE\utils\{
	FishingLootTable, TextFormat
};
use pocketmine\command\{
	CommandSender, defaults\DumpMemoryCommand, defaults\GarbageCollectorCommand, defaults\StatusCommand
};
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginLogger;
use pocketmine\utils\Config;

class Main extends PluginBase {

	// self explanatory constants
	public const CONFIG_VERSION = 24;
	public const BASE_POCKETMINE_VERSION = "1.7dev"; // The PocketMine version before Jenkins builds it... (Can be found on PocketMine.php as the 'VERSION' constant)
	public const TESTED_MIN_POCKETMINE_VERSION = "1.7dev-939"; // The minimum build this was tested working
	public const TESTED_MAX_POCKETMINE_VERSION = "1.7dev-940"; // The current build this was actually tested

	///////////////////////////////// START OF INSTANCE VARIABLES /////////////////////////////////
	/** @var Config */
	public static $config;
	/** @var Config */
	public static $cacheFile;
	/** @var int[] */
	public static $onPortal = [];
	/** @var Main */
	private static $instance;
	/** @var Session[] */
	private $sessions = [];
	/** @var bool */
	private $disable = false;
	/** @var string */
	private static $sixCharCommitHash = "";
	////////////////////////////////// END OF INSTANCE VARIABLES //////////////////////////////////

	///////////////////////////////// START OF CONFIGS VARIABLES /////////////////////////////////
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
	public static $limitedCreative = true;
	/** @var bool */
	public static $randomFishingLootTables = false;
	/** @var bool */
	public static $vanillaNetherTranfer = false;
	/** @var string */
	public static $overworldLevelName = "";
	/** @var Level */
	public static $overworldLevel = null;
	/** @var bool */
	public static $instantArmorReplace = false;
	/** @var bool */
	public static $elytraEnabled = true;
	/** @var bool */
	public static $elytraBoostEnabled = true;
	/** @var bool */
	public static $silkSpawners = false;
	/** @var bool */
	public static $fireworksEnabled = true;
	/** @var bool */
	public static $totemEnabled = true;
	/** @var bool */
	public static $ePearlEnabled = true;
	/** @var bool */
	public static $chorusFruitEnabled = true;
	/** @var bool */
	public static $instantArmorEnabled = true;
	/** @var bool */
	public static $dropMobExperience = true;
	/** @var bool */
	public static $fishingEnabled = true;
	/** @var bool */
	public static $clearInventoryOnGMChange = false;
	/** @var bool */
	public static $mobSpawnerEnable = true;
	/** @var bool */
	public static $mobSpawnerDamageAsEID = false;
	/** @var bool */
	public static $hoppersEnabled = true;
	/** @var bool */
	public static $beaconEnabled = true;
	/** @var bool */
	public static $beaconEffectsEnabled = true;
	/** @var bool */
	public static $shulkerBoxEnabled = true;
	/** @var bool */
	public static $elytraBoostParticles = true;
	/** @var bool */
	public static $XPOrbOverride = false;
	/** @var bool */
	public static $XPPickupDelay = false;
	/** @var bool */
	public static $endCrystalExplode = true;
	/** @var int */
	public static $XPTicksTillDespawn = 200;
	/** @var bool */
	public static $EnchantingTableEnabled = true;
	/** @var bool */
	public static $AnvilEnabled = true;
	/** @var bool */
	public static $dragonEggTeleport = true;
	/** @var float */
	public static $endCrystalPower = 6;
	/** @var bool */
	public static $cars = false;
	////////////////////////////////// END OF CONFIGS VARIABLES //////////////////////////////////

	public static function getInstance(): Main{
		return self::$instance;
	}

	public function onLoad(){
		if(Utils::checkSpoon()){
			$this->getLogger()->error("This plugin is for PMMP only. It is meant to extend PMMP's functionality.");
			$this->getLogger()->error("The plugin will disable itself after being later enabled by the server to prevent any interference with the existing Spoon features.");
			$this->disable = true;
		}
		$this->getLogger()->info("Loading Resources...");

		// Load Resources //
		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
		$this->saveDefaultConfig();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		self::$cacheFile = new Config($this->getDataFolder() . "cache.json", Config::JSON);

		// Load Configuration //
		self::$netherName = self::$config->getNested("dimensions.nether.levelName", self::$netherName);
		self::$endName = self::$config->getNested("dimensions.end.levelName", self::$endName);
		self::$loadAllAPIs = self::$config->getNested("misc.loadAllAPIs", self::$loadAllAPIs);
		self::$lightningFire = self::$config->getNested("entities.lightningFire", self::$lightningFire);
		self::$enderPearlCooldown = self::$config->getNested("enderPearl.cooldown", self::$enderPearlCooldown);
		self::$ePearlDamage = self::$config->getNested("enderPearl.damage", self::$ePearlDamage);
		self::$chorusFruitCooldown = self::$config->getNested("chorusFruit.cooldown", self::$chorusFruitCooldown);
		self::$registerVanillaEntities = self::$config->getNested("entities.register", self::$registerVanillaEntities);
		self::$registerVanillaEnchantments = self::$config->getNested("enchantments.register", self::$registerVanillaEnchantments);
		self::$registerDimensions = self::$config->getNested("dimensions.enable", self::$registerDimensions);
		self::$weatherEnabled = self::$config->getNested("weather.enable", self::$weatherEnabled);
		self::$weatherMinTime = self::$config->getNested("weather.minDuration", self::$weatherMinTime);
		self::$weatherMaxTime = self::$config->getNested("weather.maxDuration", self::$weatherMaxTime);
		self::$enableWeatherLightning = self::$config->getNested("weather.lightning", self::$enableWeatherLightning);
		self::$limitedCreative = self::$config->getNested("player.limitedCreative", self::$limitedCreative);
		self::$randomFishingLootTables = self::$config->getNested("misc.randomFishingLootTables", self::$randomFishingLootTables);
		self::$vanillaNetherTranfer = self::$config->getNested("dimensions.nether.vanillaNetherTranfer", self::$vanillaNetherTranfer);
		self::$overworldLevelName = self::$config->getNested("dimensions.overrideOverworldLevel", self::$overworldLevelName);
		self::$instantArmorReplace = self::$config->getNested("player.instantArmor.replace", self::$instantArmorReplace);
		self::$elytraEnabled = self::$config->getNested("player.elytra.enable", self::$elytraEnabled);
		self::$elytraBoostEnabled = self::$config->getNested("player.elytra.enableElytraBoost", self::$elytraBoostEnabled);
		self::$silkSpawners = self::$config->getNested("mobSpawner.silkTouchSpawners", self::$silkSpawners);
		self::$fireworksEnabled = self::$config->getNested("fireworks.enable", self::$fireworksEnabled);
		self::$totemEnabled = self::$config->getNested("player.totemOfUndying", self::$totemEnabled);
		self::$ePearlEnabled = self::$config->getNested("enderPearl.enable", self::$ePearlEnabled);
		self::$chorusFruitEnabled = self::$config->getNested("chorusFruit.enable", self::$chorusFruitEnabled);
		self::$instantArmorEnabled = self::$config->getNested("player.instantArmor.enable", self::$instantArmorEnabled);
		self::$dropMobExperience = self::$config->getNested("Xp.dropMobExperience", self::$dropMobExperience);
		self::$fishingEnabled = self::$config->getNested("player.fishing", self::$fishingEnabled);
		self::$clearInventoryOnGMChange = self::$config->getNested("player.clearInventoryOnGameModeChange", self::$clearInventoryOnGMChange);
		self::$mobSpawnerEnable = self::$config->getNested("mobSpawner.enable", self::$mobSpawnerEnable);
		self::$mobSpawnerDamageAsEID = self::$config->getNested("mobSpawner.enable", self::$mobSpawnerDamageAsEID);
		self::$hoppersEnabled = self::$config->getNested("hopper.enable", self::$hoppersEnabled);
		self::$beaconEnabled = self::$config->getNested("beacon.enable", self::$beaconEnabled);
		self::$beaconEffectsEnabled = self::$config->getNested("beacon.effectsEnabled", self::$beaconEffectsEnabled);
		self::$shulkerBoxEnabled = self::$config->getNested("shulkerBox.enable", self::$shulkerBoxEnabled);
		self::$elytraBoostParticles = self::$config->getNested("player.elytra.elytraBoostParticles", self::$elytraBoostParticles);
		self::$XPOrbOverride = self::$config->getNested("Xp.override", self::$XPOrbOverride);
		self::$XPPickupDelay = self::$config->getNested("Xp.pickupDelay", self::$XPPickupDelay);
		self::$endCrystalExplode = self::$config->getNested("entities.endCrystalExplosion", self::$endCrystalExplode);
		self::$XPTicksTillDespawn = self::$config->getNested("Xp.ticksTillDespawn", self::$XPTicksTillDespawn);
		self::$EnchantingTableEnabled = self::$config->getNested("enchantments.enchantingTableEnabled", self::$EnchantingTableEnabled);
		self::$AnvilEnabled = self::$config->getNested("anvil.enable", self::$AnvilEnabled);
		self::$dragonEggTeleport = self::$config->getNested("blocks.dragonEggTeleport", self::$dragonEggTeleport);
		self::$endCrystalPower = self::$config->getNested("entities.endCrystalPower", self::$endCrystalPower);
		self::$cars = self::$config->getNested("misc.cars", self::$cars);

		// Pre-Enable Checks //

		// Phars Force Poggit Builds only //
		if(Utils::isPhared()){ // unphared = dev
			$thisPhar = new \Phar(\Phar::running(false));
			$meta = $thisPhar->getMetadata(); // https://github.com/poggit/poggit/blob/beta/src/poggit/ci/builder/ProjectBuilder.php#L227-L236
			if(!isset($meta["builderName"]) || !is_array($meta)){
				$this->getLogger()->error("Only use TeaSpoon Builds from Poggit: https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~");
				$this->disable = true;
				return;
			}

			self::$sixCharCommitHash = substr($meta["fromCommit"], 0, 6);
		} else {
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

			$this->getLogger()->warning("You're using a developer's build of TeaSpoon. For better performance and stability, please get a pre-packaged version here: https://poggit.pmmp.io/ci/CortexPE/TeaSpoon/~");
		}

		if(!Utils::isServerPhared() || $this->getServer()->getPocketMineVersion() == self::BASE_POCKETMINE_VERSION){
			$this->getLogger()->warning("Non-Packaged / Unsupported PocketMine installation detected. Some of TeaSpoon's protective functions are now disabled.");
		}

		self::$instance = $this;
	}

	public function onEnable(){
		// Yes compatibility checks (the ones with setEnabled(false)) are repeated because they should still look good in CLI...
		if($this->disable){
			$this->setEnabled(false);
			return;
		}

		$yr = 2017 . ((2017 != date('Y')) ? '-' . date('Y') : '');
		$stms = TextFormat::DARK_GREEN . "\n\nMMP\"\"MM\"\"YMM              " . TextFormat::GREEN . " .M\"\"\"bgd                                        " . TextFormat::DARK_GREEN . "\nP'   MM   `7             " . TextFormat::GREEN . " ,MI    \"Y                                        " . TextFormat::DARK_GREEN . "\n     MM  .gP\"Ya   ,6\"Yb.  " . TextFormat::GREEN . "`MMb.   `7MMpdMAo.  ,pW\"Wq.   ,pW\"Wq.`7MMpMMMb.  " . TextFormat::DARK_GREEN . "\n     MM ,M'   Yb 8)   MM" . TextFormat::GREEN . "    `YMMNq. MM   `Wb 6W'   `Wb 6W'   `Wb MM    MM  " . TextFormat::DARK_GREEN . "\n     MM 8M\"\"\"\"\"\"  ,pm9MM " . TextFormat::GREEN . " .     `MM MM    M8 8M     M8 8M     M8 MM    MM  " . TextFormat::DARK_GREEN . "\n     MM YM.    , 8M   MM  " . TextFormat::GREEN . "Mb     dM MM   ,AP YA.   ,A9 YA.   ,A9 MM    MM  " . TextFormat::DARK_GREEN . "\n   .JMML.`Mbmmd' `Moo9^Yo." . TextFormat::GREEN . "P\"Ybmmd\"  MMbmmd'   `Ybmd9'   `Ybmd9'.JMML  JMML." . TextFormat::GREEN . "\n                                    MM                                     \n                                  .JMML.  " . TextFormat::YELLOW . Splash::getRandomSplash() . TextFormat::RESET . "\nCopyright (C) CortexPE " . $yr . "\n";
		switch(true){ // todo: add more events?
			case (Splash::isValentines()):
				$stms = TextFormat::RED . "\n\n   .-.                                        " . TextFormat::DARK_RED . "       .-.                    " . TextFormat::RESET . "\n" . TextFormat::RED . "  (_) )-.                                  /  " . TextFormat::DARK_RED . " .--.-'                       " . TextFormat::RESET . "\n" . TextFormat::RED . "     /   \    .-.  .    .    .-.  ).--.---/---" . TextFormat::DARK_RED . "(  (_).-.  .-._..-._..  .-.   " . TextFormat::RESET . "\n" . TextFormat::RED . "    /     \ ./.-'_/ \  / \ ./.-'_/       /    " . TextFormat::DARK_RED . " `-.  /  )(   )(   )  )/   )  " . TextFormat::RESET . "\n" . TextFormat::RED . " .-/.      )(__.'/ ._)/ ._)(__.'/       /    " . TextFormat::DARK_RED . "_    )/`-'  `-'  `-'  '/   (   " . TextFormat::RESET . "\n" . TextFormat::RED . "(_/  `----'     /    /                      " . TextFormat::DARK_RED . "(_.--'/                      `- " . TextFormat::RESET . "\n                                              " . TextFormat::YELLOW . Splash::getRandomSplash() . TextFormat::RESET . "\nCopyright (C) CortexPE " . $yr . "\n";
				break;
		}
		$this->getLogger()->info("Loading..." . $stms);

		if(!$this->checkServer()){
			$this->setEnabled(false);
			return;
		}

		$this->loadEverythingElse();
		$this->getLogger()->info("TeaSpoon is distributed under the AGPL License");
		$this->checkConfigVersion();
	}

	private function loadEverythingElse(){
		// Initialize ze managars //
		CommandManager::init();
		Enchantment::init();
		BlockManager::init();
		ItemManager::init();
		EntityManager::init();
		// LevelManager::init(); EXECUTED VIA EventListener
		Tile::init();
		FishingLootTable::init();
		PacketManager::init();
		PacketManager::init();

		// Register Listeners
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new PacketHandler($this), $this);
		if(self::$registerVanillaEnchantments){
			$this->getServer()->getPluginManager()->registerEvents(new EnchantHandler($this), $this);
		}

		// Task(s)
		if(self::$weatherEnabled){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new TickLevelsTask($this), 1);
		}

		// Load other API plugins at last (too still look gud)
		if(self::$loadAllAPIs){
			AllAPILoaderManager::init();
		}
	}

	private function checkConfigVersion(){
		$ver = self::$config->get("version");

		if($ver === null || $ver === false || $ver < self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is Outdated! Keep a backup of it and delete the outdated file.");
		}elseif($ver > self::CONFIG_VERSION){
			$this->getLogger()->critical("Your configuration file is from a higher version of TeaSpoon! Please update the plugin from https://github.com/CortexPE/TeaSpoon");
		}

		if(self::$cacheFile->get("date", "") != strval(date("d-m-y"))){
			self::$cacheFile->set("date", strval(date("d-m-y")));
		}
	}

	private function checkServer() : bool {
		if(Utils::isServerPhared()){
			$serverVersion = $this->getServer()->getPocketMineVersion();
			$versionMinComp = version_compare($serverVersion, self::TESTED_MIN_POCKETMINE_VERSION);
			$versionMaxComp = version_compare($serverVersion, self::TESTED_MAX_POCKETMINE_VERSION);

			if($versionMinComp < 0){
				// PocketMine version is older than minimum tested version
				$this->getLogger()->alert("This plugin has been tested on PocketMine version: " . self::TESTED_MAX_POCKETMINE_VERSION . ", running it on older PocketMine versions is very unstable. To prevent any futher in-compatibility issues, TeaSpoon will now disable itself."); // I still put the max version so that patches will be included...
				return false;
			}

			if($versionMaxComp > 0){
				$this->getLogger()->info("You're using a newer PocketMine build than the highest tested version (" . self::TESTED_MAX_POCKETMINE_VERSION . "). Please report bugs if there's any. ;)");
			}
		}
		return true;
	}

	public function onDisable(){
		self::$cacheFile->save();
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

	public static function sendVersion(CommandSender $sender){
		$sender->getServer()->dispatchCommand($sender, "ver");
		$sender->sendMessage("\x2d\x2d\x2d\x20\x2b\x20\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x2d\x20\x2b\x20\x2d\x2d\x2d");
		$logo = TextFormat::DARK_GREEN . "\x54\x65\x61" . TextFormat::GREEN . "\x53\x70\x6f\x6f\x6e";
		if(Splash::isValentines()){
			$logo = TextFormat::RED . "\x44\x65\x73\x73\x65\x72\x74" . TextFormat::DARK_RED . "\x53\x70\x6f\x6f\x6e";
		}
		$sender->sendMessage("\x54\x68\x69\x73\x20\x73\x65\x72\x76\x65\x72\x20\x69\x73\x20\x72\x75\x6e\x6e\x69\x6e\x67\x20" . $logo . TextFormat::WHITE . "\x20\x76" . self::$instance->getDescription()->getVersion() . (Utils::isPhared() ? "" : "-dev") . "\x20\x66\x6f\x72\x20\x50\x6f\x63\x6b\x65\x74\x4d\x69\x6e\x65\x2d\x4d\x50\x20" . (self::TESTED_MIN_POCKETMINE_VERSION != self::TESTED_MAX_POCKETMINE_VERSION ? self::TESTED_MIN_POCKETMINE_VERSION . "\x20\x2d\x20" . self::TESTED_MAX_POCKETMINE_VERSION : self::TESTED_MAX_POCKETMINE_VERSION));

		if(self::$sixCharCommitHash != ""){
			$sender->sendMessage("\x43\x6f\x6d\x6d\x69\x74\x3a\x20" . self::$sixCharCommitHash);
		}
		$sender->sendMessage("\x52\x65\x70\x6f\x73\x69\x74\x6f\x72\x79\x3a\x20\x68\x74\x74\x70\x73\x3a\x2f\x2f\x67\x69\x74\x68\x75\x62\x2e\x63\x6f\x6d\x2f\x43\x6f\x72\x74\x65\x78\x50\x45\x2f\x54\x65\x61\x53\x70\x6f\x6f\x6e");
		$sender->sendMessage("\x57\x65\x62\x73\x69\x74\x65\x3a\x20\x68\x74\x74\x70\x73\x3a\x2f\x2f\x43\x6f\x72\x74\x65\x78\x50\x45\x2e\x78\x79\x7a");
	}

	public static function getPluginLogger() : PluginLogger { // 2 lazy
		return self::$instance->getLogger();
	}
}
