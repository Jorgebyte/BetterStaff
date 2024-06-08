<?php

namespace Jorgebyte\BetterStaff\utils;

use Jorgebyte\BetterStaff\items\BanItem;
use Jorgebyte\BetterStaff\items\FreezeItem;
use Jorgebyte\BetterStaff\items\MuteItem;
use Jorgebyte\BetterStaff\items\PlayerInfoItem;
use Jorgebyte\BetterStaff\items\TeleportItem;
use Jorgebyte\BetterStaff\items\VanishItem;
use Jorgebyte\BetterStaff\Main;
use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\webhook\DiscordWebhook;
use Jorgebyte\BetterStaff\utils\webhook\Embed;
use Jorgebyte\BetterStaff\utils\webhook\Message;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;
use pocketmine\utils\Config;

class Utils
{

    public static function addSound(Player $player, string $sound, $volume = 1, $pitch = 1): void
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

    public static function getConfigValue(string $fileName, string $key): mixed
    {
        $filePath = Main::getInstance()->getDataFolder() . $fileName . ".yml";
        $file = new Config($filePath, Config::YAML);
        return $file->get($key);
    }

    public static function getPrefix(): string
    {
        return self::getConfigValue("messages", "prefix");
    }

    public static function setKitStaff(Player $player): void
    {
        $player->getInventory()->setContents(array(0 => new TeleportItem(), 2 => new VanishItem(), 3 => new FreezeItem(), 4 => new BanItem(), 6 => new PlayerInfoItem(), 8 => new MuteItem()));
    }

    public static function toggleFreeze(Player $player, Player $victim): void
    {
        $prefix = self::getConfigValue("messages", "prefix");

        if (StaffSession::isStaff($victim)) {
            $player->sendMessage($prefix . self::getConfigValue("messages", "staff-no-freeze"));
            return;
        }
        if (StaffSession::isFrozen($victim)) {
            StaffSession::removeFrozen($victim);
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . self::getConfigValue("messages", "unfreeze-player")));
            $victim->sendMessage($prefix . self::getConfigValue("messages", "you-unfreeze"));
            Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()], $prefix . self::getConfigValue("messages", "broadcast-unfreeze")));
        } else {
            StaffSession::registerFrozen($victim);
            $player->sendMessage(str_replace("{PLAYER}", $victim->getName(), $prefix . self::getConfigValue("messages", "freeze-player")));
            $victim->sendMessage($prefix . self::getConfigValue("messages", "contact-staff"));
            Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}"], [$victim->getName(), $player->getName()], $prefix . self::getConfigValue("messages", "broadcast-freeze")));
        }
    }

    public static function parseTime(string $timeString): int|false
    {
        $multipliers = [
            's' => 1,
            'm' => 60,
            'h' => 3600,
            'd' => 86400
        ];

        $unit = strtolower(substr($timeString, -1));
        $value = intval(substr($timeString, 0, -1));

        if (isset($multipliers[$unit])) {
            return $value * $multipliers[$unit];
        }
        return false;
    }

    public static function formatDuration(int $seconds): string
    {
        $units = [
            'd' => floor($seconds / 86400),
            'h' => floor(($seconds % 86400) / 3600),
            'm' => floor(($seconds % 3600) / 60),
            's' => $seconds % 60
        ];

        $formattedDuration = [];
        foreach ($units as $unit => $value) {
            if ($value > 0) {
                $formattedDuration[] = "$value " . ($unit == 's' ? 'second' . ($value !== 1 ? 's' : '') : $unit);
            }
        }
        return implode(' ', $formattedDuration);
    }

    // staffchat
    public static function broadcastToStaff(Player $player, string $message): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $staffs) {
            if ($staffs->hasPermission("betterstaff.staffchat")) {
                $formattedMessage = str_replace(["{PLAYER}", "{MSG}"], [$player->getName(), $message], self::getConfigValue("messages", "staffchat-broadcast"));
                $staffs->sendMessage($formattedMessage);
            }
        }
    }

    // player info utils
    public static function getDevice(Player $player): string
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
            subject: self::getConfigValue("messages", "player-info-message"));
        $player->sendMessage($info);
        return $info;
    }

    public static function sendReportWebhook(string $reporter, string $reportedPlayer, string $reason): void
    {
        $webhookURL = self::getConfigValue("settings", "webhook-url");
        $webhook = new DiscordWebhook($webhookURL);
        $message = new Message();
        $message->setUsername(self::getConfigValue("settings", "webhook-username"));
        $message->setContent(self::getConfigValue("settings", "webhook-content"));
        $embed = new Embed();
        $embed->setTitle(self::getConfigValue("settings", "webhook-title"));
        $embed->addField(self::getConfigValue("settings", "webhook-reporter"), $reporter);
        $embed->addField(self::getConfigValue("settings", "webhook-reported"), $reportedPlayer);
        $embed->addField(self::getConfigValue("settings", "webhook-reason"), $reason);
        $embed->setColor(0xFF0000);
        $message->addEmbed($embed);
        $webhook->send($message);
    }
}