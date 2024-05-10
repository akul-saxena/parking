<?php

/*
 * @file
 * This code generates a ticket for a vehicle entry into a parking lot.
 * 
 * @return void
 * This script echoes a message indicating success or failure of ticket generation.
 * 
 * @param string $vehicleNumber
 * The vehicle number for which the ticket is generated.
 * @param string $vehicleType
 * The type of vehicle for which the ticket is generated.
 * @param mysqli_connection $conn
 * The database connection object.
 */

require_once('../db.php');

class TicketGenerator
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Function to Generate a new Ticket
    public function generateTicket($vehicleNumber, $vehicleType)
    {
        // Find released slot for the given vehicle type
        $query = "SELECT * FROM tickets WHERE status = 'Released' AND vehicle_type = '$vehicleType'";
        $result = mysqli_query($this->conn, $query);
        if (mysqli_num_rows($result) > 0) {
            // Released slot found, assign new entry to this slot
            $row = mysqli_fetch_assoc($result);
            $slotNumber = $row['slot_number'];
            $query = "SELECT * FROM tickets WHERE status = 'Booked' AND vehicle_type = '$vehicleType' AND slot_number = '$slotNumber' LIMIT 1";
            $res = mysqli_query($this->conn, $query);
            if (mysqli_num_rows($res) > 0) {
                $query = "SELECT MAX(slot_number) AS max_slot FROM tickets WHERE vehicle_type = '$vehicleType'";
                $result = mysqli_query($this->conn, $query);
                $row = mysqli_fetch_assoc($result);
                $slotNumber = $row['max_slot'] + 1;
                $status = 'Booked';
            }
            $status = 'Booked';
        } else {
            // No released slots found, find the next available slot
            $query = "SELECT MAX(slot_number) AS max_slot FROM tickets WHERE vehicle_type = '$vehicleType'";
            $result = mysqli_query($this->conn, $query);
            $row = mysqli_fetch_assoc($result);
            $slotNumber = $row['max_slot'] + 1;
            $status = 'Booked';
        }

        // Insert new ticket
        $query = "INSERT INTO tickets (vehicle_number, vehicle_type, slot_number, time_of_entry, status) VALUES ('$vehicleNumber', '$vehicleType', '$slotNumber', NOW(), '$status')";
        if (mysqli_query($this->conn, $query)) {
            echo "Ticket generated successfully.";
        } else {
            echo "Error generating ticket.";
        }
    }
}

// Instantiate TicketGenerator object with database connection.
$ticketGenerator = new TicketGenerator($conn);

// Get input data.
$vehicleNumber = $_POST['vehicleNumber'];
$vehicleType = $_POST['vehicleType'];

// Generate ticket and send response.
echo $ticketGenerator->generateTicket($vehicleNumber, $vehicleType);
