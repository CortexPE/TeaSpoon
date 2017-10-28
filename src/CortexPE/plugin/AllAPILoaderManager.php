<?php

namespace CortexPE\plugin;

use CortexPE\plugin\AllAPI\FolderPluginLoader;
use CortexPE\plugin\AllAPI\PharPluginLoader;
use CortexPE\plugin\AllAPI\ScriptPluginLoader;
use pocketmine\plugin\PluginLoadOrder;
use pocketmine\Server as PMServer;

class AllAPILoaderManager {
	public static function init(){
		PMServer::getInstance()->getPluginManager()->registerInterface(PharPluginLoader::class);
		PMServer::getInstance()->getPluginManager()->registerInterface(ScriptPluginLoader::class);
		if(self::hasFolderPluginLoader()){
			PMServer::getInstance()->getPluginManager()->registerInterface(FolderPluginLoader::class);
		}

		PMServer::getInstance()->getPluginManager()->loadPlugins(PMServer::getInstance()->getPluginPath(), [PharPluginLoader::class, ScriptPluginLoader::class, FolderPluginLoader::class]);
		PMServer::getInstance()->enablePlugins(PluginLoadOrder::STARTUP);
	}

	public static function hasFolderPluginLoader(){
		return (PMServer::getInstance()->getPluginManager()->getPlugin("DevTools")->isEnabled() or
			PMServer::getInstance()->getPluginManager()->getPlugin("FolderPluginLoader")->isEnabled());
	}
}