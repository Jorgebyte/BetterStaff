<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class BetterStaffCommand extends Command
{

    private $plugin;

    public function __construct()
    {
        parent::__construct("betterstaff", "BetterStaff - Start in administrator mode", null, ["staff", "mod"]);
        $this->setPermission("betterstaff.command.staff");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be run in the game.");
            return;
        }

        $prefix = $this->plugin->getMessages("prefix");
        $staffSession = $this->plugin->getStaffSession();
        $utils = $this->plugin->getUtils();
        if ($staffSession->isStaff($sender)) {
            $staffSession->removeStaff($sender);
            $sender->sendMessage($prefix . $this->plugin->getMessages("exit-staffmode-message"));
            $utils->addSound($sender, "random.pop");
        } else {
            $staffSession->registerStaff($sender);
            $sender->sendMessage($prefix . $this->plugin->getMessages("join-staffmode-message"));
            $utils->addSound($sender, "random.pop");
        }
    }
}