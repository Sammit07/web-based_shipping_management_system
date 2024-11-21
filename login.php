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

// When the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = trim($_POST['customer_id']);
    $password = trim($_POST['password']);

    // Check if customer exists
    $sql = "SELECT * FROM customers WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
       // Verify password using md5 hash comparison
       if (md5($password) == $row['password']) {
            // Redirect to request page with customer number
            header("Location: request.php?customer_id=" . $customer_id);
            exit();
        } else {
            $error = "Invalid customer number or password.";
        }
    } else {
        $error = "Invalid customer number or password.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>ShipOnline Login</title>
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
    </style>
</head>
<body>

    <h1>ShipOnline System Login Page</h1>

    <!-- Display error messages if any -->
    <?php if (!empty($error)) : ?>
        <p class="error"><?= $error; ?></p>
    <?php endif; ?>

    <!-- Login form -->
    <form method="POST" action="login.php">
        <label>Customer Number:</label><br>
        <input type="text" name="customer_id" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br>

        <input type="submit" value="Log in">
    </form>

    <!-- Link back to the home page -->
    <p><a href="shiponline.php">Home</a></p>

</body>
</html>