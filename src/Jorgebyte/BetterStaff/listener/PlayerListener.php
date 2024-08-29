<?php

namespace Jorgebyte\BetterStaff\listener;

use Jorgebyte\BetterStaff\Main;
use Jorgebyte\BetterStaff\session\SessionManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\TimeUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class PlayerListener implements Listener
{
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $playerName = strtolower($player->getName());
        $banData = Main::getInstance()->getBanData();

        if ($banData->isBanned($playerName)) {
            $banInfo = $banData->getBanInfo($playerName);
            $endTime = $banInfo['end_time'];
            $remainingTime = $endTime - time();
            $formatDuration = TimeUtils::formatDuration($remainingTime);
            $reason = $banInfo['reason'];
            $staffName = $banInfo['staff_name'];
            $prefix = ConfigUtils::getPrefix();
            $msg = str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                $prefix . ConfigUtils::getConfigValue("messages", "login-player-ban"));
            $player->kick($msg);
        }
    }

    public function onPlayerChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $playerName = strtolower($player->getName());
        $muteData = Main::getInstance()->getMuteData();

        if ($muteData->isMuted($playerName)) {
            $muteInfo = $muteData->getMuteInfo($playerName);
            if ($muteInfo !== null) {
                $remainingTime = $muteInfo['end_time'] - time();
                $formatDuration = TimeUtils::formatDuration($remainingTime);
                $reason = $muteInfo['reason'];
                $prefix = ConfigUtils::getPrefix();
                $message = str_replace(["{TIME}", "{REASON}"], [$formatDuration, $reason],
                    ConfigUtils::getConfigValue("messages", "mute-message"));
                $player->sendMessage($prefix . $message);
                $event->cancel();
            }
        }
    }

    public function onCommand(CommandEvent $event): void
    {
        $sender = $event->getSender();
        $prefix = ConfigUtils::getPrefix();
        if (!$sender instanceof Player) return;

        $freezeSession = SessionManager::getSession($sender, 'freeze');
        if ($freezeSession !== null) {
            $command = strtolower($event->getCommand());
            $blockedCommands = (array) ConfigUtils::getConfigValue("settings", "commands-block");
            if (in_array($command, $blockedCommands)) {
                $event->cancel();
                $sender->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "message-commands-block"));
            }
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $prefix = ConfigUtils::getPrefix();

        if (!($entity instanceof Player && $damager instanceof Player)) {
            return;
        }

        $damagerSession = SessionManager::getSession($damager, 'freeze');
        $entitySession = SessionManager::getSession($entity, 'freeze');

        if ($damagerSession !== null) {
            $event->cancel();
            $damager->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "frozen-player-attack"));
            return;
        }

        if ($entitySession !== null && SessionManager::getSession($damager, 'staff') === null) {
            $event->cancel();
            $damager->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "player-attack-frozen"));
        }
    }
}