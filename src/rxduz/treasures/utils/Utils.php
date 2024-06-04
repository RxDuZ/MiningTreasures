<?php

namespace rxduz\treasures\utils;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

class Utils {

	/**
	 * @param Player $player
	 * @param string $soundName
	 * @param float $volume
	 * @param float $pitch
	 */
    public static function playSound(Player $player, string $soundName, float $volume = 1.0, float $pitch = 1.0): void {
		$pk = new PlaySoundPacket();
		$pk->soundName = $soundName;
		$pk->x = (int)$player->getLocation()->asVector3()->getX();
		$pk->y = (int)$player->getLocation()->asVector3()->getY();
		$pk->z = (int)$player->getLocation()->asVector3()->getZ();
		$pk->volume = $volume;
		$pk->pitch = $pitch;
		$player->getNetworkSession()->sendDataPacket($pk);
	}

}

?>