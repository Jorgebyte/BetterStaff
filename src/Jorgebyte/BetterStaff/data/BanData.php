<?php

namespace Jorgebyte\BetterStaff\data;

use Jorgebyte\BetterStaff\Main;
use pocketmine\utils\SingletonTrait;
use SQLite3;

class BanData
{
    use SingletonTrait;

    private $database;
    private $bans;

    public function __construct()
    {
        $this->database = new SQLite3(Main::getInstance()->getDataFolder() . "bans.db");
        $this->database->exec("CREATE TABLE IF NOT EXISTS bans (player_name TEXT PRIMARY KEY, end_time INTEGER, reason TEXT, staff_name TEXT)");
        $this->loadBans();
    }

    public function addBan(string $playerName, int $banTimeInSeconds, string $reason, string $staffName): void
    {
        $endTime = time() + $banTimeInSeconds;
        $statement = $this->database->prepare("INSERT OR REPLACE INTO bans (player_name, end_time, reason, staff_name) VALUES (:player_name, :end_time, :reason, :staff_name)");
        $statement->bindValue(':player_name', $playerName, SQLITE3_TEXT);
        $statement->bindValue(':end_time', $endTime, SQLITE3_INTEGER);
        $statement->bindValue(':reason', $reason, SQLITE3_TEXT);
        $statement->bindValue(':staff_name', $staffName, SQLITE3_TEXT);
        $statement->execute();
        $this->loadBans();
    }

    public function isBanned(string $playerName): bool
    {
        return isset($this->bans[$playerName]) && $this->bans[$playerName]['end_time'] > time();
    }

    public function getBanInfo(string $playerName): ?array
    {
        return $this->isBanned($playerName) ? $this->bans[$playerName] : null;
    }

    public function removeBan(string $playerName): void
    {
        $this->database->exec("DELETE FROM bans WHERE player_name = '$playerName'");
        $this->loadBans();
    }

    private function loadBans(): void
    {
        $this->bans = [];
        $result = $this->database->query("SELECT * FROM bans");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->bans[$row['player_name']] = [
                'end_time' => (int)$row['end_time'],
                'reason' => $row['reason'],
                'staff_name' => $row['staff_name']
            ];
        }
    }
}