<?php

namespace Jorgebyte\BetterStaff\utils;

use Jorgebyte\BetterStaff\items\{
    TeleportItem,
    FreezeItem,
    BanItem,
    MuteItem,
    VanishItem,
    PlayerInfoItem
};
use Jorgebyte\BetterStaff\session\SessionManager;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerUtils
{
    public static function setKitStaff(Player $player): void
    {
        $player->getInventory()->setContents([
            0 => new TeleportItem(),
            2 => new VanishItem(),
            3 => new FreezeItem(),
            4 => new BanItem(),
            6 => new PlayerInfoItem(),
            8 => new MuteItem()
        ]);
    }

    public static function toggleFreeze(Player $player, Player $victim): void
    {
        $prefix = ConfigUtils::getPrefix();
        $session = SessionManager::getSession($victim, 'freeze');
        if ($session !== null) {
            SessionManager::endSession($victim, 'freeze');
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . ConfigUtils::getConfigValue("messages", "unfreeze-player")));
            $victim->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "you-unfreeze"));
            Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()],
                $prefix . ConfigUtils::getConfigValue("messages", "broadcast-unfreeze")));
        } else {
            SessionManager::startSession($victim, 'freeze');
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . ConfigUtils::getConfigValue("messages", "freeze-player")));
            $victim->sendMessage($prefix . ConfigUtils::getConfigValue("messages", "contact-staff"));
            Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()],
                $prefix . ConfigUtils::getConfigValue("messages", "broadcast-freeze")));
        }
    }

    public static function broadcastToStaff(Player $player, string $message): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $staffs) {
            if ($staffs->hasPermission("betterstaff.staffchat")) {
                $formattedMessage = str_replace(["{PLAYER}", "{MSG}"], [$player->getName(), $message], ConfigUtils::getConfigValue("messages", "staffchat-broadcast"));
                $staffs->sendMessage($formattedMessage);
            }
        }
    }

    public static function getDevice(Player $player): string
    {
        $data = $player->getPlayerInfo()->getExtraData();
        return match ($data["DeviceOS"]) {
            DeviceOS::ANDROID => $data["DeviceModel"] === "" ? "Linux" : "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::OSX => "MacOS",
            DeviceOS::AMAZON => "FireOS",
            DeviceOS::GEAR_VR => "Gear VR",
            DeviceOS::HOLOLENS => "Hololens",
            DeviceOS::WINDOWS_10 => "Windows",
            DeviceOS::WIN32 => "WinEdu",
            DeviceOS::DEDICATED => "Dedicated",
            DeviceOS::TVOS => "TV OS",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo Switch",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::WINDOWS_PHONE => "Windows Phone",
            default => "Unknown"
        };
    }

    public static function getInputMode(Player $player): string
    {
        $data = $player->getPlayerInfo()->getExtraData();
        return match ($data["CurrentInputMode"]) {
            InputMode::TOUCHSCREEN => "Touch",
            InputMode::MOUSE_KEYBOARD => "Keyboard",
            InputMode::GAME_PAD => "Controller",
            InputMode::MOTION_CONTROLLER => "Motion Controller",
            default => "Unknown"
        };
    }

    public static function getPlayerInfo(Player $player, Player $victim): string
    {
        $name = $victim->getName();
        $input = self::getInputMode($victim);
        $device = self::getDevice($victim);
        $deviceModel = $victim->getPlayerInfo()->getExtraData()["DeviceModel"];
        $ip = $victim->getNetworkSession()->getIp();
        $ping = $victim->getNetworkSession()->getPing();

        $info = str_replace(["{PLAYER}", "{INPUT}", "{DEVICE}", "{DEVICEMODEL}", "{IP}", "{PING}"], [$name, $input, $device, $deviceModel, $ip, $ping],
            subject: ConfigUtils::getConfigValue("messages", "player-info-message"));
        $player->sendMessage($info);
        return $info;
    }
}
