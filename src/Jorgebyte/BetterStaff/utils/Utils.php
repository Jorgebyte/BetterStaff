<?php

namespace Jorgebyte\BetterStaff\utils;

use Jorgebyte\BetterStaff\items\BanItem;
use Jorgebyte\BetterStaff\items\FreezeItem;
use Jorgebyte\BetterStaff\items\PlayerInfoItem;
use Jorgebyte\BetterStaff\items\TeleportItem;
use Jorgebyte\BetterStaff\items\VanishItem;
use Jorgebyte\BetterStaff\Main;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class Utils
{
    public Main $plugin;

    public function __construct()
    {
        $this->plugin = Main::getInstance();
    }

    public function addSound(Player $player, string $sound, $volume = 1, $pitch = 1): void
    {
        $packet = new PlaySoundPacket();
        $packet->x = $player->getPosition()->getX();
        $packet->y = $player->getPosition()->getY();
        $packet->z = $player->getPosition()->getZ();
        $packet->soundName = $sound;
        $packet->volume = $volume;
        $packet->pitch = $pitch;
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function setKitStaff(Player $player): void
    {
        $player->getInventory()->setContents(array(
            0 => new TeleportItem(),
            2 => new VanishItem(),
            3 => new FreezeItem(),
            4 => new BanItem(),
            6 => new PlayerInfoItem()
        ));
    }

    public function toggleFreeze(Player $player, Player $victim): void
    {
        $session = $this->plugin->getStaffSession();
        $prefix = $this->plugin->getMessages("prefix");

        if ($session->isStaff($victim)) {
            $player->sendMessage($prefix . $this->plugin->getMessages("staff-no-freeze"));
            return;
        }
        if ($session->isFrozen($victim)) {
            $session->removeFrozen($victim);
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . $this->plugin->getMessages("unfreeze-player")));
            $victim->sendMessage($prefix . $this->plugin->getMessages("you-unfreeze"));
            $this->plugin->getServer()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()], $prefix . $this->plugin->getMessages("broadcast-unfreeze")));
        } else {
            $session->freezePlayer($victim);
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . $this->plugin->getMessages("freeze-player")));
            $victim->sendMessage($prefix . $this->plugin->getMessages("contact-staff"));
            $this->plugin->getServer()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()], $prefix . $this->plugin->getMessages("broadcast-freeze")));
        }
    }

    // staffchat
    public function broadcastToStaff(Player $player, string $message): void
    {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $staffs) {
            if ($staffs->hasPermission("betterstaff.staffchat")) {
                $formattedMessage = str_replace(["{PLAYER}", "{MSG}"], [$player->getName(), $message], $this->plugin->getMessages("staffchat-broadcast"));
                $staffs->sendMessage($formattedMessage);
                $this->addSound($player, "random.pop");
            }
        }
    }

    // player info utils
    public function getDevice(Player $player): string
    {
        $data = $player->getPlayerInfo()->getExtraData();
        if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
            return "Linux";
        }

        return match ($data["DeviceOS"]) {
            DeviceOS::ANDROID => "Android",
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

    public function getInputMode(Player $player): string
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

    public function getPlayerInfo(Player $player, Player $victim): string
    {
        $name = $victim->getName();
        $input = $this->getInputMode($victim);
        $device = $this->getDevice($victim);
        $deviceModel = $victim->getPlayerInfo()->getExtraData()["DeviceModel"];
        $ip = $victim->getNetworkSession()->getIp();
        $ping = $victim->getNetworkSession()->getPing();

        $info = str_replace(["{PLAYER}", "{INPUT}", "{DEVICE}", "{DEVICEMODEL}", "{IP}", "{PING}"], [$name, $input, $device, $deviceModel, $ip, $ping], $this->plugin->getMessages("player-info-message"));
        $player->sendMessage($info);
        return $info;
    }
}