<?php

namespace Jorgebyte\BetterStaff\session\type;

use Jorgebyte\BetterStaff\session\Session;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use pocketmine\player\GameMode;
use pocketmine\world\Position;

class StaffSession extends Session
{
    private array $state;

    protected function init(): void
    {
        $player = $this->getPlayer();

        $this->state = [
            "inventory" => $player->getInventory()->getContents(),
            "armor" => $player->getArmorInventory()->getContents(),
            "position" => $player->getPosition()
        ];

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setGamemode(GameMode::SURVIVAL());
        $player->setAllowFlight(true);
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood(20);
        PlayerUtils::setKitStaff($player);
    }

    public function endSession(): void
    {
        $player = $this->getPlayer();
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setGamemode(GameMode::SURVIVAL());
        $player->setFlying(false);
        $player->setAllowFlight(false);

        if (isset($this->state['inventory']) && isset($this->state['armor'])) {
            $player->getInventory()->setContents($this->state['inventory']);
            $player->getArmorInventory()->setContents($this->state['armor']);
            if (isset($this->state['position']) && $this->state['position'] instanceof Position) {
                $player->teleport($this->state['position']);
            }
        }
    }
}