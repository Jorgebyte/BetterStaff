<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\Main;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class UnTempBanCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("untempban", "BetterStaff - Unban the player", null, ["pardontemp", "removeban", "delban"]);
        $this->setPermission("betterstaff.command.untempban");
        $this->setUsage("Usage: /untempban <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = ConfigUtils::getPrefix();

        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
           SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $playerName = $args[0];
        $banData = Main::getInstance()->getBanData();
        if (!$banData->isBanned($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . ConfigUtils::getConfigValue("messages", "player-no-ban")));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }
        $banData->removeBan($playerName);
        $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . ConfigUtils::getConfigValue("messages", "player-remove-ban")));
        SoundUtils::addSound($sender, "random.pop");
    }
}