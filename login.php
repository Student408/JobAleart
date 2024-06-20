<?php
// Start session
session_start();

// Include database connection
include 'db.php';

// Check if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirect to the admin page
    header("Location: admin.php");
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if username and password are submitted and not empty
    if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare the SQL statement
        $sql = "SELECT password_hash FROM admin WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and the password is correct
        if ($result && password_verify($password, $result['password_hash'])) {
            // Authentication successful
            session_regenerate_id(true);
            $_SESSION['loggedin'] = true;
            header("Location: admin.php");
            exit();
        } else {
            // Authentication failed
            $errorMessage = 'Invalid username or password!';
        }
    } else {
        $errorMessage = 'Please enter your username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" href="logo.svg" type="image/svg+xml">
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form label {
            display: block;
            margin-bottom: 10px;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 15px;
        }

        .login-form button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .login-form button[type="submit"]:hover {
            background-color: #0056B3;
        }

        .login-form .error-message {
            color: #ff0000;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Responsive Styles */
        @media (max-width: 480px) {
            body {
                height: auto;
                padding: 20px;
            }

            .login-form {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>Login</h2>
        <?php if (isset($errorMessage)) echo '<p class="error-message">' . $errorMessage . '</p>'; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>