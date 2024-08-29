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
use pocketmine\Server;

class TempMuteCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("tempmute", "BetterStaff - Temporary mute player", null, ["mute", "tmute"]);
        $this->setPermission("betterstaff.command.tempmute");
        $this->setUsage("Usage: /tempmute <player> <time> <reason>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $prefix = ConfigUtils::getPrefix();

        if (count($args) < 3) {
            $sender->sendMessage($prefix . $this->getUsage());
            SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $playerName = array_shift($args);
        $timeString = array_shift($args);
        $reason = implode(" ", $args);
        $time = TimeUtils::parseTime($timeString);

        if ($time === false || $time <= 0) {
            $sender->sendMessage($prefix . Utils::getConfigValue("messages", "invalid-time"));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $staffName = $sender->getName();
        $muteData = Main::getInstance()->getMuteData();
        $muteData->addMute($playerName, $time, $reason, $staffName);

        $formatDuration = TimeUtils::formatDuration($time);
        $sender->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
            $prefix . ConfigUtils::getConfigValue("messages", "staff-mute-message")));
        Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . ConfigUtils::getConfigValue("messages", "broadcast-mute-message")));
        SoundUtils::addSound($sender, "random.pop");
    }
}