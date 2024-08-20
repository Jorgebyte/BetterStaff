<?php

namespace Jorgebyte\BetterStaff\forms\types;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use Vecnavium\FormsUI\SimpleForm;

class TeleportForm extends SimpleForm
{
    public function __construct()
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        parent::__construct(function (Player $player, ?int $selectedPlayerIndex) use ($playerNames) {
            if ($selectedPlayerIndex !== null) {
                $selectedPlayerName = $playerNames[$selectedPlayerIndex];
                $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayerName);
                if ($selectedPlayer instanceof Player) {
                    $prefix = ConfigUtils::getPrefix();
                    $teleportMessage = str_replace("{PLAYER}", $selectedPlayer->getName(), ConfigUtils::getConfigValue("messages", "teleport-success"));
                    $player->teleport($selectedPlayer->getPosition());
                    $player->sendMessage($prefix . $teleportMessage);
                } else {
                    $player->sendMessage(ConfigUtils::getConfigValue("messages", "player-not-online"));
                }
            }
        });

        $this->setTitle("Select a Player");
        $this->setContent("Connected players:");
        foreach ($playerNames as $name) {
            $this->addButton($name);
        }
    }
}