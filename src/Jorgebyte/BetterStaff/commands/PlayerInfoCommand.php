<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerInfoCommand extends Command
{


    public function __construct()
    {
        parent::__construct("playerinfo", "BetterStaff - Obtain information from a player", null, ["plinfo", "userinfo"]);
        $this->setPermission("betterstaff.command.playerinfo");
        $this->setUsage("Usage: /playerinfo <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command can only be used in-game.");
            return;
        }

        $prefix = Utils::getPrefix();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        $victim = Server::getInstance()->getPlayerExact($playerName);
        $sender->sendMessage($prefix . (
            ($victim instanceof Player)
                ? Utils::getPlayerInfo($sender, $victim)
                : Utils::getConfigValue("messages", "player-not-online")
            ));
    }
}