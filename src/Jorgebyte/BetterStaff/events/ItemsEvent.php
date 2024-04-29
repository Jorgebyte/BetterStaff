<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\items\BanItem;
use Jorgebyte\BetterStaff\items\FreezeItem;
use Jorgebyte\BetterStaff\items\PlayerInfoItem;
use Jorgebyte\BetterStaff\items\TeleportItem;
use Jorgebyte\BetterStaff\items\VanishItem;
use Jorgebyte\BetterStaff\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;

class ItemsEvent implements Listener
{
    public Main $plugin;

    public function __construct()
    {
        $this->plugin = Main::getInstance();
    }

    public function onPlayerUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $session = $this->plugin->getStaffSession();
        $prefix = $this->plugin->getMessages("prefix");
        if ($session->isStaff($player)) {
            switch (true) {
                case $item instanceof TeleportItem:
                    $this->plugin->getForms()->teleportUI($player);
                    break;
                case $item instanceof FreezeItem:
                    $this->plugin->getForms()->freezeUI($player);
                    break;
                case $item instanceof BanItem:
                    $this->plugin->getForms()->customBanUI($player);
                    break;
                case $item instanceof VanishItem:
                    if ($session->isVanish($player)) {
                        $session->unvanish($player);
                        $player->sendMessage($prefix . $this->plugin->getMessages("disable-vanish"));
                    } else {
                        $session->vanish($player);
                        $player->sendMessage($prefix . $this->plugin->getMessages("enable-vanish"));
                    }
                    break;
            }
        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if (!$damager instanceof Player) {
            return;
        }
        $itemInHand = $damager->getInventory()->getItemInHand();
        if ($itemInHand instanceof FreezeItem) {
            $this->plugin->getUtils()->toggleFreeze($damager, $victim);
        } elseif ($itemInHand instanceof PlayerInfoItem) {
            $this->plugin->getutils()->getPlayerInfo($damager, $victim);
        }
    }
}