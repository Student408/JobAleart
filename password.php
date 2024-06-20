<?php
include 'db.php'; // Ensure db.php connects to the database using PDO

$username = 'admin';
$password = 'yourpassword'; // Replace with your desired password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admin (username, password_hash) VALUES (:username, :password_hash)";
$stmt = $conn->prepare($sql);
$stmt->execute([
    'username' => $username,
    'password_hash' => $password_hash
]);

echo "Admin user created successfully!";
?>
