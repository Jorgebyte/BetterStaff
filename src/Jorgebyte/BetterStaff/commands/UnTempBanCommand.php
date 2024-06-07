<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UnTempBanCommand extends Command
{

    public function __construct()
    {
        parent::__construct("untempban", "BetterStaff - Unban the player", null, ["pardontemp", "removeban", "delban"]);
        $this->setPermission("betterstaff.command.untempban");
        $this->setUsage("Usage: /untempban <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = Utils::getPrefix();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
           Utils::addSound($sender, "note.bass");
            return;
        }

        $playerName = $args[0];
        $banData = BanData::getInstance();
        if (!$banData->isBanned($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . Utils::getConfigValue("messages", "player-no-ban")));
            Utils::addSound($sender, "note.bass");
            return;
        }
        $banData->removeBan($playerName);
        $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . Utils::getConfigValue("messages", "player-remove-ban")));
        Utils::addSound($sender, "random.pop");
    }
}