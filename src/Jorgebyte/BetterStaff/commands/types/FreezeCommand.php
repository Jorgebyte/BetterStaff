<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\forms\FormManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\Server;

class FreezeCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("freeze", "BetterStaff - Freeze or unfreeze a player", null, ["froze"]);
        $this->setPermission("betterstaff.command.freeze");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = ConfigUtils::getPrefix();
        if (empty($args)) {
          FormManager::sendForm($sender, 'freeze');
            SoundUtils::addSound($sender, "random.pop");
            return;
        }

        $victim = Server::getInstance()->getPlayerExact($args[0]);

        if ($victim instanceof Player) PlayerUtils::toggleFreeze($sender, $victim); else {
            $sender->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "player-not-online"));
        }

        SoundUtils::addSound($sender, "random.pop");
    }
}
