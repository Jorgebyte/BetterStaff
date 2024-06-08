<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\data\MuteData;
use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class PlayerEvent implements Listener
{

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $banData = BanData::getInstance();

        if ($banData->isBanned($playerName)) {
            $banInfo = $banData->getBanInfo($playerName);
            $endTime = $banInfo['end_time'];
            $remainingTime = $endTime - time();
            $formatDuration = Utils::formatDuration($remainingTime);
            $reason = $banInfo['reason'];
            $staffName = $banInfo['staff_name'];
            $prefix = Utils::getPrefix();
            $msg = str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                $prefix . Utils::getConfigValue("messages", "login-player-ban"));
            $player->kick($msg);
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $muteData = MuteData::getInstance();

        if ($muteData->isMuted($player->getName())) {
            $muteInfo = $muteData->getMuteInfo($player->getName());
            if ($muteInfo !== null) {
                $remainingTime = $muteInfo['end_time'] - time();
                $formatDuration = Utils::formatDuration($remainingTime);
                $reason = $muteInfo['reason'];
                $prefix = Utils::getPrefix();
                $message = str_replace(["{TIME}", "{REASON}"], [$formatDuration, $reason],
                    Utils::getConfigValue("messages", "mute-message"));
                $player->sendMessage($prefix . $message);
                $event->cancel();
            }
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        if (StaffSession::isFrozen($player))
        {
            $event->cancel();
        }
    }

    public function onCommand(CommandEvent $event): void
    {
        $sender = $event->getSender();
        $prefix = Utils::getPrefix();
        if (!$sender instanceof Player) {
            return;
        }
        if (StaffSession::isFrozen($sender)) {
            $command = strtolower($event->getCommand());
            $blockedCommands = (array) Utils::getConfigValue("settings", "commands-block");
            if (in_array($command, $blockedCommands)) {
                $event->cancel();
                $sender->sendMessage($prefix . Utils::getConfigValue("messages", "message-commands-block"));
            }
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $prefix = Utils::getPrefix();
        if ($entity instanceof Player && $damager instanceof Player) {
            if (StaffSession::isFrozen($damager)) {
                $event->cancel();
                $damager->sendMessage($prefix . Utils::getConfigValue("messages", "frozen-player-attack"));
                return;
            }
            if (StaffSession::isFrozen($entity)) {
                if (!StaffSession::isStaff($damager)) {
                    $event->cancel();
                    $damager->sendMessage($prefix . Utils::getConfigValue("messages", "player-attack-frozen"));
                }
            }
        }
    }
}