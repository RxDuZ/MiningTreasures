<?php

namespace rxduz\treasures;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use rxduz\treasures\command\TreasureCommand;
use rxduz\treasures\listener\BlockListener;
use rxduz\treasures\listener\PlayerListener;
use rxduz\treasures\translation\Translation;

class Main extends PluginBase {

    public const PREFIX = TextFormat::BOLD . TextFormat::DARK_GRAY . "(" . TextFormat::AQUA . "MiningTreasures" . TextFormat::DARK_GRAY . ")" . TextFormat::RESET . " ";

    use SingletonTrait;

    protected function onEnable(): void
    {
        self::setInstance($this);

        $this->saveDefaultConfig();

        $this->saveResource("/treasures.yml");

        $this->saveResource("/messages.yml");

        Translation::getInstance()->init();

        TreasureManager::getInstance()->init();

        $this->getServer()->getPluginManager()->registerEvents(new BlockListener(), $this);

        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener(), $this);

        $this->getServer()->getCommandMap()->register("MiningTreasures", new TreasureCommand());

        Server::getInstance()->getLogger()->info(self::PREFIX . TextFormat::GREEN . "Plugin enabled successfully made by iRxDuZ.");
    }

    protected function onDisable(): void
    {
        Server::getInstance()->getLogger()->info(self::PREFIX . TextFormat::RED . "Plugin disabled successfully.");
    }

}

?>