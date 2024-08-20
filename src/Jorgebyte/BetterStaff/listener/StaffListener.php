<?php

namespace Jorgebyte\BetterStaff\listener;

use Jorgebyte\BetterStaff\session\SessionManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class StaffListener implements Listener
{
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        SessionManager::endSession($player);
    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        $staffSession = SessionManager::getSession($player, 'staffchat');
        if ($staffSession !== null) {
            PlayerUtils::broadcastToStaff($player, $msg);
            $event->cancel();
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $staffSession = SessionManager::getSession($player, 'staff');
        if ($staffSession !== null) {
            $event->cancel();
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        $staffSession = SessionManager::getSession($player, 'staff');
        if ($staffSession !== null) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        $staffSession = SessionManager::getSession($player, 'staff');
        if ($staffSession !== null) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $staffSession = SessionManager::getSession($player, 'staff');
        if ($staffSession !== null) {
            $event->cancel();
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $staffSession = SessionManager::getSession($entity, 'staff');
            if ($staffSession !== null) {
                $event->cancel();
            }
        }
    }

    public function onEntityDeath(EntityDeathEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $staffSession = SessionManager::getSession($entity, 'staff');
            if ($staffSession !== null) {
                $event->setDrops([]);
                SessionManager::endSession($entity, 'staff');
                $prefix = ConfigUtils::getConfigValue("messages", "prefix");
                $entity->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "staff-death"));
            }
        }
    }
}