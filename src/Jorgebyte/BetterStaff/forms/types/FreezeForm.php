<?php

namespace Jorgebyte\BetterStaff\forms\types;

use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\PlayerUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use Vecnavium\FormsUI\SimpleForm;

class FreezeForm extends SimpleForm
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
                    PlayerUtils::toggleFreeze($player, $selectedPlayer);
                } else {
                    $player->sendMessage(ConfigUtils::getConfigValue("messages", "player-not-online"));
                }
            }
        });

        $this->setTitle("Freeze Players");
        $this->setContent("Select a player to freeze or unfreeze:");
        foreach ($playerNames as $name) {
            $this->addButton($name);
        }
    }
}