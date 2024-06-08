<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\data\BanData;
use Jorgebyte\BetterStaff\data\MuteData;
use Jorgebyte\BetterStaff\utils\Forms\CustomForm;
use Jorgebyte\BetterStaff\utils\Forms\SimpleForm;
use Jorgebyte\BetterStaff\utils\Utils;
use pocketmine\player\Player;
use pocketmine\Server;

class Forms
{
    public static function teleportUI(Player $player): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        self::createSimpleForm($player, "Select a Player", "Connected players:", $playerNames, function (Player $player, string $selectedPlayerName) {
            $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayerName);
            if ($selectedPlayer instanceof Player) {
                $prefix = Utils::getPrefix();
                $teleportMessage = str_replace("{PLAYER}", $selectedPlayer->getName(), Utils::getConfigValue("messages", "teleport-success"));
                $player->teleport($selectedPlayer->getPosition());
                $player->sendMessage($prefix . $teleportMessage);
            } else {
                $player->sendMessage(Utils::getConfigValue("messages", "player-not-online"));
            }
        });
    }

    public static function freezeUI(Player $player): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        self::createSimpleForm($player, "Freeze Players", "Select a player to freeze or unfreeze:", $playerNames, function (Player $player, string $selectedPlayerName) {
            $selectedPlayer = Server::getInstance()->getPlayerExact($selectedPlayerName);
            if ($selectedPlayer instanceof Player) {
                Utils::toggleFreeze($player, $selectedPlayer);
            } else {
                $player->sendMessage(Utils::getConfigValue("messages","player-not-online"));
            }
        });
    }

    public static function customBanUI(Player $staff): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];
        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        $form = new CustomForm(function (Player $staff, ?array $data) use ($playerNames) {
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
                $formatDuration = Utils::formatDuration($totalSeconds);
                $staffName = $staff->getName();
                $banData->addBan($playerName, $totalSeconds, $reason, $staffName);
                $player = Server::getInstance()->getPlayerExact($playerName);
                $prefix = Utils::getPrefix();
                $player?->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "kick-player-ban")));
                $staff->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "staff-ban-message")));
                Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "broadcast-ban-message")));
            }
        });
        $form->setTitle("Ban Players");
        $form->addDropdown("Select a player to ban", $playerNames);
        $form->addSlider("Days", 0, 30);
        $form->addSlider("Hours", 0, 23);
        $form->addSlider("Minutes", 0, 59);
        $form->addSlider("Seconds", 0, 59);
        $form->addInput("Reason", "Enter the reason for the ban...");
        $staff->sendForm($form);
    }

    public static function customMuteUI(Player $staff): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];
        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        $form = new CustomForm(function (Player $staff, ?array $data) use ($playerNames) {
            if ($data !== null) {
                $selectedPlayerIndex = (int)$data[0];
                $playerName = $playerNames[$selectedPlayerIndex];

                $days = (int)$data[1];
                $hours = (int)$data[2];
                $minutes = (int)$data[3];
                $seconds = (int)$data[4];
                $reason = $data[5] ?? "";

                $totalSeconds = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
                $muteData = MuteData::getInstance();
                $formatDuration = Utils::formatDuration($totalSeconds);
                $staffName = $staff->getName();
                $muteData->addMute($playerName, $totalSeconds, $reason, $staffName);
                $player = Server::getInstance()->getPlayerExact($playerName);
                $prefix = Utils::getPrefix();
                $player?->sendMessage(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "mute-message")));
                $staff->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "staff-mute-message")));
                Server::getInstance()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason],
                    $prefix . Utils::getConfigValue("messages", "broadcast-mute-message")));
            }
        });
        $form->setTitle("Mute Players");
        $form->addDropdown("Select a player to mute", $playerNames);
        $form->addSlider("Days", 0, 30);
        $form->addSlider("Hours", 0, 23);
        $form->addSlider("Minutes", 0, 59);
        $form->addSlider("Seconds", 0, 59);
        $form->addInput("Reason", "Enter the reason for the mute...");
        $staff->sendForm($form);
    }

    public static function customReportUI(Player $player): void
    {
        $onlinePlayers = Server::getInstance()->getOnlinePlayers();
        $playerNames = [];
        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        $form = new CustomForm(function (Player $player, $data) use ($playerNames) {
            if ($data === null) {
                return;
            }

            $selectedPlayerIndex = (int)$data[0];
            $reportedPlayerName = $playerNames[$selectedPlayerIndex];
            $reportReason = $data[1];

            if ($reportedPlayerName === "" || $reportReason === "") {
                $player->sendMessage("ERROR: Be sure to provide a reason for the report.");
                return;
            }

            if ($reportedPlayerName === $player->getName()) {
                $player->sendMessage("ERROR: You cannot report yourself.");
                return;
            }

            foreach (Server::getInstance()->getOnlinePlayers() as $staff) {
                if ($staff->hasPermission("betterstaff.reportview")) {
                    $staff->sendMessage(str_replace(["{PLAYER}", "{REPORTED}", "{REASON}"], [$player->getName(), $reportedPlayerName, $reportReason],
                        Utils::getConfigValue("messages", "report-staff-success")));
                    Utils::addSound($staff, "random.pop");
                }
            }
            Utils::sendReportWebhook($player->getName(), $reportedPlayerName, $reportReason);
        });

        $form->setTitle("Report");
        $form->addDropdown("Select a player to report", $playerNames);
        $form->addInput("reason for the report");
        $player->sendForm($form);
    }

    public static function createSimpleForm(Player $player, string $title, string $content, array $playerNames, callable $selectionHandler): void
    {
        $form = new SimpleForm(function (Player $player, ?int $selectedPlayerIndex) use ($playerNames, $selectionHandler) {
            if ($selectedPlayerIndex !== null) {
                $selectedPlayerName = $playerNames[$selectedPlayerIndex];
                $selectionHandler($player, $selectedPlayerName);
            }
        });
        $form->setTitle($title);
        $form->setContent($content);
        foreach ($playerNames as $name) {
            $form->addButton($name);
        }
        $player->sendForm($form);
    }
}