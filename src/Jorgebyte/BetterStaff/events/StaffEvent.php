<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\Main;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;

class StaffEvent implements Listener
{

    public Main $plugin;

    public function __construct()
    {
        $this->plugin = Main::getInstance();
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        $staffSession = $this->plugin->getStaffSession();

        if ($staffSession->isStaff($player)) {
            $staffSession->removeStaff($player);
        }
        if ($staffSession->isVanish($player)) {
            $staffSession->unvanish($player);
        }
        if ($staffSession->isFrozen($player)) {
            $staffSession->removeFrozen($player);
        }
    }

    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $msg = $event->getMessage();
        if ($this->plugin->getStaffSession()->isInStaffChat($player)) {
            $this->plugin->getUtils()->broadcastToStaff($player, $msg);
            $event->cancel();
        }
    }

    public function onPlayerDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        if ($this->plugin->getStaffSession()->isStaff($player)) {
            $event->cancel();
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        if ($this->plugin->getStaffSession()->isStaff($player)) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $player = $event->getPlayer();
        if ($this->plugin->getStaffSession()->isStaff($player)) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        if ($this->plugin->getStaffSession()->isStaff($player)) {
            $event->cancel();
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            if ($this->plugin->getStaffSession()->isStaff($entity)) {
                $event->cancel();
            }
        }
    }

    public function onEntityDeath(EntityDeathEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            $staffSession = $this->plugin->getStaffSession();
            if ($staffSession->isStaff($entity)) {
                $event->setDrops(array());
                $staffSession->removeStaff($entity);
                $prefix = $this->plugin->getMessages("prefix");
                $entity->sendMessage($prefix . $this->plugin->getMessages("staff-death"));
            }
        }
    }
}