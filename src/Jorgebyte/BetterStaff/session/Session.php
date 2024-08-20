<?php

namespace Jorgebyte\BetterStaff\session;

use pocketmine\player\Player;

abstract class Session
{
    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->init();
    }

    abstract protected function init(): void;

    abstract public function endSession(): void;

    public function getPlayer(): Player
    {
        return $this->player;
    }
}