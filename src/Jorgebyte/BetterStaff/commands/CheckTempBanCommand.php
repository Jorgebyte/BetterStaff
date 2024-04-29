<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CheckTempBanCommand extends Command
{

    private $plugin;

    public function __construct()
    {
        parent::__construct("checktempban", "BetterStaff - Check the banning of the players", null, ["viewtempban", "checkban"]);
        $this->setPermission("betterstaff.command.staff");
        $this->setUsage("Usage: /checktempban <player>");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $banData = $this->plugin->getBanData();
        $prefix = $this->plugin->getMessages("prefix");
        $utils = $this->plugin->getUtils();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        if (!$banData->isBanned($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . $this->plugin->getMessages("player-no-ban")));
            $utils->addSound($sender, "note.bass");
            return;
        }

        $banInfo = $banData->getBanInfo($playerName);

        if ($banInfo === null) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . $this->plugin->getMessages("error-player-check")));
            $utils->addSound($sender, "note.bass");
            return;
        }

        $endTime = $banInfo['end_time'];
        $remainingTime = $endTime - time();
        $formatDuration = $banData->formatDuration($remainingTime);
        $reason = $banInfo['reason'];
        $staffName = $banInfo['staff_name'];
        $msg = str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason], $prefix . $this->plugin->getMessages("player-check"));
        $sender->sendMessage($msg);
        $utils->addSound($sender, "random.pop");
    }
}