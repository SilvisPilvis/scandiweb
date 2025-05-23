<?php

/**
 * Model
 * php version  8.2
 *
 * @category    Database
 * @description A Class for database connections using mysqli
 * @package     App\Database
 * @author      Silvestrs Lignickis <silvestrsl47@gmail.com>
 * @license     https://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version     GIT: main
 * @link        None
 */

namespace App\Database;

use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;
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
    private ?mysqli $_conn = null;
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
     * @return mysqli
     * @throws RuntimeException if connection fails.
     */
    public function connect(): mysqli
    {
        if ($this->_conn === null) {
            // Enable mysqli exceptions
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            if (!$this->_host || !$this->_user || !$this->_password || !$this->_db_name) {
                throw new RuntimeException("Database configuration is not set");
            }

            try {
                $this->_conn = new mysqli(
                    $this->_host,
                    $this->_user,
                    $this->_password,
                    $this->_db_name
                );

                // connect_error check is somewhat redundant if MYSQLI_REPORT_STRICT
                // is on, as an exception would have been thrown.
                // Kept for explicit error message population.
                if ($this->_conn->connect_error) {
                    $this->connect_error =
                        "Connection failed: " . $this->_conn->connect_error;
                    error_log($this->connect_error);
                    throw new RuntimeException(
                        $this->connect_error,
                        $this->_conn->connect_errno
                    );
                }

                $this->_conn->set_charset("utf8mb4");
                $this->connect_error = null;
            } catch (mysqli_sql_exception $e) {
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
     * Get the mysqli connection, ensuring it's established.
     */
    private function _getConnectedInstance(): mysqli
    {
        return $this->connect();
    }

    /**
     * Helper to bind parameters to a mysqli_stmt.
     *
     * @param mysqli_stmt $stmt The statement.
     * @param array       $params Parameters to bind.
     * @return void
     */
    private function _bindParams(mysqli_stmt $stmt, array $params): void
    {
        if (empty($params)) {
            return;
        }

        $types = '';
        $bindableParams = [];

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } elseif (is_bool($param)) {
                $types .= 'i'; // Treat booleans as integers
                $param = (int) $param;
            } elseif (is_null($param)) {
                $types .= 's'; // Type doesn't strictly matter for NULL
            } else {
                $types .= 'b'; // Default to blob for other types
            }
            $bindableParams[] = $param;
        }
        $stmt->bind_param($types, ...$bindableParams);
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
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new RuntimeException(
                "Prepare failed: (" . $conn->errno . ") " . $conn->error
            );
        }

        $this->_bindParams($stmt, $params);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            // For non-SELECT queries, get_result() returns false.
            $stmt->close();
            if ($conn->error) {
                $this->error = $conn->error;
                error_log($conn->error);
                throw new RuntimeException($conn->error);
            }
            // If there's no error but no result (e.g., for INSERT/UPDATE queries)
            return [];
        }

        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
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
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new RuntimeException(
                "Prepare failed: (" . $conn->errno . ") " . $conn->error
            );
        }

        $this->_bindParams($stmt, $params);
        $stmt->execute();

        $lastId = $conn->insert_id;
        $stmt->close();

        if ($lastId > 0) {
            return (string) $lastId;
        }
        return false;
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
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new RuntimeException(
                "Prepare failed: (" . $conn->errno . ") " . $conn->error
            );
        }

        $this->_bindParams($stmt, $params);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
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
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new RuntimeException(
                "Prepare failed: (" . $conn->errno . ") " . $conn->error
            );
        }

        $this->_bindParams($stmt, $params);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

    /**
     * Prepares an SQL statement for execution.
     *
     * @param string $sql The SQL statement to prepare (must use ? placeholders).
     * @return mysqli_stmt|false
     */
    public function prepare(string $sql): mysqli_stmt|false
    {
        return $this->_getConnectedInstance()->prepare($sql);
    }

    /**
     * Database destructor.
     */
    public function __destruct()
    {
        if ($this->_conn !== null) {
            $this->_conn->close();
            $this->_conn = null;
        }
    }
}
