<?php
function connectToDB() {
    $host = 'localhost';
    $user = 'root';        // default XAMPP username
    $pass = '';            // default XAMPP password is empty
    $db   = 'rollin_ramblers_db';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
