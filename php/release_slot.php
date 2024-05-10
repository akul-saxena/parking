<?php

/*
 * @file
 * This script releases a parking slot for a vehicle.
 * 
 * @return void
 * There's no return value from this script.
 * 
 * @param string $vehicleNumber
 * The vehicle number for which the parking slot is released.
 * @param mysqli_connection $conn
 * The database connection object.
 */

// Include database connection.
require_once('../db.php');

class ParkingSlotRelease
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Function to release a Parking Slot
    public function releaseParkingSlot($vehicleNumber)
    {
        $query = "UPDATE tickets SET status = 'Released', time_of_exit = NOW() WHERE vehicle_number = '$vehicleNumber' AND status = 'Booked'";
        if (mysqli_query($this->conn, $query)) {
            return "Slot released successfully.";
        } else {
            return "Error releasing slot.";
        }
    }
}

// Instantiate ParkingSlotRelease object with database connection.
$parkingSlotRelease = new ParkingSlotRelease($conn);

// Get input data.
$vehicleNumber = $_POST['vehicleNumber'];

// Release parking slot and send response.
echo $parkingSlotRelease->releaseParkingSlot($vehicleNumber);
