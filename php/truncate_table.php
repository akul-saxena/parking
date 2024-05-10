<?php

/*
 * @file
 * This script truncates the ticket table.
 * 
 * @return void
 * There's no return value from this script.
 * 
 * @param string $tableName
 * The name of the table to truncate.
 * @param mysqli $conn
 * The database connection.
 */

// Include the database connection file.
require_once('db.php');

/**
 * Truncate a specified database table.
 */
class TableTruncator
{
    private $tableName;
    private $conn;

    public function __construct($tableName, $conn)
    {
        $this->tableName = $tableName;
        $this->conn = $conn;
    }

    // Function to Truncate the Table
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

// Get input data.
$tableName = 'tickets';
// Instantiate TableTruncator object with database connection.
$tableTruncator = new TableTruncator($tableName, $conn);
// Truncate Table
$tableTruncator->truncateTable();
