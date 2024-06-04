<?php

namespace rxduz\treasures\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use rxduz\treasures\Main;
use rxduz\treasures\translation\Translation;
use rxduz\treasures\TreasureManager;

class TreasureCommand extends Command {

    public function __construct()
    {
        parent::__construct("treasure", "MiningTreasures command made by iRxDuZ", null, []);

        $this->setPermission("treasure.command.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

        if(!$this->testPermission($sender)){
            return;
        }

        $amount = 1;

        if(isset($args[0]) and is_numeric($args[0]) and $args[0] > 0){
            $amount = intval($args[0]);
        }

        $player = $sender;

        if(isset($args[1])){
            $player = Server::getInstance()->getPlayerByPrefix($args[1]);
        }

        if(!$player instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Player is not online.");

            return;
        }

        $item = TreasureManager::getInstance()->getTreasureItem($amount);

        if($player->getInventory()->canAddItem($item)){
            $player->getInventory()->addItem($item);
        } else {
            $player->dropItem($item);
        }

        $player->sendMessage(Translation::getInstance()->getMessage("COMMAND_TREASURE_RECEIVED", ["{PREFIX}" => Main::PREFIX, "{COUNT}" => strval($amount)]));

        $sender->sendMessage(Translation::getInstance()->getMessage("COMMAND_TREASURE_GIVE", ["{PREFIX}" => Main::PREFIX, "{COUNT}" => strval($amount), "{PLAYER}" => $player->getName()]));
    }

}

?>