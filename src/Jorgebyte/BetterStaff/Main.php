<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\commands\BetterStaffCommand;
use Jorgebyte\BetterStaff\commands\CheckTempBanCommand;
use Jorgebyte\BetterStaff\commands\FreezeCommand;
use Jorgebyte\BetterStaff\commands\PlayerInfoCommand;
use Jorgebyte\BetterStaff\commands\StaffChatCommand;
use Jorgebyte\BetterStaff\commands\TempBanCommand;
use Jorgebyte\BetterStaff\commands\UnTempBanCommand;
use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\events\ItemsEvent;
use Jorgebyte\BetterStaff\events\PlayerEvent;
use Jorgebyte\BetterStaff\events\StaffEvent;
use Jorgebyte\BetterStaff\session\StaffSession;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
    use SingletonTrait;

    private StaffSession $staffSession;
    private BanData $banData;

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->staffSession = new StaffSession();
        $this->banData = new BanData($this);
        $this->saveResource("itemnames.yml");
        $this->saveResource("messages.yml");
        $this->saveResource("settings.yml");
        $this->saveResource("bans.db");
        $this->getLogger()->info(TextFormat::GREEN . "BetterStaff Enable");
        $this->getServer()->getCommandMap()->registerAll("BetterStaff", array(
            new BetterStaffCommand(),
            new FreezeCommand(),
            new StaffChatCommand(),
            new TempBanCommand(),
            new UnTempBanCommand(),
            new CheckTempBanCommand(),
            new PlayerInfoCommand()
        ));
        $pluginManager = $this->getServer()->getPluginManager();
        $pluginManager->registerEvents(new ItemsEvent(), $this);
        $pluginManager->registerEvents(new StaffEvent(), $this);
        $pluginManager->registerEvents(new PlayerEvent(), $this);
    }

    public function getItemNames(string $itemNames): string
    {
        $file = new Config($this->getDataFolder() . "itemnames.yml", Config::YAML);
        return $file->get($itemNames);
    }

    public function getMessages(string $messages): string
    {
        $file = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        return $file->get($messages);
    }

    public function getSettings(string $settings)
    {
        $file = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        return $file->get($settings);
    }

    public function getUtils(): Utils
    {
        return new Utils();
    }

    public function getForms(): Forms
    {
        return new Forms();
    }

    public function getStaffSession(): StaffSession
    {
        return $this->staffSession;
    }

    public function getBanData(): BanData
    {
        return $this->banData;
    }
}