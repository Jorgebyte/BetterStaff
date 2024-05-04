<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class CheckTempBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("checktempban", "BetterStaff - Check the banning of the players", null, ["viewtempban", "checkban"]);
        $this->setPermission("betterstaff.command.staff");
        $this->setUsage("Usage: /checktempban <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = Utils::getPrefix();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        $banData = BanData::getInstance();
        $banInfo = $banData->getBanInfo($playerName);
        if (!$banData->isBanned($playerName) || $banInfo === null) {
            $messageKey = !$banData->isBanned($playerName) ? "player-no-ban" : "error-player-check";
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . Utils::getConfigValue("messages", $messageKey)));
            Utils::addSound($sender, "note.bass");
            return;
        }

        $endTime = $banInfo['end_time'];
        $remainingTime = $endTime - time();
        $formatDuration = Utils::formatDuration($remainingTime);
        $reason = $banInfo['reason'];
        $staffName = $banInfo['staff_name'];
        $msg = str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . Utils::getConfigValue("messages", "player-check"));
        $sender->sendMessage($msg);
        Utils::addSound($sender, "random.pop");
    }
}