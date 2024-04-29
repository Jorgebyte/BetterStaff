<?php

namespace Jorgebyte\BetterStaff\session;

use Jorgebyte\BetterStaff\Main;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;

class StaffSession
{

    private array $staffSession;
    private array $inventory;
    private array $frozenPlayers;
    private array $staffChat;
    private array $vanishedPlayers;

    public function registerStaff(Player $player): void
    {
        $this->staffSession[$player->getName()] = true;
        // save inventory
        $this->inventory[$player->getName()] = array(
            "inventory" => $player->getInventory()->getContents(),
            "armor" => $player->getArmorInventory()->getContents()
        );
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->setGamemode(GameMode::SURVIVAL());
        $player->setAllowFlight(true);
        $player->setHealth($player->getMaxHealth());
        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
        Main::getInstance()->getUtils()->setKitStaff($player);
    }

    public function isStaff(Player $player): bool
    {
        return isset($this->staffSession[$player->getName()]);
    }

    public function removeStaff(Player $player): void
    {
        if (isset($this->staffSession[$player->getName()])) {
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->setGamemode(GameMode::SURVIVAL());
            $player->setFlying(false);
            $player->setAllowFlight(false);
            // restore inventory
            $player->getInventory()->setContents($this->inventory[$player->getName()]["inventory"]);
            $player->getArmorInventory()->setContents($this->inventory[$player->getName()]["armor"]);
            unset($this->staffSession[$player->getName()]);
            unset($this->inventory[$player->getName()]);
        }
    }

    public function freezePlayer(Player $player): void
    {
        $this->frozenPlayers[$player->getName()] = true;
    }

    public function isFrozen(Player $player): bool
    {
        return isset($this->frozenPlayers[$player->getName()]);
    }

    public function removeFrozen(Player $player): void
    {
        unset($this->frozenPlayers[$player->getName()]);
    }

    public function joinStaffChat(Player $player): void
    {
        $this->staffChat[$player->getName()] = true;
    }

    public function isInStaffChat(Player $player): bool
    {
        return isset($this->staffChat[$player->getName()]);
    }

    public function leaveStaffChat(Player $player): void
    {
        unset($this->staffChat[$player->getName()]);
    }

    public function vanish(Player $player): void
    {
        $this->vanishedPlayers[$player->getName()] = true;
        foreach (Server::getInstance()->getOnlinePlayers()  as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->hidePlayer($player);
                $player->setSilent(true);
            }
        }
    }

    public function isVanish(Player $player): bool
    {
        return isset($this->vanishedPlayers[$player->getName()]);
    }

    public function unvanish(Player $player): void
    {
        unset($this->vanishedPlayers[$player->getName()]);
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->showPlayer($player);
                $player->setSilent(false);
            }
        }
    }
}