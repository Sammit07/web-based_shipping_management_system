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

// Initialize variables for error messages and success messages
$error = $success = '';

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user inputs
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = trim($_POST['email']);
    $contact_phone = trim($_POST['contact_phone']);

    // Validate inputs
    if (empty($name) || empty($password) || empty($confirm_password) || empty($email) || empty($contact_phone)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if the email is unique
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email address is already registered.";
        } else {
            // Hash the password using md5 (alternative for older PHP versions)
            $hashed_password = md5($password);  // Use md5 for hashing

            // Insert new customer data into the database
            $sql = "INSERT INTO customers (name, password, email, contact_phone) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $name, $hashed_password, $email, $contact_phone);

            if ($stmt->execute()) {
                // Get the customer ID of the newly registered user
                $customer_id = $stmt->insert_id;
                $success = "Dear $name, you are successfully registered into ShipOnline, and your customer number is $customer_id.";
            } else {
                $error = "There was a problem registering the customer. Please try again.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShipOnline Registration</title>
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
        }

        input[type="text"], input[type="password"] {
            width: 200px;
            margin: 10px;
            padding: 5px;
        }

        input[type="submit"] {
            padding: 5px 15px;
            margin: 10px;
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

    <h1>ShipOnline System Registration Page</h1>

    <!-- Display error messages if any -->
    <?php if (!empty($error)) : ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <!-- Display success messages if any -->
    <?php if (!empty($success)) : ?>
        <p class="success"><?= $success; ?></p>
    <?php endif; ?>

    <!-- Registration form -->
    <form method="POST" action="register.php">
        <label>Name:</label><br>
        <input type="text" name="name" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirm_password" required><br>

        <label>Email Address:</label><br>
        <input type="text" name="email" required><br>

        <label>Contact Phone:</label><br>
        <input type="text" name="contact_phone" required><br>

        <input type="submit" value="Register">
    </form>

    <!-- Link back to the home page -->
    <p><a href="shiponline.php">Home</a></p>

</body>
</html>
