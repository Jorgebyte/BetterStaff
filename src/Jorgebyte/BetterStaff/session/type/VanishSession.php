<?php

namespace Jorgebyte\BetterStaff\session\type;

use Jorgebyte\BetterStaff\session\Session;
use pocketmine\Server;

class VanishSession extends Session
{
    protected function init(): void
    {
        $this->registerVanish();
    }

    private function registerVanish(): void
    {
        $player = $this->getPlayer();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->hidePlayer($player);
                $player->setSilent();
            }
        }
    }

    public function endSession(): void
    {
        $player = $this->getPlayer();
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->showPlayer($player);
                $player->setSilent(false);
            }
        }
    }
}
