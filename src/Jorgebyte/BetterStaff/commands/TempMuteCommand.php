<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\data\MuteData;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class TempMuteCommand extends Command
{

    public function __construct()
    {
        parent::__construct("tempmute", "BetterStaff - Temporary mute player", null, ["mute", "tmute"]);
        $this->setPermission("betterstaff.command.tempmute");
        $this->setUsage("Usage: /tempmute <player> <time> <reason>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
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
        $muteData = MuteData::getInstance();
        $muteData->addMute($target->getName(), $time, $reason, $staffName);
        $formatDuration = Utils::formatDuration($time);
        $sender->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "staff-mute-message")));
        Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "broadcast-mute-message")));
        Utils::addSound($sender, "random.pop");
    }
}