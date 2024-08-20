<?php

namespace Jorgebyte\BetterStaff\forms\types;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\utils\ConfigUtils;
use Jorgebyte\BetterStaff\utils\TimeUtils;
use pocketmine\player\Player;
use pocketmine\Server;
use Vecnavium\FormsUI\CustomForm;

class CustomBanForm extends CustomForm
{
    public function __construct()
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        parent::__construct(function (Player $staff, ?array $data) use ($playerNames) {
            if ($data !== null) {
                $selectedPlayerIndex = (int)$data[0];
                $playerName = $playerNames[$selectedPlayerIndex];

                $days = (int)$data[1];
                $hours = (int)$data[2];
                $minutes = (int)$data[3];
                $seconds = (int)$data[4];
                $reason = $data[5] ?? "";

                $totalSeconds = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
                $banData = BanData::getInstance();
                $formatDuration = TimeUtils::formatDuration($totalSeconds);
                $staffName = $staff->getName();
                $banData->addBan($playerName, $totalSeconds, $reason, $staffName);
                $player = Server::getInstance()->getPlayerExact($playerName);
                $prefix = ConfigUtils::getPrefix();
                $player?->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                    $prefix . ConfigUtils::getConfigValue("messages", "kick-player-ban")));
                $staff->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
                    $prefix . ConfigUtils::getConfigValue("messages", "staff-ban-message")));
                Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
                    $prefix . ConfigUtils::getConfigValue("messages", "broadcast-ban-message")));
            }
        });

        $this->setTitle("Ban Players");
        $this->addDropdown("Select a player to ban", $playerNames);
        $this->addSlider("Days", 0, 30);
        $this->addSlider("Hours", 0, 23);
        $this->addSlider("Minutes", 0, 59);
        $this->addSlider("Seconds", 0, 59);
        $this->addInput("Reason", "Enter the reason for the ban...");
    }
}