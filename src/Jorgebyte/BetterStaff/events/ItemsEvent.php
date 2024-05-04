<?php

namespace Jorgebyte\BetterStaff\events;

use Jorgebyte\BetterStaff\Forms;
use Jorgebyte\BetterStaff\items\BanItem;
use Jorgebyte\BetterStaff\items\FreezeItem;
use Jorgebyte\BetterStaff\items\PlayerInfoItem;
use Jorgebyte\BetterStaff\items\TeleportItem;
use Jorgebyte\BetterStaff\items\VanishItem;
use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;

class ItemsEvent implements Listener
{

    public function onPlayerUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $prefix = Utils::getConfigValue("messages", "prefix");
        if (StaffSession::isStaff($player)) {
            switch (true) {
                case $item instanceof TeleportItem:
                    Forms::teleportUI($player);
                    break;
                case $item instanceof FreezeItem:
                    Forms::freezeUI($player);
                    break;
                case $item instanceof BanItem:
                    Forms::customBanUI($player);
                    break;
                case $item instanceof VanishItem:
                    if (StaffSession::isVanish($player)) {
                        StaffSession::removevanish($player);
                        $player->sendMessage($prefix . Utils::getConfigValue("messages", "disable-vanish"));
                    } else {
                        StaffSession::registervanish($player);
                        $player->sendMessage($prefix . Utils::getConfigValue("messages", "enable-vanish"));
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
            Utils::toggleFreeze($damager, $victim);
        } elseif ($itemInHand instanceof PlayerInfoItem) {
            Utils::getPlayerInfo($damager, $victim);
        }
    }
}