<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class TempBanCommand extends Command
{

    public $plugin;

    public function __construct()
    {
        parent::__construct("tempban", "Temporarily ban a player", null, ["tban"]);
        $this->setPermission("betterstaff.command.tempban");
        $this->setUsage("Usage: /tempban <player> <time> <reason>");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = $this->plugin->getMessages("prefix");
        $utils = $this->plugin->getUtils();
        if (count($args) < 3) {
            $sender->sendMessage($prefix . $this->getUsage());
            $utils->addSound($sender, "note.bass");
            return;
        }

        $playerName = array_shift($args);
        $timeString = array_shift($args);
        $reason = implode(" ", $args);
        $time = $this->plugin->getBanData()->parseTime($timeString);
        if ($time === false || $time <= 0) {
            $sender->sendMessage($prefix . $this->plugin->getMessages("invalid-time"));
            $utils->addSound($sender, "note.bass");
            return;
        }

        $target = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($target === null) {
            $sender->sendMessage($prefix . $this->plugin->getMessages("player-not-online"));
            $utils->addSound($sender, "note.bass");
            return;
        }

        $staffName = $sender->getName();
        $banData = $this->plugin->getBanData();
        $banData->addBan($target->getName(), $time, $reason, $staffName);
        $formatDuration = $banData->formatDuration($time);
        $target->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason], $prefix . $this->plugin->getMessages("kick-player-ban")));
        $sender->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason], $prefix . $this->plugin->getMessages("staff-ban-message")));
        $this->plugin->getServer()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason], $prefix . $this->plugin->getMessages("broadcast-ban-message")));
        $utils->addSound($sender, "random.pop");
    }
}