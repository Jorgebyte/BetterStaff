<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class PlayerEvent implements Listener
{
    public Main $plugin;

    public function __construct()
    {
        $this->plugin = Main::getInstance();
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        $banData = $this->plugin->getBanData();

        if ($banData->isBanned($playerName)) {
            $banInfo = $banData->getBanInfo($playerName);
            $endTime = $banInfo['end_time'];
            $remainingTime = $endTime - time();
            $formatDuration = $banData->formatDuration($remainingTime);
            $reason = $banInfo['reason'];
            $staffName = $banInfo['staff_name'];
            $prefix = $this->plugin->getMessages("prefix");
            $msg = str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason], $prefix . $this->plugin->getMessages("login-player-ban"));
            $player->kick($msg);
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event): void
    {
        $player = $event->getPlayer();
        if ($this->plugin->getStaffSession()->isFrozen($player))
        {
            $event->cancel();
        }
    }

    public function onCommand(CommandEvent $event): void
    {
        $sender = $event->getSender();
        $prefix = $this->plugin->getMessages("prefix");
        if (!$sender instanceof Player) {
            return;
        }
        if ($this->plugin->getStaffSession()->isFrozen($sender)) {
            $command = strtolower($event->getCommand());
            $blockedCommands = (array) $this->plugin->getSettings("commands-block");
            if (in_array($command, $blockedCommands)) {
                $event->cancel();
                $sender->sendMessage($prefix . $this->plugin->getMessages("message-commands-block"));
            }
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        $prefix = $this->plugin->getMessages("prefix");
        if ($entity instanceof Player && $damager instanceof Player) {
            $staffSession = $this->plugin->getStaffSession();
            if ($staffSession->isFrozen($damager)) {
                $event->cancel();
                $damager->sendMessage($prefix . $this->plugin->getMessages("frozen-player-attack"));
                return;
            }
            if ($staffSession->isFrozen($entity)) {
                if (!$staffSession->isStaff($damager)) {
                    $event->cancel();
                    $damager->sendMessage($prefix . $this->plugin->getMessages("player-attack-frozen"));
                }
            }
        }
    }
}