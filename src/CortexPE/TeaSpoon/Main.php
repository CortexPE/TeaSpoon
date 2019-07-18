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
 * @author CortexPE, and contributors
 * @link   https://github.com/CortexPE/TeaSpoon
 *
 */

declare(strict_types=1);

namespace CortexPE\TeaSpoon;

use CortexPE\TeaSpoon\module\ModuleManager;
use CortexPE\TeaSpoon\utility\AutoloaderLoader;
use CortexPE\TeaSpoon\utility\TOMLConfig;
use pocketmine\plugin\PluginBase;
use function class_exists;
use function closedir;
use function in_array;
use function opendir;
use function readdir;

class Main extends PluginBase {
	/** @var ModuleManager */
	private $moduleManager;

	/**
	 * @throws exception\ModuleException
	 */
	public function onLoad() {
		AutoloaderLoader::load(); // hack to trigger loading the composer autoload file

		$this->moduleManager = new ModuleManager($this);

		$modules = [];
		if($handle = opendir($this->getFile() . "src/CortexPE/TeaSpoon/module/impl")) {
			while($file = readdir($handle)) {
				if(!in_array($file, [".", ".."])) {
					$fn = pathinfo($file, PATHINFO_FILENAME);
					$modules[$fn] = true;
				}
			}
			closedir($handle);
		}
		$conf = new TOMLConfig($this->getDataFolder() . "modules.toml", $modules, [
			"TeaSpoon module list",
			"Set values to 'true' to enable specific modules, or 'false' to disable"
		]);
		foreach($conf->getData() as $moduleName => $enabled) {
			if($enabled) {
				$mName = "CortexPE\\TeaSpoon\\module\\impl\\" . $moduleName;
				if(class_exists($mName)) {
					$this->moduleManager->loadModule($mName);
				}else{
					$this->getLogger()->error("Unknown module '" . $moduleName . "'");
				}
			}
		}
	}

	public function onEnable(): void {
		$this->moduleManager->enableModules();
	}

	public function onDisable(): void {
		$this->moduleManager->disableModules();
	}
}
