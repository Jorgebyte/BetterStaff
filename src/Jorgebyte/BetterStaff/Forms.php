<?php

namespace Jorgebyte\BetterStaff;

use Jorgebyte\BetterStaff\utils\Forms\CustomForm;
use Jorgebyte\BetterStaff\utils\Forms\SimpleForm;
use pocketmine\player\Player;

class Forms
{
    public function teleportUI(Player $player): void
    {
        $plugin = Main::getInstance();
        $onlinePlayers = $plugin->getServer()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        $form = new SimpleForm(function (Player $player, ?int $selectedPlayerIndex) use ($plugin, $playerNames) {
            if ($selectedPlayerIndex !== null) {
                $selectedPlayerName = $playerNames[$selectedPlayerIndex];
                $selectedPlayer = $plugin->getServer()->getPlayerExact($selectedPlayerName);

                if ($selectedPlayer instanceof Player) {
                    $prefix = $plugin->getMessages("prefix");
                    $teleportMessage = str_replace("{PLAYER}", $selectedPlayer->getName(), $plugin->getMessages("teleport-success"));
                    $player->teleport($selectedPlayer->getPosition());
                    $player->sendMessage($prefix . $teleportMessage);
                } else {
                    $player->sendMessage($plugin->getMessages("player-not-online"));
                }
            }
        });

        $form->setTitle("Select a Player");
        $form->setContent("Connected players:");
        foreach ($playerNames as $name) {
            $form->addButton($name);
        }
        $player->sendForm($form);
    }

    public function freezeUI(Player $player): void
    {
        $plugin = Main::getInstance();
        $onlinePlayers = $plugin->getServer()->getOnlinePlayers();
        $playerNames = [];

        foreach ($onlinePlayers as $onlinePlayer) $playerNames[] = $onlinePlayer->getName();

        $form = new SimpleForm(function (Player $player, ?int $selectedPlayerIndex) use ($plugin, $playerNames) {
            if ($selectedPlayerIndex !== null) {
                $selectedPlayerName = $playerNames[$selectedPlayerIndex];
                $selectedPlayer = $plugin->getServer()->getPlayerExact($selectedPlayerName);
                if ($selectedPlayer instanceof Player) {
                    $plugin->getUtils()->toggleFreeze($player, $selectedPlayer);
                } else {
                    $player->sendMessage($plugin->getMessages("player-not-online"));
                }
            }
        });
        $form->setTitle("Freeze Players");
        $form->setContent("Select a player to freeze or unfreeze:");
        foreach ($playerNames as $name) {
            $form->addButton($name);
        }
        $player->sendForm($form);
    }

    public function customBanUI(Player $staff): void
    {
        $plugin = Main::getInstance();
        $onlinePlayers = $plugin->getServer()->getOnlinePlayers();
        $playerNames = [];
        foreach ($onlinePlayers as $onlinePlayer) {
            $playerNames[] = $onlinePlayer->getName();
        }
        $form = new CustomForm(function (Player $staff, ?array $data) use ($plugin, $playerNames) {
            if ($data !== null) {
                $selectedPlayerIndex = (int)$data[0];
                $playerName = $playerNames[$selectedPlayerIndex];

                $days = (int)$data[1];
                $hours = (int)$data[2];
                $minutes = (int)$data[3];
                $seconds = (int)$data[4];
                $reason = $data[5] ?? "";

                $totalSeconds = $days * 86400 + $hours * 3600 + $minutes * 60 + $seconds;
                $banData = $plugin->getBanData();
                $formatDuration = $banData->formatDuration($totalSeconds);
                $staffName = $staff->getName();
                $banData->addBan($playerName, $totalSeconds, $reason, $staffName);
                $player = $plugin->getServer()->getPlayerExact($playerName);
                $prefix = $plugin->getMessages("prefix");
                $player?->kick(str_replace(["{STAFF}", "{TIME}", "{REASON}"], [$staffName, $formatDuration, $reason], $prefix . $plugin->getMessages("kick-player-ban")));
                $staff->sendMessage(str_replace(["{PLAYER}", "{TIME}", "{REASON}"], [$playerName, $formatDuration, $reason], $prefix . $plugin->getMessages("staff-ban-message")));
                $plugin->getServer()->broadcastMessage(str_replace(["{PLAYER}", "{STAFF}", "{TIME}", "{REASON}"], [$playerName, $staffName, $formatDuration, $reason], $prefix . $plugin->getMessages("broadcast-ban-message")));
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
}