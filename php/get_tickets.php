<?php

/*
 * @file
 * This script retrieves tickets information from the database.
 * 
 * @return void
 * There's no return value from this script.
 * 
 * @param mysqli_connection $conn
 * The database connection object.
 */

require_once('../db.php');

class TicketRetriever
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Function to Retreive Tickets
    public function retrieveTickets()
    {
        $query = "SELECT * FROM tickets";
        $result = mysqli_query($this->conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $tickets_html = '';
            while ($row = mysqli_fetch_assoc($result)) {
                $tickets_html .= "<li>Slot Number:{$row['slot_number']} Vehicle Number:  <span class='vehicle-number'>{$row['vehicle_number']}</span>, Time of Entry: <span class='timeOfEntry'>{$row['time_of_entry']}</span>, Time of Exit: ";
                $tickets_html .= $row['status'] == 'Booked' ? 'Still booked' : $row['time_of_exit'];
                $tickets_html .= ", Status: <span class='status'>{$row['status']}</span></li>";
            }
        } else {
            $tickets_html = "<li>No tickets found</li>";
        }

        return $tickets_html;
    }
}

// Instantiate TicketRetriever object with database connection.
$ticketRetriever = new TicketRetriever($conn);

// Retrieve tickets information.
$tickets_html = $ticketRetriever->retrieveTickets();

// Send response.
echo $tickets_html;
