<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;

class PlayerInfoCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

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

        $prefix = ConfigUtils::getPrefix();
        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        $victim = Server::getInstance()->getPlayerExact($playerName);
        $sender->sendMessage($prefix . (
            ($victim instanceof Player)
                ? PlayerUtils::getPlayerInfo($sender, $victim)
                : ConfigUtils::getConfigValue("messages", "player-not-online")
            ));
    }
}