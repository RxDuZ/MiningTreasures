<?php

namespace rxduz\treasures\listener;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;
use rxduz\treasures\extension\Treasure;
use rxduz\treasures\Main;
use rxduz\treasures\translation\Translation;
use rxduz\treasures\TreasureManager;
use rxduz\treasures\utils\Utils;

class PlayerListener implements Listener {

    /**
     * @param PlayerQuitEvent $ev
     */
    public function onQuit(PlayerQuitEvent $ev): void {
        TreasureManager::getInstance()->resetBreakBlocks($ev->getPlayer());
    }

    /**
     * @param PlayerInteractEvent $ev
     */
    public function onInteract(PlayerInteractEvent $ev): void {
        $item = $ev->getItem();

        if($item->getNamedTag()->getTag(TreasureManager::TREASURE_TAG) !== null) $ev->cancel();
    }

    /**
     * @param PlayerItemUseEvent $ev
     */
    public function onItemUse(PlayerItemUseEvent $ev): void {
        $player = $ev->getPlayer();

        $item = $ev->getItem();

        if($item->getNamedTag()->getTag(TreasureManager::TREASURE_TAG) !== null){
            $treasure = TreasureManager::getInstance()->getRandomTreasure();

            if($treasure instanceof Treasure){
                foreach($treasure->getCommands() as $command){
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), str_replace("{PLAYER}", '"' . $player->getName() . '"', $command));
                }
    
                $player->getInventory()->removeItem($item->setCount(1));

                $player->sendMessage(Translation::getInstance()->getMessage("PLAYER_OPEN_TREASURE", ["{PREFIX}" => Main::PREFIX, "{TREASURE}" => $treasure->getName()]));

                Utils::playSound($player, Main::getInstance()->getConfig()->get("open-treasure-sound"));
            }
        }
    }

}

?>