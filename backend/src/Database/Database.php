<?php

/**
 * Model
 * php version  8.2
 *
 * @category    Database
 * @description A Class for database connections using PDO
 * @package     App\Database
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Class Database
 *
 * @category Database
 * @package  App\Database
 * @author   Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license  https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @link     None
 */
class Database
{
    private ?PDO $_conn = null;
    private string | null $_host;
    private string | null $_user;
    private string | null $_password;
    private string | null $_db_name;
    public string | null $connect_error;
    public string | null $error;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        $this->_host = $_ENV['DB_HOST'] ?? null;
        $this->_user = $_ENV['DB_USER'] ?? null;
        $this->_password = $_ENV['DB_PASSWORD'] ?? null;
        $this->_db_name = $_ENV['DB_DATABASE'] ?? null;
        $this->connect_error = null;
        $this->error = null;
    }

    /**
     * Connect to the database
     *
     * @return PDO
     * @throws RuntimeException if connection fails.
     */
    public function connect(): PDO
    {
        if ($this->_conn === null) {
            if (!$this->_host || !$this->_user || !$this->_password || !$this->_db_name) {
                throw new RuntimeException("Database configuration is not set");
            }

            $dsn = "mysql:host={$this->_host};dbname={$this->_db_name};charset=utf8mb4";
            try {
                $this->_conn = new PDO(
                    $dsn,
                    $this->_user,
                    $this->_password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
                $this->connect_error = null;
            } catch (PDOException $e) {
                $this->connect_error = "Connection failed: " . $e->getMessage();
                error_log($this->connect_error);
                throw new RuntimeException(
                    $this->connect_error,
                    (int) $e->getCode(),
                    $e
                );
            }
        }
        return $this->_conn;
    }

    /**
     * Get the PDO connection, ensuring it's established.
     */
    private function _getConnectedInstance(): PDO
    {
        return $this->connect();
    }

    /**
     * Query the database using a prepared statement.
     * Assumes a SELECT query. For other types, use specific methods.
     *
     * @param string $query SQL query to execute (must use ? placeholders).
     * @param array  $params Parameters for prepared statement.
     * @return array Result set as an array of associative arrays.
     * @throws RuntimeException on prepare or execution error.
     */
    public function query(string $query, array $params = []): array
    {
        $conn = $this->_getConnectedInstance();
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            throw new RuntimeException($this->error, (int) $e->getCode(), $e);
        }
    }

    /**
     * Execute an INSERT statement and return the last insert ID.
     *
     * @param string $query SQL INSERT statement (must use ? placeholders).
     * @param array  $params Parameters for prepared statement.
     * @return string|false The ID of the last inserted row, or false on failure/no ID.
     * @throws RuntimeException on prepare or execution error.
     */
    public function insert(string $query, array $params = []): string|false
    {
        $conn = $this->_getConnectedInstance();
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $lastId = $conn->lastInsertId();
            return $lastId !== "0" ? $lastId : false;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            throw new RuntimeException($this->error, (int) $e->getCode(), $e);
        }
    }

    /**
     * Execute an UPDATE statement and return the number of affected rows.
     *
     * @param string $query SQL UPDATE statement (must use ? placeholders).
     * @param array  $params Parameters for prepared statement.
     * @return int Number of affected rows.
     * @throws RuntimeException on prepare or execution error.
     */
    public function update(string $query, array $params = []): int
    {
        $conn = $this->_getConnectedInstance();
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            throw new RuntimeException($this->error, (int) $e->getCode(), $e);
        }
    }

    /**
     * Execute a DELETE statement and return the number of affected rows.
     *
     * @param string $query SQL DELETE statement (must use ? placeholders).
     * @param array  $params Parameters for prepared statement.
     * @return int Number of affected rows.
     * @throws RuntimeException on prepare or execution error.
     */
    public function delete(string $query, array $params = []): int
    {
        $conn = $this->_getConnectedInstance();
        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            throw new RuntimeException($this->error, (int) $e->getCode(), $e);
        }
    }

    /**
     * Prepares an SQL statement for execution.
     *
     * @param string $sql The SQL statement to prepare (must use ? placeholders).
     * @return \PDOStatement|false
     */
    public function prepare(string $sql): \PDOStatement|false
    {
        try {
            return $this->_getConnectedInstance()->prepare($sql);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            error_log($this->error);
            return false;
        }
    }

    /**
     * Database destructor.
     */
    public function __destruct()
    {
        $this->_conn = null; // PDO closes connection when set to null
    }
}
