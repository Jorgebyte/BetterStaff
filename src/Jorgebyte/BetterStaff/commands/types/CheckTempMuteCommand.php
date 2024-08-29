<?php

namespace Jorgebyte\BetterStaff\commands\types;

use Jorgebyte\BetterStaff\Main;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\SoundUtils;
use Jorgebyte\BetterStaff\utils\TimeUtils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;

class CheckTempMuteCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("checktempmute", "BetterStaff - Check the mute status of the players", null, ["viewtempmute", "checkmute"]);
        $this->setPermission("betterstaff.command.staff");
        $this->setUsage("Usage: /checktempmute <player>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $prefix = ConfigUtils::getPrefix();

        if (empty($args)) {
            $sender->sendMessage($prefix . $this->getUsage());
            return;
        }

        $playerName = $args[0];
        $muteData = Main::getInstance()->getMuteData();
        $muteInfo = $muteData->getMuteInfo($playerName);
        if (!$muteData->isMuted($playerName) || $muteInfo === null) {
            $messageKey = !$muteData->isMuted($playerName) ? "player-no-mute" : "error-player-check";
            $sender->sendMessage(str_replace("{PLAYER}", $playerName, $prefix . ConfigUtils::getConfigValue("messages", $messageKey)));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $endTime = $muteInfo['end_time'];
        $remainingTime = $endTime - time();
        $formatDuration = TimeUtils::formatDuration($remainingTime);
        $reason = $muteInfo['reason'];
        $staffName = $muteInfo['staff_name'];
        $msg = str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . ConfigUtils::getConfigValue("messages", "player-check-mute"));
        $sender->sendMessage($msg);
        SoundUtils::addSound($sender, "random.pop");
    }
}