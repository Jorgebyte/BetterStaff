<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class PlayerInfoCommand extends Command
{

    public $plugin;

    public function __construct()
    {
        parent::__construct("playerinfo", "BetterStaff - Obtain information from a player", null, ["plinfo", "userinfo"]);
        $this->setPermission("betterstaff.command.playerinfo");
        $this->setUsage("Usage: /playerinfo <player>");
        $this->plugin = Main::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $prefix = $this->plugin->getMessages("prefix");
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        $victim = $this->plugin->getServer()->getPlayerExact($playerName);
        if ($victim instanceof Player) {
            $this->plugin->getUtils()->getPlayerInfo($sender, $victim);
        } else {
            $sender->sendMessage($prefix . $this->plugin->getMessages("player-not-online"));
        }
    }
}