<?php

namespace Jorgebyte\BetterStaff\commands;

use Jorgebyte\BetterStaff\Forms;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class FreezeCommand extends Command
{

    public function __construct()
    {
        parent::__construct("freeze", "BetterStaff - Freeze or unfreeze a player", null, ["froze"]);
        $this->setPermission("betterstaff.command.freeze");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = Utils::getPrefix();
        if (empty($args)) {
            Forms::freezeUI($sender);
            Utils::addSound($sender, "random.pop");
            return;
        }
        $victim = Server::getInstance()->getPlayerExact($args[0]);
        $sender->sendMessage($prefix . (
            ($victim instanceof Player)
                ? Utils::toggleFreeze($sender, $victim)
                : Utils::getConfigValue("messages", "player-not-online")
            ));
        Utils::addSound($sender, "random.pop");
    }
}