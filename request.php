<?php
// Establish database connection
$servername = "";
$username = "";  
$password = "";  
$dbname = ""; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = $success = '';

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_GET['customer_id'];
    $description = trim($_POST['description']);
    $weight = $_POST['weight'];
    $pickup_address = trim($_POST['pickup_address']);
    $pickup_suburb = trim($_POST['pickup_suburb']);
    
    // Get pickup date and time from the form directly
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    
    $receiver_name = trim($_POST['receiver_name']);
    $delivery_address = trim($_POST['delivery_address']);
    $delivery_suburb = trim($_POST['delivery_suburb']);
    $delivery_state = $_POST['delivery_state'];

    $request_date = date('Y-m-d');

    // Validate inputs
    if (empty($description) || empty($pickup_address) || empty($pickup_suburb) || empty($receiver_name) ||
        empty($delivery_address) || empty($delivery_suburb)) {
        $error = "All fields are required.";
    } else {
        // **Validation for Pick-up Date and Time**
        
        // (c) Ensure the preferred pick-up date and time are at least 24 hours from now
        $pickup_datetime = strtotime($pickup_date . ' ' . $pickup_time);
        $current_datetime = strtotime('+24 hours'); // Current time + 24 hours

        if ($pickup_datetime < $current_datetime) {
            $error = "Pick-up date and time must be at least 24 hours from the current time.";
        }
        
        // (d) Ensure the preferred pick-up time is between 08:00 and 20:00
        $pickup_hour = date('H', strtotime($pickup_time)); // Get the hour part of the pick-up time
        
        if ($pickup_hour < 8 || $pickup_hour > 20) {
            $error = "Pick-up time must be between 08:00 and 20:00.";
        }

        // If there are no errors, proceed with the request
        if (empty($error)) {
            // Calculate the cost
            $cost = 20 + (($weight - 2) * 3); // Pricing rule

            // Insert the request into the requests table
            $sql = "INSERT INTO requests (customer_id, description, weight, pickup_address, pickup_suburb, pickup_date, pickup_time, receiver_name, delivery_address, delivery_suburb, delivery_state, request_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isisssssssss", $customer_id, $description, $weight, $pickup_address, $pickup_suburb, $pickup_date, $pickup_time, $receiver_name, $delivery_address, $delivery_suburb, $delivery_state, $request_date);

            if ($stmt->execute()) {
                $request_id = $stmt->insert_id;
                $success = "Thank you! Your request number is $request_id. The cost is \$$cost. We will pick-up the item at $pickup_time on $pickup_date.";

                // Fetch customer name and email from the database
                $sql_customer = "SELECT name, email FROM customers WHERE customer_id = ?";
                $stmt_customer = $conn->prepare($sql_customer);
                $stmt_customer->bind_param("i", $customer_id);
                $stmt_customer->execute();
                $result_customer = $stmt_customer->get_result();
                $customer = $result_customer->fetch_assoc();

                // Send confirmation email
                $to = $customer['email']; // The customer's email address
                $subject = "Shipping request with ShipOnline"; // The email subject
                $message = "Dear " . $customer['name'] . ",\n\nThank you for using ShipOnline! Your request number is $request_id. The cost is \$$cost. We will pick-up the item at $pickup_time on $pickup_date."; // The message body

                // Set the headers
                $headers = "From: 104691259@student.swin.edu.au" . "\r\n" .
                           "Reply-To: 104691259@student.swin.edu.au" . "\r\n" .
                           "X-Mailer: PHP/" . phpversion(); // Additional headers including Reply-To and X-Mailer

                // Send the email
                mail($to, $subject, $message, $headers);

            } else {
                $error = "There was a problem with the request. Please try again.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShipOnline Request</title>
    <style>
        body {
            background-color: #ffffcc;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        form {
            background-color: #ffff99;
            padding: 20px;
            border: 1px solid #ccc;
            display: inline-block;
            width: 400px;
            text-align: left;
        }

        input[type="text"], input[type="number"], input[type="date"], input[type="time"], select {
            width: 100%;
            margin: 10px 0;
            padding: 5px;
        }

        label {
            margin-top: 10px;
            display: inline-block;
        }

        .form-section {
            margin-bottom: 15px;
        }

        input[type="submit"] {
            padding: 5px 15px;
            margin: 10px;
            display: block;
            width: 100px;
            margin-left: auto;
            margin-right: auto;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

    <h1>ShipOnline Request Page</h1>

    <!-- Display error messages if any -->
    <?php if (!empty($error)) : ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <!-- Display success messages if any -->
    <?php if (!empty($success)) : ?>
        <p class="success"><?= $success; ?></p>
    <?php endif; ?>

    <!-- Request form -->
    <form method="POST" action="request.php?customer_id=<?= $_GET['customer_id']; ?>">

        <!-- Item Information -->
        <div class="form-section">
            <label><strong>Item Information:</strong></label><br>

            <label>Description:</label>
            <input type="text" name="description" required>

            <label>Weight:</label>
            <select name="weight" required>
                <?php for ($i = 2; $i <= 20; $i++): ?>
                    <option value="<?= $i; ?>"><?= $i; ?> kg</option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- Pick-up Information -->
        <div class="form-section">
            <label><strong>Pick-up Information:</strong></label><br>

            <label>Address:</label>
            <input type="text" name="pickup_address" required>

            <label>Suburb:</label>
            <input type="text" name="pickup_suburb" required>

            <label>Preferred Date:</label>
            <input type="date" name="pickup_date" required>

            <label>Preferred Time:</label>
            <input type="time" name="pickup_time" required>
        </div>

        <!-- Delivery Information -->
        <div class="form-section">
            <label><strong>Delivery Information:</strong></label><br>

            <label>Receiver Name:</label>
            <input type="text" name="receiver_name" required>

            <label>Address:</label>
            <input type="text" name="delivery_address" required>

            <label>Suburb:</label>
            <input type="text" name="delivery_suburb" required>

            <label>State:</label>
            <select name="delivery_state" required>
                <option value="NSW">New South Wales (NSW)</option>
                <option value="VIC">Victoria (VIC)</option>
                <option value="QLD">Queensland (QLD)</option>
                <option value="SA">South Australia (SA)</option>
                <option value="WA">Western Australia (WA)</option>
                <option value="TAS">Tasmania (TAS)</option>
                <option value="ACT">Australian Capital Territory (ACT)</option>
                <option value="NT">Northern Territory (NT)</option>
            </select>
        </div>

        <input type="submit" value="Request">
    </form>

    <!-- Link back to the home page -->
    <p><a href="shiponline.php">Home</a></p>

</body>
</html>