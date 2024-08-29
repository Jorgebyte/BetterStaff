<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\Main;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class UnTempMuteCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("untempmute", "BetterStaff - Unmute the player", null, ["pardonmute", "removemute", "delmute"]);
        $this->setPermission("betterstaff.command.untempmute");
        $this->setUsage("Usage: /untempmute <player>");
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
        $muteData = Main::getInstance()->getMuteData();
        if (!$muteData->isMuted($playerName)) {
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . ConfigUtils::getConfigValue("messages", "player-no-mute")));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }
        $muteData->removeMute($playerName);
        $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . ConfigUtils::getConfigValue("messages", "player-remove-mute")));
        SoundUtils::addSound($sender, "random.pop");
    }
}