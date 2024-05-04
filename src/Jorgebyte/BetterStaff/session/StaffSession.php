<?php

namespace Jorgebyte\BetterStaff\session;

use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class StaffSession
{

    private static array $staffSession;
    private static array $state;
    private static array $frozenPlayers;
    private static array $staffChat;
    private static array $vanishedPlayers;

    public static function registerStaff(Player $player): void
    {
        $playerName = $player->getName();

        self::$staffSession[$playerName] = true;
        // Save inventory
        self::$state[$playerName] = [
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
        Utils::setKitStaff($player);
    }

    public static function isStaff(Player $player): bool
    {
        return isset(self::$staffSession[$player->getName()]);
    }

    public static function removeStaff(Player $player): void
    {
        $playerName = $player->getName();

        if (isset(self::$staffSession[$playerName])) {
            if (isset(self::$state[$playerName]['inventory']) && isset(self::$state[$playerName]['armor'])) {
                $player->getInventory()->setContents(self::$state[$playerName]['inventory']);
                $player->getArmorInventory()->setContents(self::$state[$playerName]['armor']);
            }
            if (isset(self::$state[$playerName]['position']) && self::$state[$playerName]['position'] instanceof Position) {
                $player->teleport(self::$state[$playerName]['position']);
            }
            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->setGamemode(GameMode::SURVIVAL());
            $player->setFlying(false);
            $player->setAllowFlight(false);
            unset(self::$staffSession[$playerName]);
            unset(self::$state[$playerName]);
        }
    }

    public static function registerFrozen(Player $player): void
    {
        self::$frozenPlayers[$player->getName()] = true;
    }

    public static function isFrozen(Player $player): bool
    {
        return isset(self::$frozenPlayers[$player->getName()]);
    }

    public static function removeFrozen(Player $player): void
    {
        unset(self::$frozenPlayers[$player->getName()]);
    }

    public static function registerStaffChat(Player $player): void
    {
        self::$staffChat[$player->getName()] = true;
    }

    public static function isStaffChat(Player $player): bool
    {
        return isset(self::$staffChat[$player->getName()]);
    }

    public static function removeStaffChat(Player $player): void
    {
        unset(self::$staffChat[$player->getName()]);
    }

    public static function registervanish(Player $player): void
    {
        self::$vanishedPlayers[$player->getName()] = true;
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->hidePlayer($player);
                $player->setSilent(true);
            }
        }
    }

    public static function isVanish(Player $player): bool
    {
        return isset(self::$vanishedPlayers[$player->getName()]);
    }

    public static function removevanish(Player $player): void
    {
        unset(self::$vanishedPlayers[$player->getName()]);
        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if ($onlinePlayer !== $player) {
                $onlinePlayer->showPlayer($player);
                $player->setSilent(false);
            }
        }
    }
}