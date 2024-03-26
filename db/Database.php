<?php

namespace framework\core\db;

use framework\core\Application;

class Database
{
    public \PDO $pdo;


    public function __construct(array $config)
    {
        try {
            $this->pdo = new \PDO($config['dsn'], $config['user'], $config['pass']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        catch(\PDOException $e) {
            die($e->getMessage());
        }
    }


    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }


    public function applyMigrations(): void
    {
        // iterate through all migrations and execute them one after another.
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = scandir(Application::$ROOT_DIR.'/migrations');

        $toApplyMigrations = array_diff($files, $appliedMigrations);
        $newMigrations = [];

        foreach ($toApplyMigrations as $migration) {
            if($migration === '.' || $migration === '..') {
                continue;
            }

            require_once Application::$ROOT_DIR."/migrations/".$migration;
            $className = "framework\migrations\\".pathinfo($migration, PATHINFO_FILENAME);

            $instance = new $className();

            $this->log("Applying migration $migration".PHP_EOL);
            $instance->up()? $this->log("success") : $this->log("Problem");
            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        }
        else {
            $this->log("All migrations have been applied!!");
        }
    }


    public function saveMigrations(array $migrations): void
    {
        $migrations = array_map(fn($m) => "('$m')", $migrations);
        $str = implode(',', $migrations);
        $stm = $this->pdo->prepare("INSERT INTO migrations(migration) VALUES $str");
        $stm->execute();
    }


    public function createMigrationsTable(): void
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;      
        ");
    }


    public function getAppliedMigrations(): array
    {
        // this function returns the migrations that already been created.
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations");
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }


    protected function log($message): void
    {
        echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }

}