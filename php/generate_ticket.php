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

// Include database connection.
require_once('../db.php');

class TicketGenerator
{
	private $conn;

	public function __construct($conn)
	{
		$this->conn = $conn;
	}

	public function generateTicket($vehicleNumber, $vehicleType)
	{
		global $conn;
		// Find released slots for the given vehicle type
		$query = "SELECT * FROM tickets WHERE status = 'Released' AND vehicle_type = '$vehicleType'";
		$result = mysqli_query($conn, $query);
		if (mysqli_num_rows($result) > 0) {
			// Loop through each released slot
			while ($row = mysqli_fetch_assoc($result)) {
				$slotNumber = $row['slot_number'];
				// Check if the slot is not already booked
				$query = "SELECT * FROM tickets WHERE status = 'Booked' AND vehicle_type = '$vehicleType' AND slot_number = '$slotNumber' LIMIT 1";
				$res = mysqli_query($conn, $query);
				// If the slot is not already booked, assign a new entry
				if (mysqli_num_rows($res) == 0) {
					$status = 'Booked';
					// Insert new ticket
					$query = "INSERT INTO tickets (vehicle_number, vehicle_type, slot_number, time_of_entry, status) VALUES ('$vehicleNumber', '$vehicleType', '$slotNumber', NOW(), '$status')";
					if (mysqli_query($conn, $query)) {
						echo "Ticket generated successfully.";
						return;
						// Exit the function after generating the ticket
					} else {
						echo "Error generating ticket.";
						return;
						// Exit the function if there's an error
					}
				}
			}
		}
		// If no released slots are found or all released slots are already booked, find the next available slot
		$query = "SELECT MAX(slot_number) AS max_slot FROM tickets WHERE vehicle_type = '$vehicleType'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		$slotNumber = $row['max_slot'] + 1;
		$status = 'Booked';
		// Insert new ticket
		$query = "INSERT INTO tickets (vehicle_number, vehicle_type, slot_number, time_of_entry, status) VALUES ('$vehicleNumber', '$vehicleType', '$slotNumber', NOW(), '$status')";
		if (mysqli_query($conn, $query)) {
			echo "Ticket generated successfully.";
		} else {
			echo "Error generating ticket.";
		}
	}
}

// Instantiate TicketGenerator object
$ticketGenerator = new TicketGenerator($conn);

// Get input data
$vehicleNumber = $_POST['vehicleNumber'];
$vehicleType = $_POST['vehicleType'];

// Generate ticket
$ticketGenerator->generateTicket($vehicleNumber, $vehicleType);
