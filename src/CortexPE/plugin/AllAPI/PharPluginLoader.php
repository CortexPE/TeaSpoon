<?php

namespace CortexPE\plugin\AllAPI;

use pocketmine\plugin\PharPluginLoader as PMPharPluginLoader;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginDescription;
use pocketmine\Server;

class PharPluginLoader extends PMPharPluginLoader {

    private $server;

    public function __construct(Server $server) {
        parent::__construct($server);
        $this->server = $server;
    }

    public function getPluginDescription(string $file) {
        $phar = new \Phar($file);
        if (isset($phar["plugin.yml"])) {
            $pluginYml = $phar["plugin.yml"];
            if ($pluginYml instanceof \PharFileInfo) {
                $description = new PluginDescription($pluginYml->getContent());
                if (!$this->server->getPluginManager()->getPlugin($description->getName()) instanceof Plugin and !in_array($this->server->getApiVersion(), $description->getCompatibleApis())) {
                    $api = (new \ReflectionClass("pocketmine\plugin\PluginDescription"))->getProperty("api");
                    $api->setAccessible(true);
                    $api->setValue($description, [$this->server->getApiVersion()]);
                    return $description;
                }
            }
        }

        return null;
    }
}