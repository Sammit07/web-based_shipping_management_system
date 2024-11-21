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

$error = '';
$results = '';
$total_requests = 0;
$total_revenue_or_weight = 0.0;
$date_selected = '';
$type_selected = '';
$selected_date = '';

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $day = $_POST['day'];
    $month = $_POST['month'];
    $year = $_POST['year'];
    $date_selected = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
    $selected_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
    
    $type_selected = $_POST['type']; // 'request_date' or 'pickup_date'

    if ($type_selected == 'request_date') {
        // Retrieve requests based on the request date
        $sql = "SELECT customer_id, request_id, description, weight, pickup_suburb, pickup_date, delivery_suburb, delivery_state 
                FROM requests WHERE request_date = ?";
        $table_title = "Requests on " . $selected_date; // Dynamic title for request date
    } else {
        // Retrieve requests based on the pick-up date
        $sql = "SELECT c.customer_id, c.name, c.contact_phone, r.request_id, r.description, r.weight, r.pickup_suburb, r.pickup_date, r.pickup_time, r.delivery_suburb, r.delivery_state 
                FROM requests r
                JOIN customers c ON r.customer_id = c.customer_id
                WHERE r.pickup_date = ?
                ORDER BY r.pickup_suburb, r.delivery_state, r.delivery_suburb";
        $table_title = "Pick-up Details for " . $selected_date; // Dynamic title for pick-up date
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date_selected);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Display the dynamic title above the table

            // Initialize table headers
            $results = '<h2>' . $table_title . '</h2>' . '<table border="1" cellpadding="10">';
            if ($type_selected == 'request_date') {
                $results .= '<tr>
                                <th>Customer Number</th>
                                <th>Request Number</th>
                                <th>Description</th>
                                <th>Weight</th>
                                <th>Pick-up Suburb</th>
                                <th>Preferred Pick-up Date</th>
                                <th>Delivery Suburb</th>
                                <th>Delivery State</th>
                             </tr>';
            } else {
                $results .= '<tr>
                                <th>Customer ID</th>
                                <th>Customer Name</th>
                                <th>Contact Phone</th>
                                <th>Request ID</th>
                                <th>Description</th>
                                <th>Weight</th>
                                <th>Pick-up Suburb</th>
                                <th>Pick-up Date</th>
                                <th>Pick-up Time</th>
                                <th>Delivery Suburb</th>
                                <th>Delivery State</th>
                             </tr>';
            }

            // Loop through the result and build rows
            while ($row = $result->fetch_assoc()) {
                if ($type_selected == 'request_date') {
                    $results .= '<tr>
                                    <td>' . $row['customer_id'] . '</td>
                                    <td>' . $row['request_id'] . '</td>
                                    <td>' . $row['description'] . '</td>
                                    <td>' . $row['weight'] . ' kg</td>
                                    <td>' . $row['pickup_suburb'] . '</td>
                                    <td>' . $row['pickup_date'] . '</td>
                                    <td>' . $row['delivery_suburb'] . '</td>
                                    <td>' . $row['delivery_state'] . '</td>
                                </tr>';
                    // For request date, calculate total revenue
                    $total_revenue_or_weight += 20 + (($row['weight'] - 2) * 3); // Assuming $20 base and $3 per kg above 2 kg
                } else {
                    $results .= '<tr>
                                    <td>' . $row['customer_id'] . '</td>
                                    <td>' . $row['name'] . '</td>
                                    <td>' . $row['contact_phone'] . '</td>
                                    <td>' . $row['request_id'] . '</td>
                                    <td>' . $row['description'] . '</td>
                                    <td>' . $row['weight'] . ' kg</td>
                                    <td>' . $row['pickup_suburb'] . '</td>
                                    <td>' . $row['pickup_date'] . '</td>
                                    <td>' . $row['pickup_time'] . '</td>
                                    <td>' . $row['delivery_suburb'] . '</td>
                                    <td>' . $row['delivery_state'] . '</td>
                                </tr>';
                    // For pick-up date, calculate total weight
                    $total_revenue_or_weight += $row['weight'];
                }

                $total_requests++;
            }

            $results .= '</table>';

            // Add totals below the table
            if ($type_selected == 'request_date') {
                $results .= '<p>Total number of requests: ' . $total_requests . '</p>';
                $results .= '<p>Total revenue: $' . number_format($total_revenue_or_weight, 2) . '</p>';
            } else {
                $results .= '<p>Total number of requests: ' . $total_requests . '</p>';
                $results .= '<p>Total weight: ' . number_format($total_revenue_or_weight, 2) . ' kg</p>';
            }
        } else {
            $error = "No requests found for the selected date.";
        }
    } else {
        $error = "Error executing query.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
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
            width: 300px;
            margin: 20px auto;
            text-align: center;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

        .home-link {
            margin-top: 20px;
            text-align: center;
        }

        a {
            text-decoration: none;
            color: blue;
        }

    </style>
</head>
<body>

<h1>ShipOnline System Administration Page</h1>

<!-- Display error messages if any -->
<?php if (!empty($error)) : ?>
    <p class="error"><?= $error; ?></p>
<?php endif; ?>

<!-- Form to input date and type -->
<form method="POST" action="admin.php">
    <label>Date for Retrieve:</label><br>
    <select name="day" required>
        <?php for ($d = 1; $d <= 31; $d++): ?>
            <option value="<?= $d; ?>"><?= $d; ?></option>
        <?php endfor; ?>
    </select>
    <select name="month" required>
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m; ?>"><?= $m; ?></option>
        <?php endfor; ?>
    </select>
    <select name="year" required>
        <?php for ($y = 2020; $y <= 2030; $y++): ?> 
            <option value="<?= $y; ?>"><?= $y; ?></option>
        <?php endfor; ?>
    </select><br><br>

    <label>Select Date Item for Retrieve:</label><br>
    <input type="radio" name="type" value="request_date" required> Request Date
    <input type="radio" name="type" value="pickup_date" required> Pick-up Date<br><br>

    <input type="submit" value="Show">
</form>

<!-- Display results -->
<?php if (!empty($results)) : ?>
    <?= $results; ?>
<?php endif; ?>

<!-- Link back to the home page -->
<div class="home-link" style="text-align: center; margin-top: 20px;">
    <p><a href="shiponline.php">Home</a></p>
</div>

</body>
</html>
