<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class UnTempBanCommand extends Command
{

    private $plugin;

    public function __construct()
    {
        parent::__construct("untempban", "Unban the player", null, ["pardontemp", "removeban", "delban"]);
        $this->setPermission("betterstaff.command.untempban");
        $this->setUsage("Usage: /untempban <player>");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = $this->plugin->getMessages("prefix");
        $utils = $this->plugin->getUtils();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            $utils->addSound($sender, "note.bass");
            return;
        }

        $playerName = $args[0];
        $banData = $this->plugin->getBanData();
        if (!$banData->isBanned($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . $this->plugin->getMessages("player-no-ban")));
            $utils->addSound($sender, "note.bass");
            return;
        }
        $banData->removeBan($playerName);
        $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . $this->plugin->getMessages("player-remove-ban")));
        $utils->addSound($sender, "random.pop");
    }
}