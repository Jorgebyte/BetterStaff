<?php

namespace Jorgebyte\BetterStaff\data;

use Jorgebyte\BetterStaff\Main;
use pocketmine\utils\SingletonTrait;
use SQLite3;

class MuteData
{
    use SingletonTrait;

    private $database;
    private $mutes;

    public function __construct()
    {
        $this->database = new SQLite3(Main::getInstance()->getDataFolder() . "mutes.db");
        $this->database->exec("CREATE TABLE IF NOT EXISTS mutes (player_name TEXT PRIMARY KEY, end_time INTEGER, reason TEXT, staff_name TEXT)");
        $this->loadMutes();
    }

    public function addMute(string $playerName, int $muteTimeInSeconds, string $reason, string $staffName): void
    {
        $endTime = time() + $muteTimeInSeconds;
        $statement = $this->database->prepare("INSERT OR REPLACE INTO mutes (player_name, end_time, reason, staff_name) VALUES (:player_name, :end_time, :reason, :staff_name)");
        $statement->bindValue(':player_name', $playerName, SQLITE3_TEXT);
        $statement->bindValue(':end_time', $endTime, SQLITE3_INTEGER);
        $statement->bindValue(':reason', $reason, SQLITE3_TEXT);
        $statement->bindValue(':staff_name', $staffName, SQLITE3_TEXT);
        $statement->execute();
        $this->loadMutes();
    }

    public function isMuted(string $playerName): bool
    {
        return isset($this->mutes[$playerName]) && $this->mutes[$playerName]['end_time'] > time();
    }

    public function getMuteInfo(string $playerName): ?array
    {
        return $this->isMuted($playerName) ? $this->mutes[$playerName] : null;
    }

    public function removeMute(string $playerName): void
    {
        $this->database->exec("DELETE FROM mutes WHERE player_name = '$playerName'");
        $this->loadMutes();
    }

    private function loadMutes(): void
    {
        $this->mutes = [];
        $result = $this->database->query("SELECT * FROM mutes");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->mutes[$row['player_name']] = [
                'end_time' => (int)$row['end_time'],
                'reason' => $row['reason'],
                'staff_name' => $row['staff_name']
            ];
        }
    }
}