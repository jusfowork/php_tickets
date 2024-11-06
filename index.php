<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order</title>
    <style>
        .ticket-row {
            margin-bottom: 10px;
        }
        .barcode {
            font-weight: bold;
            color: green;
        }
    </style>
    <script>
        // Function to generate ticket rows based on ticket type and quantity
        function generateTicketRows(ticketType) {
            // Get container for ticket rows (adult or kid based on ticketType)
            const container = document.getElementById(ticketType + '_tickets_container');
            container.innerHTML = ''; // Clear previous rows

            // Get ticket quantity, defaulting to 0 if input is empty or invalid
            const quantity = parseInt(document.getElementById(ticketType + '_quantity').value) || 0;
            
            // Define ticket options based on ticket type (adult or kid)
            const ticketOptions = ticketType === 'adult' 
                ? ['Взрослый билет', 'Льготный билет', 'Групповой билет']
                : ['Детский билет', 'Льготный билет', 'Групповой билет'];

            // Get default ticket price based on ticket type (adult or kid)
            const defaultPrice = ticketType === 'adult' 
                ? parseFloat(document.getElementById('ticket_adult_price').value) 
                : parseFloat(document.getElementById('ticket_kid_price').value);

            // Loop to create the specified number of ticket rows
            for (let i = 1; i <= quantity; i++) {
                const row = document.createElement('div');
                row.className = 'ticket-row'; // Assign row styling
                
                const label = document.createElement('label');
                label.textContent = `Ticket ${i}: `;
                row.appendChild(label);

                // Create dropdown for selecting ticket type
                const select = document.createElement('select');
                select.name = ticketType + '_ticket_type[]'; // Assign name attribute for form submission
                ticketOptions.forEach(optionText => {
                    const option = document.createElement('option');
                    option.value = optionText;
                    option.textContent = optionText;
                    select.appendChild(option);
                });
                row.appendChild(select);

                // Input for individual ticket price, with initial value as the default price
                const priceInput = document.createElement('input');
                priceInput.type = 'number';
                priceInput.name = ticketType + '_ticket_price[]'; // Name for form submission
                priceInput.step = "0.01"; // Price input with two decimal steps
                priceInput.value = defaultPrice.toFixed(2); // Set default price formatted to two decimals
                priceInput.oninput = updateTotalPrice; // Trigger total price update on input change
                row.appendChild(priceInput);

                // Add completed row to the container
                container.appendChild(row);
            }
            updateTotalPrice(); // Update total price whenever rows are generated
        }

        // Function to calculate and display total price for selected tickets
        function updateTotalPrice() {
            // Get quantities for adult and kid tickets
            const adultQuantity = parseInt(document.getElementById('adult_quantity').value) || 0;
            const kidQuantity = parseInt(document.getElementById('kid_quantity').value) || 0;

            // Collect adult ticket prices as an array up to specified quantity
            const adultPrices = Array.from(document.getElementsByName('adult_ticket_price[]'))
                .slice(0, adultQuantity)
                .map(input => parseFloat(input.value) || 0);

            // Collect kid ticket prices as an array up to specified quantity
            const kidPrices = Array.from(document.getElementsByName('kid_ticket_price[]'))
                .slice(0, kidQuantity)
                .map(input => parseFloat(input.value) || 0);

            // Calculate total prices for adult and kid tickets
            const totalAdultPrice = adultPrices.reduce((sum, price) => sum + price, 0);
            const totalKidPrice = kidPrices.reduce((sum, price) => sum + price, 0);

            // Calculate and display overall price
            const overallPrice = totalAdultPrice + totalKidPrice;
            document.getElementById('overall_price').textContent = 'Total Price: ' + overallPrice.toFixed(2);
        }
    </script>
</head>
<body>

<h1>Order Form</h1>

<!-- Order form section -->
<form action="" method="post">
    <!-- Input fields for event details and ticket prices/quantities -->
    <label for="event_id">Event ID:</label>
    <input type="text" id="event_id" name="event_id" required><br><br>
    
    <label for="event_date">Event Date:</label>
    <input type="date" id="event_date" name="event_date" required><br><br>
    
    <label for="ticket_adult_price">Adult Ticket Price:</label>
    <input type="number" id="ticket_adult_price" name="ticket_adult_price" step="0.01" required onchange="updateTotalPrice()"><br><br>
    
    <label for="adult_quantity">Adult Ticket Quantity:</label>
    <input type="number" id="adult_quantity" name="ticket_adult_quantity" min="0" required onchange="generateTicketRows('adult'); updateTotalPrice()"><br><br>
    
    <!-- Container for dynamically generated adult ticket rows -->
    <div id="adult_tickets_container"></div>

    <label for="ticket_kid_price">Kid Ticket Price:</label>
    <input type="number" id="ticket_kid_price" name="ticket_kid_price" step="0.01" required onchange="updateTotalPrice()"><br><br>
    
    <label for="kid_quantity">Kid Ticket Quantity:</label>
    <input type="number" id="kid_quantity" name="ticket_kid_quantity" min="0" required onchange="generateTicketRows('kid'); updateTotalPrice()"><br><br>
    
    <!-- Container for dynamically generated kid ticket rows -->
    <div id="kid_tickets_container"></div>
    
    <!-- Display for overall price -->
    <div id="overall_price">Total Price: 0.00</div>
    
    <button type="submit" name="submit">Create Order</button>
</form>

<?php
// Include necessary scripts for database, barcode generation, and API client
require '/Applications/MAMP/htdocs/test2/kak/db.php';
require '/Applications/MAMP/htdocs/test2/kak/generate_barcode.php';
require '/Applications/MAMP/htdocs/test2/kak/api_client.php';

// Process form submission
if (isset($_POST['submit'])) {
    // Retrieve and store form inputs
    $event_id = $_POST['event_id'];
    $event_date = $_POST['event_date'];
    $ticket_adult_price = $_POST['ticket_adult_price'];
    $ticket_adult_quantity = $_POST['ticket_adult_quantity'];
    $ticket_kid_price = $_POST['ticket_kid_price'];
    $ticket_kid_quantity = $_POST['ticket_kid_quantity'];

    // Retrieve ticket types and prices from form
    $adult_ticket_types = $_POST['adult_ticket_type'] ?? [];
    $kid_ticket_types = $_POST['kid_ticket_type'] ?? [];
    $adult_ticket_prices = $_POST['adult_ticket_price'] ?? [];
    $kid_ticket_prices = $_POST['kid_ticket_price'] ?? [];

    // Call createOrder to process order creation
    createOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices);
}

// Function to create an order and manage barcode generation and order approval
function createOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices) {
    $barcodes = generateUniqueBarcodes($ticket_adult_quantity + $ticket_kid_quantity); // Generate barcodes
    $orderData = [
        'event_id' => $event_id,
        'event_date' => $event_date,
        'ticket_adult_price' => $ticket_adult_price,
        'ticket_adult_quantity' => $ticket_adult_quantity,
        'ticket_kid_price' => $ticket_kid_price,
        'ticket_kid_quantity' => $ticket_kid_quantity,
        'barcodes' => $barcodes
    ];

    // Send booking request via API
    $response = sendPostRequest('http://localhost:3000/book', $orderData);

    // Check if booking was successful
    if (isset($response['message']) && $response['message'] === 'order successfully booked') {
        // Approve booking if successful
        approveBooking($barcodes, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices, $ticket_adult_price, $ticket_kid_price);
    } else {
        handleBookingError($response);
    }
}

// Function to handle approval of booking
function approveBooking($barcodes, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices, $ticket_adult_price, $ticket_kid_price) {
    $approvalResponse = sendPostRequest('http://localhost:3000/approve', ['barcodes' => $barcodes]);

    // Check if approval was successful
    if (isset($approvalResponse['message']) && $approvalResponse['message'] === 'order successfully approved') {
        saveOrderAndTickets($barcodes, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices, $ticket_adult_price, $ticket_kid_price);
        displaySuccessMessage($barcodes); // Display success message if approved
    } else {
        handleApprovalError($approvalResponse);
    }
}

// Function to save the order and ticket details to the database
function saveOrderAndTickets($barcodes, $adult_ticket_types, $kid_ticket_types, $adult_ticket_prices, $kid_ticket_prices, $ticket_adult_price, $ticket_kid_price) {
    $total_price = calculateTotalPrice($adult_ticket_prices, $kid_ticket_prices); // Calculate total order price

    /* Placeholder for database saving logic, commented out */
    /* Code would save order in the `orders` table and tickets in `tickets` table */
}

// Function to display success message with barcodes
function displaySuccessMessage($barcodes) {
    echo "<h2>Order successfully saved in the database.</h2>";
    echo "<h3>Generated Barcodes:</h3><ul>";
    foreach ($barcodes as $barcode) {
        echo "<li class='barcode'>{$barcode}</li>";
    }
    echo "</ul>";
}

// Error handling functions for booking and approval
function handleBookingError($response) {
    if (isset($response['error'])) {
        echo "Error: " . $response['error'];
    } else {
        echo "Error: Unexpected response structure.";
    }
}

function handleApprovalError($response) {
    if (isset($response['error'])) {
        echo "Error: " . $response['error'];
    } else {
        echo "Error: Unexpected response structure.";
    }
}

// Function to calculate the total price based on adult and kid ticket prices
function calculateTotalPrice($adult_ticket_prices, $kid_ticket_prices) {
    $totalAdultPrice = array_sum($adult_ticket_prices);
    $totalKidPrice = array_sum($kid_ticket_prices);
    return $totalAdultPrice + $totalKidPrice;
}

// Function to generate a unique barcode for each ticket
function generateUniqueBarcodes($total) {
    return array_map(function() {
        return generateBarcode(); // Call external function to generate unique barcode
    }, range(1, $total));
}
?>
</body>
</html>
