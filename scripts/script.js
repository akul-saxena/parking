$(document).ready(function () {
  // Load initial data on page load
  loadAvailability();
  loadTickets();

  // Generate Ticket form submission
  $("#generate-ticket-form").on("submit", function (event) {
    event.preventDefault();
    var vehicleNumber = $("#vehicle-number").val();
    var vehicleType = $("#vehicle-type").val();
    if (validateVehicleNumber(vehicleNumber)) {
      generateTicket(vehicleNumber, vehicleType);
    } else {
      alert(
        "Invalid vehicle number format. Please enter a valid vehicle number."
      );
    }
  });

  // Function to validate vehicle number format
  function validateVehicleNumber(vehicleNumber) {
    // Regular expression to match vehicle number format
    var regex = /^[A-Z]{2}\s?[0-9]{2}\s?[A-Z]{2}\s?[0-9]{4}$/;
    return regex.test(vehicleNumber);
  }

  // Release Slot form submission
  $("#release-slot-form").on("submit", function (event) {
    event.preventDefault();
    var releaseVehicleNumber = $("#release-vehicle-number").val();
    if (validateVehicleNumber(releaseVehicleNumber)) {
      releaseSlot(releaseVehicleNumber);
    } else {
      alert(
        "Invalid vehicle number format. Please enter a valid vehicle number."
      );
    }
  });

  // Function to load availability data
  function loadAvailability() {
    $.ajax({
      url: "../php/get_availability.php",
      type: "GET",
      success: function (response) {
        $("#availability-info").text(response);
      },
      error: function (xhr, status, error) {
        console.error("Error loading availability:", error);
      },
    });
  }

  // Function to load tickets data
  function loadTickets() {
    $.ajax({
      url: "../php/get_tickets.php",
      type: "GET",
      success: function (response) {
        $("#tickets-list").html(response);
        // Check for expired tickets after loading tickets
        checkExpiredTickets();
      },
      error: function (xhr, status, error) {
        console.error("Error loading tickets:", error);
      },
    });
  }

  // Function to generate ticket
  function generateTicket(vehicleNumber, vehicleType) {
    $.ajax({
      url: "../php/generate_ticket.php",
      type: "POST",
      data: { vehicleNumber: vehicleNumber, vehicleType: vehicleType },
      success: function (response) {
        console.log(response);
        loadTickets();
        loadAvailability();
      },
      error: function (xhr, status, error) {
        console.error("Error generating ticket:", error);
      },
    });
  }

  // Function to release slot
  function releaseSlot(releaseVehicleNumber) {
    $.ajax({
      url: "../php/release_slot.php",
      type: "POST",
      data: { vehicleNumber: releaseVehicleNumber },
      success: function (response) {
        loadTickets();
        loadAvailability();
      },
      error: function (xhr, status, error) {
        console.error("Error releasing slot:", error);
      },
    });
  }

  // Function to check for expired tickets
  function checkExpiredTickets() {
    // Iterate through each ticket element and check if it's expired
    $("#tickets-list")
      .find("li")
      .each(function () {
        var ticket = $(this);
        var timeOfEntry = new Date(ticket.find(".timeOfEntry").text());
        var currentTime = new Date();
        var timeDifference = currentTime - timeOfEntry;

        // If the time difference exceeds 2 hours, mark the ticket as expired
        if (
          timeDifference >= 7200000 &&
          ticket.find(".status").text() == "Booked"
        ) {
          // Update the ticket status to "Released"
          ticket.find(".status").text("Released");
          // Send AJAX request to release the slot in the database
          releaseSlot(ticket.find(".vehicle-number").text());
        }
      });
  }

  // Function to periodically check for expired tickets
  setInterval(function () {
    checkExpiredTickets();
  }, 3600000);
  // Check every hour (adjust interval as needed)

  // Function to truncate the table at midnight
  function truncateTableAtMidnight() {
    // Set interval to check time every minute
    setInterval(function () {
      var currentTime = new Date();
      // Check if it's midnight
      if (currentTime.getHours() === 0 && currentTime.getMinutes() === 0) {
        // Perform table truncation here
        truncateTable();
      }
    }, 60000); // Check every minute
  }
  // Function to truncate the table
  function truncateTable() {
    $.ajax({
      url: "../php/truncate_table.php",
      type: "POST", // Change to your appropriate HTTP method
      success: function (response) {
        console.log("Table truncated successfully");
        // Reload any necessary data after truncating the table
        loadAvailability();
        loadTickets();
      },
      error: function (xhr, status, error) {
        console.error("Error truncating table:", error);
      },
    });
  }

  // Call the function to start checking for midnight
  truncateTableAtMidnight();
});
