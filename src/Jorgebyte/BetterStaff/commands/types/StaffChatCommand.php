<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\session\SessionManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
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
        $prefix = ConfigUtils::getPrefix();
        $session = SessionManager::getSession($sender, 'staffchat');

        if ($session !== null) {
            SessionManager::endSession($sender, 'staffchat');
        } else {
            SessionManager::startSession($sender, 'staffchat');
        }

        $messageKey = $session ? "leave-staffchat" : "join-staffchat";
        $sender->sendMessage($prefix . ConfigUtils::getConfigValue("messages", $messageKey));
        SoundUtils::addSound($sender, "random.pop");
    }
}