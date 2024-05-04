<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BetterStaffCommand extends Command
{

    public function __construct()
    {
        parent::__construct("betterstaff", "BetterStaff - Start in administrator mode", null, ["staff", "mod"]);
        $this->setPermission("betterstaff.command.staff");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be run in the game.");
            return;
        }

        if (StaffSession::isStaff($sender)) StaffSession::removeStaff($sender); else {
            StaffSession::registerStaff($sender);
        }

        $prefix = Utils::getPrefix();
        $messageKey = StaffSession::isStaff($sender) ? "join-staffmode-message" : "exit-staffmode-message";
        $message = Utils::getConfigValue("messages", $messageKey);
        $sender->sendMessage($prefix . $message);
        Utils::addSound($sender, "random.pop");
    }
}