<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\data\MuteData;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UnTempMuteCommand extends Command
{

    public function __construct()
    {
        parent::__construct("untempmute", "BetterStaff - Unmute the player", null, ["pardonmute", "removemute", "delmute"]);
        $this->setPermission("betterstaff.command.untempmute");
        $this->setUsage("Usage: /untempmute <player>");
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
        $muteData = MuteData::getInstance();
        if (!$muteData->isMuted($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . Utils::getConfigValue("messages", "player-no-mute")));
            Utils::addSound($sender, "note.bass");
            return;
        }
        $muteData->removeMute($playerName);
        $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . Utils::getConfigValue("messages", "player-remove-mute")));
        Utils::addSound($sender, "random.pop");
    }
}