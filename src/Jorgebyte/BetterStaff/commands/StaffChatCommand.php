<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StaffChatCommand extends Command
{
    private  $plugin;

    public function __construct()
    {
        parent::__construct("staffchat", "BetterStaff - Chat with the other staff", null, ["sc", "schat", "cs"]);
        $this->setPermission("betterstaff.command.staffchat");
        $this->plugin = Main::getInstance();
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }
        $staffSession = $this->plugin->getStaffSession();
        $prefix = $this->plugin->getMessages("prefix");
        $utils = $this->plugin->getUtils();
        if ($staffSession->isInStaffChat($sender)) {
            $staffSession->leaveStaffChat($sender);
            $sender->sendMessage($prefix . $this->plugin->getMessages("leave-staffchat"));
            $utils->addSound($sender, "random.pop");
        } else {
            $staffSession->joinStaffChat($sender);
            $sender->sendMessage($prefix . $this->plugin->getMessages("join-staffchat"));
            $utils->addSound($sender, "random.pop");
        }
    }
}