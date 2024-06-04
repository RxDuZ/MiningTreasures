<?php

namespace rxduz\treasures;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use rxduz\treasures\extension\Treasure;

class TreasureManager {

    use SingletonTrait;

    public const TREASURE_TAG = "treasure_tag";

    /** @var Treasure[] $treasures */
    private array $treasures = [];

    /** @var array $break_blocks */
    private array $break_blocks = [];

    public function init(): void {
        $data = new Config(Main::getInstance()->getDataFolder() . "/treasures.yml", Config::YAML);

        foreach($data->getAll() as $key => $value){
            $chance = intval($value["chance"]);

            if($chance > 100){
                $chance = 100;
            }

            if(isset($this->treasures[$key])){
                Main::getInstance()->getLogger()->info(Main::PREFIX . TextFormat::RED . $key . " treasure it was already added so it was ignored.");
                continue;
            }

            $this->treasures[$key] = new Treasure($key, $chance, $value["commands"]);
        }
    }

    /**
     * @return Treasure[]
     */
    public function getTreasures(): array {
        return $this->treasures;
    }

    /**
     * @param World $world
     * @return bool
     */
    public static function isAvailableWorld(World $world): bool {
        return in_array($world->getFolderName(), Main::getInstance()->getConfig()->get("available-worlds"));
    }

    /**
     * @param Block $block
     * @return bool
     */
    public static function isAvailableBlock(Block $block): bool {
        $stringToItem = StringToItemParser::getInstance();

        $id = $stringToItem->lookupAliases($block->asItem())[0] ?? "air";

        return in_array($id, Main::getInstance()->getConfig()->get("available-blocks"));
    }

    /**
     * @param Player $player
     */
    public function addBreakBlocks(Player $player): void {
        if(!isset($this->break_blocks[strtolower($player->getName())])){
            $this->break_blocks[strtolower($player->getName())] = 1;

            return;
        }

        $this->break_blocks[strtolower($player->getName())]++;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getBreakBlocks(Player $player): int {
        return $this->break_blocks[strtolower($player->getName())] ?? 0;
    }

    /**
     * @param Player $player
     */
    public function resetBreakBlocks(Player $player): void {
        if(isset($this->break_blocks[strtolower($player->getName())])) unset($this->break_blocks[strtolower($player->getName())]);
    }

    /**
     * @param int $amount
     * @return Item
     */
    public function getTreasureItem(int $amount = 1): Item {
        $stringToItem = StringToItemParser::getInstance();

        $pluginConfig = Main::getInstance()->getConfig();

        $item = $stringToItem->parse($pluginConfig->get("treasure-item"));

        if($item->isNull()){
            $item = VanillaItems::EMERALD();
        }

        $item->setCount($amount);

        $item->setCustomName(TextFormat::colorize($pluginConfig->get("treasure-item-name")));

        $item->setLore([TextFormat::colorize($pluginConfig->get("treasure-item-lore"))]);

        $item->getNamedTag()->setString(self::TREASURE_TAG, $item->getCustomName());

        return $item;
    }

    /**
     * @return Treasure|null
     */
    public function getRandomTreasure(): Treasure|null {
        $treasures = [];

        foreach($this->treasures as $name => $treasure){
            for($i = 0; $i < $treasure->getChance(); $i++){
                $treasures[] = $treasure;
            }
        }

        if(empty($treasures)){
            return null;
        }

        shuffle($treasures);

        return $treasures[0] ?? null;
    }

    /**
     * @return bool
     */
    public function decidesDropTreasure(): bool {
        $chance = intval(Main::getInstance()->getConfig()->get("chance-to-drop"));

        if($chance > 100){
            $chance = 100;
        }

        $values = [];

        $remaining = ($chance < 100 ? (100 - $chance) : 0);

        if($remaining === 0){
            return true;
        }

        for($i = 0; $i < $chance; $i++){
            $values[] = 1;
        }

        for($i = 0; $i < $remaining; $i++){
            $values[] = 0;
        }

        $rand = $values[array_rand($values)];

        return ($rand === 1);
    }

}

?>