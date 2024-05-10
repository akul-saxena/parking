<?php

// Include database connection.
require_once('../db.php');
/*
 * @file
 * This script calculates and returns the availability of parking slots for different vehicle types.
 * 
 * @return string
 * A formatted response indicating the availability of parking slots for 2 wheelers and 4 wheelers.
 * 
 * @param mysqli_connection $conn
 * The database connection object.
 * 
 */
class Availability
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Function to get Availability
    public function getAvailability()
    {
        $query = "SELECT COUNT(*) AS total_slots, vehicle_type FROM tickets WHERE status = 'Booked' GROUP BY vehicle_type";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $availability = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $availability[$row['vehicle_type']] = $row['total_slots'];
            }
            $response = "2 Wheeler Slots Available: " . (100 - ($availability['2-wheeler'] ?? 0)) . ", 4 Wheeler Slots Available: " . (100 - ($availability['4-wheeler'] ?? 0));
        } else {
            $response = "2 Wheeler Slots Available: 100, 4 Wheeler Slots Available: 100";
        }

        return $response;
    }
}

// Instantiate Availability object with database connection.
$Availability = new Availability($conn);

// Get availability and send response.
echo $Availability->getAvailability();
