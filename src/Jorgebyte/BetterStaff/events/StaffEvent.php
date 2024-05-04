<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
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

class StaffEvent implements Listener
{

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        if (StaffSession::isStaff($player)) {
            StaffSession::removeStaff($player);
        }
        if (StaffSession::isVanish($player)) {
            StaffSession::removevanish($player);
        }
        if (StaffSession::isFrozen($player)) {
            StaffSession::removeFrozen($player);
        }
    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        if (StaffSession::isStaffChat($player)) {
            Utils::broadcastToStaff($player, $msg);
            $event->cancel();
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        if (StaffSession::isStaff($player)) {
            $event->cancel();
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        if (StaffSession::isStaff($player)) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        if (StaffSession::isStaff($player)) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        if (StaffSession::isStaff($player)) {
            $event->cancel();
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (StaffSession::isStaff($entity)) {
                $event->cancel();
            }
        }
    }

    public function onEntityDeath(EntityDeathEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if (StaffSession::isStaff($entity)) {
                $event->setDrops(array());
                StaffSession::removeStaff($entity);
                $prefix = Utils::getConfigValue("messages", "prefix");
                $entity->sendMessage($prefix . Utils::getConfigValue("messages", "staff-death"));
            }
        }
    }
}