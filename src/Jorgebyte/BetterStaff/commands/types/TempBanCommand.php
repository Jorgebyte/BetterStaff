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

class TempBanCommand extends Command implements PluginOwned
{
    use PluginOwnedTrait;

    public function __construct()
    {
        parent::__construct("tempban", "BetterStaff - Temporarily ban a player", null, ["tban"]);
        $this->setPermission("betterstaff.command.tempban");
        $this->setUsage("Usage: /tempban <player> <time> <reason>");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
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
            $sender->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "invalid-time"));
            SoundUtils::addSound($sender, "note.bass");
            return;
        }

        $staffName = $sender->getName();
        $banData = Main::getInstance()->getBanData();
        $banData->addBan($playerName, $time, $reason, $staffName);

        $target = Server::getInstance()->getPlayerExact($playerName);
        if ($target !== null) {
            $formatDuration = TimeUtils::formatDuration($time);
            $target->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                $prefix . ConfigUtils::getConfigValue("messages", "kick-player-ban")));
        }

        $formatDuration = TimeUtils::formatDuration($time);
        $sender->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
            $prefix . ConfigUtils::getConfigValue("messages", "staff-ban-message")));
        Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
            $prefix . ConfigUtils::getConfigValue("messages", "broadcast-ban-message")));
        SoundUtils::addSound($sender, "random.pop");
    }
}