<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class TempBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tempban", "BetterStaff - Temporarily ban a player", null, ["tban"]);
        $this->setPermission("betterstaff.command.tempban");
        $this->setUsage("Usage: /tempban <player> <time> <reason>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = Utils::getPrefix();
        if (count($args) < 3) {
            $sender->sendMessage($prefix . $this->getUsage());
            Utils::addSound($sender, "note.bass");
            return;
        }

        $playerName = array_shift($args);
        $timeString = array_shift($args);
        $reason = implode(" ", $args);
        $time = Utils::parseTime($timeString);
        if ($time === false || $time <= 0) {
            $sender->sendMessage($prefix . Utils::getConfigValue("messages", "invalid-time"));
            Utils::addSound($sender, "note.bass");
            return;
        }

        $target = Server::getInstance()->getPlayerExact($playerName);
        if ($target === null) {
            $sender->sendMessage($prefix . Utils::getConfigValue("messages", "player-not-online"));
            Utils::addSound($sender, "note.bass");
            return;
        }

        $staffName = $sender->getName();
        $banData = BanData::getInstance();
        $banData->addBan($target->getName(), $time, $reason, $staffName);
        $formatDuration = Utils::formatDuration($time);
        $target->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "kick-player-ban")));
        $sender->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "staff-ban-message")));
        Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "broadcast-ban-message")));
        Utils::addSound($sender, "random.pop");
    }
}