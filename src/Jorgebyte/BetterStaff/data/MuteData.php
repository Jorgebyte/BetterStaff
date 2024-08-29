<?php

namespace Jorgebyte\BetterStaff\data;

use Jorgebyte\BetterStaff\data\trait\DataTrait;
use Jorgebyte\BetterStaff\Main;
use SQLite3;

class MuteData
{
    use DataTrait;

    private const CACHE_EXPIRATION = 300;

    public function __construct()
    {
        $this->tableName = 'mutes';
        $this->database = new SQLite3(Main::getInstance()->getDataFolder() . "{$this->tableName}.db");
        $this->createTable();
        $this->loadData();
    }

    public function addMute(string $playerName, int $muteTimeInSeconds, string $reason, string $staffName): void
    {
        $playerName = strtolower($playerName);
        $endTime = time() + $muteTimeInSeconds;
        $query = "INSERT OR REPLACE INTO mutes (player_name, end_time, reason, staff_name) VALUES (:player_name, :end_time, :reason, :staff_name)";
        $params = [
            ':player_name' => $playerName,
            ':end_time' => $endTime,
            ':reason' => $reason,
            ':staff_name' => $staffName
        ];
        $stmt = $this->prepareStatement($query, $params);
        $stmt->execute();
        $this->loadData();
    }

    public function isMuted(string $playerName): bool
    {
        $playerName = strtolower($playerName);
        $this->checkCache();
        return isset($this->data[$playerName]) && $this->data[$playerName]['end_time'] > time();
    }

    public function getMuteInfo(string $playerName): ?array
    {
        $playerName = strtolower($playerName);
        $this->checkCache();
        return $this->isMuted($playerName) ? $this->data[$playerName] : null;
    }

    public function removeMute(string $playerName): void
    {
        $playerName = strtolower($playerName);
        $query = "DELETE FROM mutes WHERE player_name = :player_name";
        $params = [':player_name' => $playerName];
        $stmt = $this->prepareStatement($query, $params);
        $stmt->execute();
        $this->loadData();
    }

    private function checkCache(): void
    {
        static $lastCheck = 0;
        if (time() - $lastCheck > self::CACHE_EXPIRATION) {
            $this->loadData();
            $lastCheck = time();
        }
    }
}