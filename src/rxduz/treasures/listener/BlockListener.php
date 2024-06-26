<?php

namespace rxduz\treasures\listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\Server;
use rxduz\treasures\Main;
use rxduz\treasures\translation\Translation;
use rxduz\treasures\TreasureManager;
use rxduz\treasures\utils\Utils;

class BlockListener implements Listener {

    /**
     * @param BlockBreakEvent $ev
     * 
     * @priority HIGHEST
     */
    public function onBreakBlock(BlockBreakEvent $ev){
        if($ev->isCancelled()) return;

        $player = $ev->getPlayer();

        if(TreasureManager::isAvailableWorld($player->getWorld())){
            $block = $ev->getBlock();

            if(TreasureManager::isAvailableBlock($block)){
                TreasureManager::getInstance()->addBreakBlocks($player);

                $counterBlocks = TreasureManager::getInstance()->getBreakBlocks($player);

                if($counterBlocks === Main::getInstance()->getConfig()->get("blocks-amount-to-drop")){
                    if(TreasureManager::getInstance()->decidesDropTreasure()){
                        if($player->getInventory()->canAddItem(TreasureManager::getInstance()->getTreasureItem())){
                            $player->getInventory()->addItem(TreasureManager::getInstance()->getTreasureItem());
                        } else {
                            $player->dropItem(TreasureManager::getInstance()->getTreasureItem());
                        }

                        Utils::playSound($player, Main::getInstance()->getConfig()->get("found-treasure-sound"));

                        if(Main::getInstance()->getConfig()->get("broadcast-message-to-find", false)) Server::getInstance()->broadcastMessage(Translation::getInstance()->getMessage("PLAYER_FOUND_TREASURE", ["{PREFIX}" => Main::PREFIX, "{PLAYER}" => $player->getName()]));
                    }

                    TreasureManager::getInstance()->resetBreakBlocks($player);
                }
            }
        }
    }
    
}

?>