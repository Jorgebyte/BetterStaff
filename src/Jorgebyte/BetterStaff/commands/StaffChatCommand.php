<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StaffChatCommand extends Command
{

    public function __construct()
    {
        parent::__construct("staffchat", "BetterStaff - Chat with the other staff", null, ["sc", "schat", "cs"]);
        $this->setPermission("betterstaff.command.staffchat");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }
        $prefix = Utils::getPrefix();
        if (StaffSession::isStaffChat($sender)) StaffSession::removeStaffChat($sender); else {
            StaffSession::registerStaffChat($sender);
        }
        $messageKey = StaffSession::isStaffChat($sender) ? "join-staffchat" : "leave-staffchat";
        $sender->sendMessage($prefix . Utils::getConfigValue("messages", $messageKey));
        Utils::addSound($sender, "random.pop");
    }
}