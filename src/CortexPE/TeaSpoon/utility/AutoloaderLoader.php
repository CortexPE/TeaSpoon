<?php
/**
 * AutoloaderLoader.php – PM-Discord-Relay
 *
 * Copyright (C) 2018 Jack Noordhuis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Jack
 *
 * MODIFIED (7/17/2019): Change namespaces to TeaSpoon
 *
 */
declare(strict_types=1);

namespace CortexPE\TeaSpoon\utility;

use pocketmine\utils\MainLogger;

class AutoloaderLoader {
	/**
	 * Dummy function called to invoke the pocketmine class loader
	 */
	public static function load(): void {
	}

	/**
	 * Require the composer autoload file whenever this class is loaded by the pocketmine class loader
	 */
	public static function onClassLoaded(): void {
		if(!defined('CortexPE\TeaSpoon\COMPOSER_AUTOLOADER_PATH')) {
			if(\Phar::running(true) !== "") {
				define('CortexPE\TeaSpoon\COMPOSER_AUTOLOADER_PATH',
					\Phar::running(true) . "/vendor/autoload.php");
			} elseif(is_file($path = dirname(__DIR__, 4) . "/vendor/autoload.php")) {
				define('CortexPE\TeaSpoon\COMPOSER_AUTOLOADER_PATH', $path);
			} else {
				MainLogger::getLogger()->debug("[DiscordRelay] Composer autoloader not found.");
				MainLogger::getLogger()
						  ->debug("[DiscordRelay] Please install/update Composer dependencies or use provided releases.");
				trigger_error("[DiscordRelay] Couldn't find composer autoloader", E_USER_ERROR);

				return;
			}
		}
		require_once(\CortexPE\TeaSpoon\COMPOSER_AUTOLOADER_PATH);
	}
}