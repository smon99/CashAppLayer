<?php declare(strict_types=1);

namespace App\Global\Persistence;

use PDO;

class SqlConnector
{
    public ?PDO $pdo = null;
    public string $dbName;

    public function __construct()
    {
        $this->dbName = $_ENV['DATABASE'] ?? 'cash';

        $host = 'localhost:3336';
        $user = 'root';
        $password = 'nexus123';

        $connection = $this->pdo = new PDO("mysql:host=$host;dbname=$this->dbName", $user, $password);
    }

    public function executeSelectAllQuery($query): array
    {
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $params
     * @return false|string
     */
    public function execute(string $query, array $params): string|false
    {
        $statement = $this->pdo->prepare($query);

        foreach ($params as $param => $value) {
            $statement->bindValue($param, $value, PDO::PARAM_STR);
        }

        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }
}
