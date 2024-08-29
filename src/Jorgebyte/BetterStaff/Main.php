<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\commands\CommandManager;
use Jorgebyte\BetterStaff\listener\{
    ItemsListener,
    PlayerListener,
    StaffListener
};
use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\data\MuteData;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;

    public BanData $banData;
    public MuteData $muteData;

    private const RESOURCES = [
        "itemnames.yml",
        "messages.yml",
        "settings.yml"
    ];

    public function onLoad(): void
    {
        self::setInstance($this);

        $this->banData = new BanData();
        $this->muteData = new MuteData();
    }

    public function onEnable(): void
    {
        self::setInstance($this);
        $this->saveResources();
        CommandManager::loadCommand();
        $this->registerListeners();
    }

    public function getBanData(): BanData
    {
        return $this->banData;
    }

    public function getMuteData(): MuteData
    {
        return $this->muteData;
    }

    private function saveResources(): void
    {
        foreach (self::RESOURCES as $resource) {
            $this->saveResource($resource);
        }
    }

    public function registerListeners(): void
    {
        $listeners = [
            new ItemsListener(),
            new StaffListener(),
            new PlayerListener(),
        ];

        foreach ($listeners as $listener) {
            $this->getServer()->getPluginManager()->registerEvents($listener, $this);
        }
    }
}