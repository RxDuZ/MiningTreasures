<?php

namespace rxduz\treasures\extension;

class Treasure {

    public function __construct(private string $name, private int $chance, private array $commands){

    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getChance(): int {
        return $this->chance;
    }

    /**
     * @return array
     */
    public function getCommands(): array {
        return $this->commands;
    }

}

?>