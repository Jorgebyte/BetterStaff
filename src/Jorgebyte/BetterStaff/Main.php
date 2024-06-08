<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\commands\BetterStaffCommand;
use Jorgebyte\BetterStaff\commands\CheckTempBanCommand;
use Jorgebyte\BetterStaff\commands\CheckTempMuteCommand;
use Jorgebyte\BetterStaff\commands\FreezeCommand;
use Jorgebyte\BetterStaff\commands\PlayerInfoCommand;
use Jorgebyte\BetterStaff\commands\ReportCommand;
use Jorgebyte\BetterStaff\commands\StaffChatCommand;
use Jorgebyte\BetterStaff\commands\StaffListCommand;
use Jorgebyte\BetterStaff\commands\TempBanCommand;
use Jorgebyte\BetterStaff\commands\TempMuteCommand;
use Jorgebyte\BetterStaff\commands\UnTempBanCommand;
use Jorgebyte\BetterStaff\commands\UnTempMuteCommand;
use Jorgebyte\BetterStaff\events\ItemsEvent;
use Jorgebyte\BetterStaff\events\PlayerEvent;
use Jorgebyte\BetterStaff\events\StaffEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Main extends PluginBase
{
    use SingletonTrait;

    private const RESOURCES = [
        "itemnames.yml",
        "messages.yml",
        "settings.yml"
    ];

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->saveResources();
        $this->getLogger()->info(TextFormat::GREEN . "BetterStaff");
        $this->registerCommands();
        $this->registerEvents();
    }

    private function saveResources(): void
    {
        foreach (self::RESOURCES as $resource) {
            $this->saveResource($resource);
        }
    }


    private function registerCommands(): void
    {
        $commands = [
            new BetterStaffCommand(),
            new FreezeCommand(),
            new StaffChatCommand(),
            new TempBanCommand(),
            new UnTempBanCommand(),
            new CheckTempBanCommand(),
            new PlayerInfoCommand(),
            new ReportCommand(),
            new StaffListCommand(),
            new TempMuteCommand(),
            new CheckTempMuteCommand(),
            new UnTempMuteCommand()
        ];
        $this->getServer()->getCommandMap()->registerAll("BetterStaff", $commands);
    }

    private function registerEvents(): void
    {
        $pluginManager = $this->getServer()->getPluginManager();
        $pluginManager->registerEvents(new ItemsEvent(), $this);
        $pluginManager->registerEvents(new StaffEvent(), $this);
        $pluginManager->registerEvents(new PlayerEvent(), $this);
    }
}