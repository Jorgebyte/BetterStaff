<?php

namespace Jorgebyte\BetterStaff\listener;

use Jorgebyte\BetterStaff\forms\FormManager;
use Jorgebyte\BetterStaff\items\{
    TeleportItem,
    FreezeItem,
    BanItem,
    MuteItem,
    VanishItem,
    PlayerInfoItem
};
use Jorgebyte\BetterStaff\session\SessionManager;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;

class ItemsListener implements Listener
{
    public function onPlayerUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $prefix = ConfigUtils::getConfigValue("messages", "prefix");
        $session = SessionManager::getSession($player, 'staff');
        if ($session !== null) {
            switch (true) {
                case $item instanceof TeleportItem:
                    FormManager::sendForm($player, 'teleport');
                    break;
                case $item instanceof FreezeItem:
                    FormManager::sendForm($player, 'freeze');
                    break;
                case $item instanceof BanItem:
                    FormManager::sendForm($player, 'ban');
                    break;
                case $item instanceof MuteItem:
                    FormManager::sendForm($player, 'mute');
                    break;
                case $item instanceof VanishItem:
                    $session = SessionManager::getSession($player, 'vanish');
                    if ($session !== null) {
                        SessionManager::endSession($player, 'vanish');
                        $player->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "disable-vanish"));
                    } else {
                        SessionManager::startSession($player, 'vanish');
                        $player->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "enable-vanish"));
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
            PlayerUtils::toggleFreeze($damager, $victim);
        } elseif ($itemInHand instanceof PlayerInfoItem) {
            PlayerUtils::getPlayerInfo($damager, $victim);
        }
    }
}