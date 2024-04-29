<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FreezeCommand extends Command
{

    private $plugin;
    public function __construct()
    {
        parent::__construct("freeze", "BetterStaff - Freeze or unfreeze a player", null, ["froze"]);
        $this->setPermission("betterstaff.command.freeze");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = $this->plugin->getMessages("prefix");
        $utils = $this->plugin->getUtils();
        if (empty($args)) {
            $this->plugin->getForms()->freezeUI($sender);
            $utils->addSound($sender, "random.pop");
            return;
        }
        $victim = $this->plugin->getServer()->getPlayerExact($args[0]);
        if (!$victim instanceof Player) {
            $sender->sendMessage($prefix . $this->plugin->getMessages("player-not-online"));
            $utils->addSound($sender, "note.bass");
            return;
        }
        $this->plugin->getUtils()->toggleFreeze($sender, $victim);
        $utils->addSound($sender, "random.pop");
    }
}