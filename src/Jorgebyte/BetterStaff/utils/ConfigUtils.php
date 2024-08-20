<?php

namespace Jorgebyte\BetterStaff\utils;

use Jorgebyte\BetterStaff\Main;
use pocketmine\utils\Config;

class ConfigUtils
{
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
}