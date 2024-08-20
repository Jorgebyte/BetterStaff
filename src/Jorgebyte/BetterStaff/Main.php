<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\commands\CommandManager;
use Jorgebyte\BetterStaff\listener\{
    ItemsListener,
    PlayerListener,
    StaffListener
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

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
        CommandManager::loadCommand();
        $this->registerListeners();
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