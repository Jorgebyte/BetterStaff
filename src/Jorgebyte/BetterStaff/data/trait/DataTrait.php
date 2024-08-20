<?php

namespace Jorgebyte\BetterStaff\data\trait;

use Jorgebyte\BetterStaff\Main;
use SQLite3;
use SQLite3Stmt;

trait DataTrait
{
    private SQLite3 $database;
    private string $tableName;
    private array $data = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
        $this->database = new SQLite3(Main::getInstance()->getDataFolder() . "{$tableName}.db");
        $this->createTable();
        $this->loadData();
    }

    private function createTable(): void
    {
        $this->database->exec("CREATE TABLE IF NOT EXISTS {$this->tableName} (
            player_name TEXT PRIMARY KEY, 
            end_time INTEGER, 
            reason TEXT, 
            staff_name TEXT
        )");
        $this->database->exec("CREATE INDEX IF NOT EXISTS idx_player_name ON {$this->tableName} (player_name)");
    }

    protected function prepareStatement(string $query, array $params): SQLite3Stmt
    {
        $stmt = $this->database->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        return $stmt;
    }

    protected function executeQuery(string $query): void
    {
        if (!$this->database->exec($query)) {
            throw new \RuntimeException('ERROR: Database query failed: ' . $this->database->lastErrorMsg());
        }
    }

    protected function loadData(): void
    {
        $this->data = [];
        $result = $this->database->query("SELECT * FROM {$this->tableName}");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->data[$row['player_name']] = $row;
        }
    }

    public function getData(): array
    {
        return $this->data;
    }
}