<?php

// Include the database connection file.
require_once('db.php');

/**
 * Truncate a specified database table.
 */
class TableTruncator
{
    private $tableName;
    private $conn;

    /**
     * Constructs a new TableTruncator object.
     *
     * @param string $tableName
     *   The name of the table to truncate.
     * @param mysqli $conn
     *   The database connection.
     */
    public function __construct($tableName, $conn)
    {
        $this->tableName = $tableName;
        $this->conn = $conn;
    }

    /**
     * Truncate the table.
     */
    public function truncateTable()
    {
        $sql = "TRUNCATE TABLE " . $this->tableName;
        if ($this->conn->query($sql) === true) {
            echo "Table " . $this->tableName . " truncated successfully.";
        } else {
            echo "Error truncating table: " . $this->conn->error;
        }
        $this->conn->close();
    }
}
$tableName = 'tickets';
$tableTruncator = new TableTruncator($tableName, $conn);
$tableTruncator->truncateTable();
